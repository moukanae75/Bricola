package application.usecases;

import domain.entities.User;
import domain.repositories.IUser;
import domain.repositories.IServiceRequest;

import java.time.LocalDate;
import java.time.temporal.ChronoUnit;
import java.util.*;
import java.util.stream.Collectors;


public class ChurnRiskPredictor {

    private final IUser           userRepo;
    private final IServiceRequest requestRepo;

    private static final double LAMBDA_HIGH_ACTIVITY   = 0.015;
    private static final double LAMBDA_MEDIUM_ACTIVITY = 0.030;
    private static final double LAMBDA_LOW_ACTIVITY    = 0.060;

    private static final double RISK_RECENT_LOW_RATING  = 0.030;
    private static final double RISK_NO_RECENT_REQUEST  = 0.020;
    private static final double RISK_SUSPENDED_RECENTLY = 0.050;

    private static final double CHURN_THRESHOLD = 0.20;

    public ChurnRiskPredictor(IUser userRepo, IServiceRequest requestRepo) {
        this.userRepo    = userRepo;
        this.requestRepo = requestRepo;
    }

    public List<ChurnRisk> execute(int topN) {
        List<User> activeUsers = userRepo.findActive();
        System.out.println("Active users = " + activeUsers.size());
        LocalDate  today       = LocalDate.now();

        return activeUsers.stream()
            .map(user -> computeRisk(user, today))
            .sorted(Comparator.comparingDouble(ChurnRisk::getChurnProbability).reversed())
            .limit(topN)
            .collect(Collectors.toList());
    }

    public ChurnRisk computeRisk(User user, LocalDate today) {
        long t = daysSinceLastActivity(user, today);

        double lambda = baseLambda(user);
        lambda += riskAdjustments(user, today);

        double survivalProb = Math.exp(-lambda * t);
        double churnProb    = 1.0 - survivalProb;

        long daysUntilLikelyGone = (long) (Math.log(2.0) / lambda);
        long daysUntilCritical = Math.max(0,
            (long) (-Math.log(CHURN_THRESHOLD) / lambda) - t);

        String recommendation = buildRecommendation(churnProb, daysUntilCritical, user);

        return new ChurnRisk(user, churnProb, survivalProb, t,
                             daysUntilLikelyGone, daysUntilCritical, recommendation);
    }

    private long daysSinceLastActivity(User user, LocalDate today) {
        LocalDate lastActive = requestRepo.lastActivityDate(user.getId());
        if (lastActive == null) lastActive = today.minusDays(90);
        return Math.max(1, ChronoUnit.DAYS.between(lastActive, today));
    }

    private double baseLambda(User user) {
        int totalRequests = requestRepo.countByUser(user.getId());
        if (totalRequests >= 10) return LAMBDA_HIGH_ACTIVITY;
        if (totalRequests >= 3)  return LAMBDA_MEDIUM_ACTIVITY;
        return LAMBDA_LOW_ACTIVITY;
    }

    private double riskAdjustments(User user, LocalDate today) {
        double extra = 0.0;
        if (requestRepo.hadLowRatingRecently(user.getId(), 30)) {
            extra += RISK_RECENT_LOW_RATING;
        }
        LocalDate lastRequest = requestRepo.lastRequestDate(user.getId());
        if (lastRequest == null || ChronoUnit.DAYS.between(lastRequest, today) > 30) {
            extra += RISK_NO_RECENT_REQUEST;
        }
        if (user.wasSuspendedWithinDays(60)) {
            extra += RISK_SUSPENDED_RECENTLY;
        }
        return extra;
    }

    private String buildRecommendation(double churnProb, long daysLeft, User user) {
        if (churnProb > 0.80) return "CRITICAL: send re-engagement offer within 24h";
        if (churnProb > 0.60) return String.format("HIGH: trigger email campaign (%d days left)", daysLeft);
        if (churnProb > 0.40) return "MEDIUM: show promotional banner on next login";
        return "LOW: monitor passively";
    }

    public static class ChurnRisk {
        public final User   user;
        public final double churnProbability;
        public final double survivalProbability;
        public final long   daysSinceLastActive;
        public final long   daysUntilLikelyGone;
        public final long   daysUntilCritical;
        public final String recommendation;

        public ChurnRisk(User user, double churnProb, double survivalProb,
                         long daysSinceLastActive, long daysUntilLikelyGone,
                         long daysUntilCritical, String recommendation) {
            this.user                = user;
            this.churnProbability    = churnProb;
            this.survivalProbability = survivalProb;
            this.daysSinceLastActive = daysSinceLastActive;
            this.daysUntilLikelyGone = daysUntilLikelyGone;
            this.daysUntilCritical   = daysUntilCritical;
            this.recommendation      = recommendation;
        }

        public double getChurnProbability() { return churnProbability; }

        @Override
        public String toString() {
            return String.format(
                "User#%s | Churn=%.1f%% | Survival=%.1f%% | Inactive=%d days | Action: %s",
                user.getId(), churnProbability * 100, survivalProbability * 100,
                daysSinceLastActive, recommendation);   
        }
    }
}
