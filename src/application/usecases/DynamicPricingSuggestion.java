package application.usecases;

import domain.entities.Artisan;
import domain.repositories.IServiceRequest;


public class DynamicPricingSuggestion {

    private final IServiceRequest requestRepo;
    private static final double DEFAULT_ELASTICITY = 0.8;

    public DynamicPricingSuggestion(IServiceRequest requestRepo) {
        this.requestRepo = requestRepo;
    }

    public PricingAdvice execute(Artisan artisan, int categoryId, double clientBudget) {
        MarketStats stats = computeMarketStats(categoryId);

        double D0         = stats.avgMonthlyBookings * (1 + DEFAULT_ELASTICITY * stats.avgPrice);
        double elasticity = DEFAULT_ELASTICITY;

        double pOptimal = D0 / (2.0 * elasticity);

        double pMin = stats.avgPrice * 0.7;
        double pMax = stats.avgPrice * 1.5;
        if (clientBudget > 0) pMax = Math.min(pMax, clientBudget);

        double ratingPremium = computeRatingPremium(artisan);
        pMin *= ratingPremium;
        pMax *= ratingPremium;

        double suggestedPrice = Math.max(pMin, Math.min(pMax, pOptimal));

        double expectedDemand   = Math.max(0, D0 - elasticity * suggestedPrice);
        double projectedRevenue = suggestedPrice * expectedDemand;

        String explanation = buildExplanation(pOptimal, pMin, pMax, suggestedPrice, ratingPremium);

        return new PricingAdvice(suggestedPrice, pMin, pMax, expectedDemand, projectedRevenue, explanation);
    }

    private MarketStats computeMarketStats(int categoryId) {
        double avgPrice           = requestRepo.averagePriceByCategory(categoryId);
        double avgMonthlyBookings = requestRepo.avgMonthlyBookingsByCategory(categoryId);
        if (avgPrice <= 0)           avgPrice = 100.0;
        if (avgMonthlyBookings <= 0) avgMonthlyBookings = 10.0;
        return new MarketStats(avgPrice, avgMonthlyBookings);
    }

    private double computeRatingPremium(Artisan artisan) {
        double rating = artisan.getAverageRating();
        if (rating < 3.0) return 0.95;
        return 1.0 + 0.04 * (rating - 3.0);
    }

    private String buildExplanation(double pOpt, double pMin, double pMax,
                                     double suggested, double premium) {
        return String.format(
            "LP optimal=%.2f | feasible=[%.2f, %.2f] | rating premium=x%.2f => suggested=%.2f",
            pOpt, pMin, pMax, premium, suggested);
    }

    private static class MarketStats {
        final double avgPrice;
        final double avgMonthlyBookings;
        MarketStats(double avgPrice, double avgMonthlyBookings) {
            this.avgPrice = avgPrice;
            this.avgMonthlyBookings = avgMonthlyBookings;
        }
    }

    public static class PricingAdvice {
        public final double suggestedPrice;
        public final double minPrice;
        public final double maxPrice;
        public final double expectedDemand;
        public final double projectedRevenue;
        public final String explanation;

        public PricingAdvice(double suggestedPrice, double minPrice, double maxPrice,
                             double expectedDemand, double projectedRevenue, String explanation) {
            this.suggestedPrice   = suggestedPrice;
            this.minPrice         = minPrice;
            this.maxPrice         = maxPrice;
            this.expectedDemand   = expectedDemand;
            this.projectedRevenue = projectedRevenue;
            this.explanation      = explanation;
        }

        @Override
        public String toString() {
            return String.format(
                "Suggested: %.2f | Range: [%.2f, %.2f] | Expected demand: %.1f | Revenue: %.2f",
                suggestedPrice, minPrice, maxPrice, expectedDemand, projectedRevenue);
        }
    }
}
