package application.usecases;

import domain.entities.Artisan;
import domain.entities.ServiceRequest;
import domain.enumeration.CategoryComplexityTier;
import domain.repositories.IArtisanReader;
import domain.repositories.IServiceRequest;

import java.util.*;
import java.util.stream.Collectors;

/**
 * USE CASE: WorkloadBalancer
 *
 * Mathematical foundation: Operations Research — Online Bin-Packing (Best Fit Decreasing)
 *
 * Algorithm: FFD variant — sort requests by weight desc, assign each to the artisan
 * with the most remaining capacity that can still fit the request.
 */
public class WorkloadBalancer {

    private final IArtisanReader  artisanRepo;
    private final IServiceRequest requestRepo;

    private static final int DEFAULT_MAX_CONCURRENT = 3;

    public WorkloadBalancer(IArtisanReader artisanRepo, IServiceRequest requestRepo) {
        this.artisanRepo = artisanRepo;
        this.requestRepo = requestRepo;
    }

    public BalancingResult execute() {
        List<ServiceRequest> pending   = requestRepo.findPending();
        List<Artisan>        available = artisanRepo.findAvailable();

        if (available.isEmpty()) {
            return new BalancingResult(Collections.emptyList(), pending, Double.MAX_VALUE);
        }

        // Step 1: Sort by weight descending (heaviest first)
        pending.sort(Comparator.comparingDouble(this::estimateWeight).reversed());

        // Step 2: Initialize bins
        List<ArtisanBin> bins = available.stream()
            .map(a -> new ArtisanBin(a, getCapacity(a)))
            .collect(Collectors.toList());

        List<Assignment>     assignments = new ArrayList<>();
        List<ServiceRequest> overflow    = new ArrayList<>();

        // Step 3: Best Fit Decreasing assignment
        for (ServiceRequest req : pending) {
            double weight = estimateWeight(req);

            ArtisanBin best = null;
            for (ArtisanBin bin : bins) {
                double remaining = bin.remainingCapacity();
                if (remaining >= weight) {
                    if (best == null || remaining > best.remainingCapacity()) {
                        best = bin;
                    }
                }
            }

            if (best != null) {
                best.assign(req, weight);
                assignments.add(new Assignment(req, best.artisan));
            } else {
                overflow.add(req);
            }
        }

        // Step 4: Compute imbalance
        double imbalance = computeImbalance(bins);
        return new BalancingResult(assignments, overflow, imbalance);
    }

    /**
     * Estimates request complexity as a weight in (0, 1].
     * Uses CategoryComplexityTier; urgent requests are weighted higher.
     */
    private double estimateWeight(ServiceRequest req) {
        double base;
        // Fixed: getCategoryComplexityTier() now returns the proper enum type
        CategoryComplexityTier tier = req.getCategoryComplexityTier();
        if (tier == CategoryComplexityTier.HIGH) {
            base = 1.0;
        } else if (tier == CategoryComplexityTier.MEDIUM) {
            base = 0.6;
        } else if (tier == CategoryComplexityTier.LOW) {
            base = 0.3;
        } else {
            base = 0.5;
        }
        return req.isUrgent() ? Math.min(1.0, base * 1.4) : base;
    }

    private int getCapacity(Artisan artisan) {
        return artisan.getMaxConcurrentJobs() > 0
            ? artisan.getMaxConcurrentJobs()
            : DEFAULT_MAX_CONCURRENT;
    }

    private double computeImbalance(List<ArtisanBin> bins) {
        if (bins.isEmpty()) return 0.0;
        double[] loads = bins.stream()
            .mapToDouble(b -> b.currentLoad / (double) b.capacity)
            .toArray();
        double mean = Arrays.stream(loads).average().orElse(0);
        double variance = Arrays.stream(loads)
            .map(l -> (l - mean) * (l - mean))
            .average()
            .orElse(0);
        return Math.sqrt(variance);
    }

    // --- Inner types ---

    private static class ArtisanBin {
        final Artisan artisan;
        final int     capacity;
        double        currentLoad = 0.0;
        List<ServiceRequest> assignedRequests = new ArrayList<>();

        ArtisanBin(Artisan artisan, int capacity) {
            this.artisan  = artisan;
            this.capacity = capacity;
        }

        double remainingCapacity() { return capacity - currentLoad; }

        void assign(ServiceRequest req, double weight) {
            currentLoad += weight;
            assignedRequests.add(req);
        }
    }

    public static class Assignment {
        public final ServiceRequest request;
        public final Artisan        artisan;

        public Assignment(ServiceRequest request, Artisan artisan) {
            this.request = request;
            this.artisan = artisan;
        }

        @Override
        public String toString() {
            return String.format("Request#%d -> Artisan#%s",
                request.getId(), artisan.getId());
        }
    }

    public static class BalancingResult {
        public final List<Assignment>     assignments;
        public final List<ServiceRequest> overflow;
        public final double               imbalanceScore;

        public BalancingResult(List<Assignment> assignments,
                               List<ServiceRequest> overflow,
                               double imbalanceScore) {
            this.assignments    = assignments;
            this.overflow       = overflow;
            this.imbalanceScore = imbalanceScore;
        }

        @Override
        public String toString() {
            return String.format(
                "Assigned: %d | Overflow: %d | Imbalance (std): %.3f",
                assignments.size(), overflow.size(), imbalanceScore);
        }
    }
}
