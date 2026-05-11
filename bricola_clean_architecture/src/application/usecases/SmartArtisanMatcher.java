package application.usecases;

import domain.entities.Artisan;
import domain.entities.ServiceRequest;
import domain.repositories.IArtisanReader;
import domain.repositories.IEvaluation;

import java.util.*;

/**
 * USE CASE: SmartArtisanMatcher
 *
 * Mathematical foundation: Bayesian Probability + Weighted Scoring
 *
 * For each candidate artisan, computes a posterior probability score P(good_match | evidence)
 * using multiple independent signals as priors:
 *
 *   score(a) = w1 * norm(avgRating)
 *            + w2 * norm(responseRate)
 *            + w3 * norm(categoryExperience)
 *            + w4 * proximityScore(a, request)
 *
 * All signals are normalized to [0,1] before weighting.
 * Final scores are softmax-normalized so they sum to 1.0 — interpretable as probabilities.
 *
 * Complexity: O(n) per call where n = number of candidate artisans
 */
public class SmartArtisanMatcher {

    // Fixed: use IArtisanReader (has findVerifiedByCategory), not IArtisanWriter
    private final IArtisanReader artisanRepo;
    private final IEvaluation evalRepo;

    // Bayesian signal weights (must sum to 1.0)
    private static final double W_RATING     = 0.35;
    private static final double W_RESPONSE   = 0.25;
    private static final double W_EXPERIENCE = 0.25;
    private static final double W_PROXIMITY  = 0.15;

    public SmartArtisanMatcher(IArtisanReader artisanRepo, IEvaluation evalRepo) {
        this.artisanRepo = artisanRepo;
        this.evalRepo    = evalRepo;
    }

    /**
     * Returns artisans ranked by match probability for the given request.
     *
     * @param request  the service request to match
     * @param topK     how many top candidates to return
     * @return list of ArtisanMatch pairs sorted descending by confidence
     */
    public List<ArtisanMatch> execute(ServiceRequest request, int topK) {
        List<Artisan> candidates = artisanRepo.findVerifiedByCategory(request.getCategoryId());
        if (candidates.isEmpty()) return Collections.emptyList();

        // Step 1: compute raw signal vectors
        double[] rawScores = new double[candidates.size()];
        for (int i = 0; i < candidates.size(); i++) {
            rawScores[i] = computeRawScore(candidates.get(i), request);
        }

        // Step 2: softmax normalization -> probability distribution
        double[] probabilities = softmax(rawScores);

        // Step 3: build result list and sort by probability descending
        List<ArtisanMatch> matches = new ArrayList<>();
        for (int i = 0; i < candidates.size(); i++) {
            matches.add(new ArtisanMatch(candidates.get(i), probabilities[i]));
        }
        matches.sort((a, b) -> Double.compare(b.confidence, a.confidence));

        return matches.subList(0, Math.min(topK, matches.size()));
    }

    // --- Internal math ---

    private double computeRawScore(Artisan artisan, ServiceRequest request) {
        double ratingSignal     = normalize(artisan.getAverageRating(), 0.0, 5.0);
        double responseSignal   = normalize(artisan.getResponseRate(), 0.0, 1.0);
        double experienceSignal = normalize(
            evalRepo.countByArtisanAndCategory(artisan.getId(), request.getCategoryId()),
            0.0, 50.0
        );
        double proximitySignal  = computeProximityScore(artisan, request);

        return W_RATING     * ratingSignal
             + W_RESPONSE   * responseSignal
             + W_EXPERIENCE * experienceSignal
             + W_PROXIMITY  * proximitySignal;
    }

    /**
     * Proximity score: decays exponentially with distance.
     * P(proximity) = e^(-lambda * distance_km)
     * lambda = 0.1 means ~50% score at 7 km
     */
    private double computeProximityScore(Artisan artisan, ServiceRequest request) {
        double distanceKm = artisan.distanceTo(request.getClientLocation());
        return Math.exp(-0.1 * distanceKm);
    }

    /** Min-max normalization to [0, 1] */
    private double normalize(double value, double min, double max) {
        if (max == min) return 0.0;
        return Math.max(0.0, Math.min(1.0, (value - min) / (max - min)));
    }

    /**
     * Softmax: converts raw scores to a probability distribution.
     * softmax(x_i) = e^x_i / sum(e^x_j)
     * Uses max-subtraction trick for numerical stability.
     */
    private double[] softmax(double[] scores) {
        double max = Arrays.stream(scores).max().orElse(0.0);
        double[] exps = new double[scores.length];
        double sum = 0.0;
        for (int i = 0; i < scores.length; i++) {
            exps[i] = Math.exp(scores[i] - max);
            sum += exps[i];
        }
        for (int i = 0; i < exps.length; i++) exps[i] /= sum;
        return exps;
    }

    // --- Result DTO ---

    public static class ArtisanMatch {
        public final Artisan artisan;
        public final double confidence; // probability in [0,1]

        public ArtisanMatch(Artisan artisan, double confidence) {
            this.artisan    = artisan;
            this.confidence = confidence;
        }

        @Override
        public String toString() {
            return String.format("Artisan[%s] -> confidence=%.2f%%",
                artisan.getId(), confidence * 100);
        }
    }
}
