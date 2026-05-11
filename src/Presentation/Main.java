/*package Presentation ; 

import application.usecases.*;
import domain.entities.*;
import domain.enumeration.CategoryComplexityTier;
import domain.repositories.*;

import java.time.LocalDate;
import java.util.*;

*
 * Main — tests all application layer use cases using in-memory stub repositories.
 * No database connection required to run this test.
 *
public class Main {

    public static void main(String[] args) {
        System.out.println("========================================");
        System.out.println("  BRICOLA — Application Layer Tests");
        System.out.println("========================================\n");

        testVerifyArtisan();
        testApproveCategory();
        testRejectCategory();
        testGetServiceStatus();
        testComputeAvgRating();
        testSuspendUser();
        testSmartArtisanMatcher();
        testDynamicPricingSuggestion();
        testChurnRiskPredictor();
        testWorkloadBalancer();
        testAttachMedia();

        System.out.println("\n========================================");
        System.out.println("  All tests completed.");
        System.out.println("========================================");
    }

    // -------------------------------------------------------------------------
    // 1. VerifyArtisan
    // -------------------------------------------------------------------------
    static void testVerifyArtisan() {
        System.out.println("--- VerifyArtisan ---");
        Artisan artisan = new Artisan(1, "Youssef", "y@m.com", "hash", "060",
                                      "bio", "Fes", 0f, false, null, new Date());
        StubArtisanRepo repo = new StubArtisanRepo(artisan);

        VerifyArtisan uc = new VerifyArtisan(repo, repo);
        boolean result = uc.execute(1);
        System.out.println("Verify result: " + result);
        System.out.println("isVerified: " + artisan.isVerified());

        // Already verified → should throw
        try {
            uc.execute(1);
        } catch (IllegalStateException e) {
            System.out.println("Expected exception: " + e.getMessage());
        }
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 2. ApproveCategory
    // -------------------------------------------------------------------------
    static void testApproveCategory() {
        System.out.println("--- ApproveCategory ---");
        Category cat = new Category(1, "Plomberie", false, true);
        StubCategoryRepo repo = new StubCategoryRepo(cat);

        ApproveCategory uc = new ApproveCategory(repo, repo);
        uc.execute(1);
        System.out.println("isApproved: " + cat.getIsApproved());
        System.out.println("isPending : " + cat.getIsPending());
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 3. RejectCategory
    // -------------------------------------------------------------------------
    static void testRejectCategory() {
        System.out.println("--- RejectCategory ---");
        Category cat = new Category(2, "Peinture", false, true);
        StubCategoryRepo repo = new StubCategoryRepo(cat);

        RejectCategory uc = new RejectCategory(repo, repo);
        uc.execute(2);
        System.out.println("isApproved: " + cat.getIsApproved());
        System.out.println("isPending : " + cat.getIsPending());
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 4. GetServiceStatus
    // -------------------------------------------------------------------------
    static void testGetServiceStatus() {
        System.out.println("--- GetServiceStatus ---");
        ServiceRequest req = new ServiceRequest(10, "Fix sink", "leak", ServiceRequest.ServiceStatus.PENDING,
                                                "Fes", new Date(), null, 1, null, 1);
        StubServiceRequestRepo repo = new StubServiceRequestRepo(req);

        GetServiceStatus uc = new GetServiceStatus(repo);
        ServiceRequest.ServiceStatus status = uc.execute(10);
        System.out.println("Status: " + status);
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 5. ComputeArtisanAvgRating
    // -------------------------------------------------------------------------
    static void testComputeAvgRating() {
        System.out.println("--- ComputeArtisanAvgRating ---");
        Artisan artisan = new Artisan(1, "Youssef", "y@m.com", "hash", "060",
                                      "bio", "Fes", 0f, true, null, new Date());
        StubArtisanRepo artisanRepo = new StubArtisanRepo(artisan);

        List<Evaluation> evals = Arrays.asList(
            new Evaluation(1, 4, "Good", new Date(), true, 1, 1),
            new Evaluation(2, 5, "Excellent", new Date(), true, 2, 2),
            new Evaluation(3, 3, "OK", new Date(), true, 3, 3)
        );
        StubEvaluationRepo evalRepo = new StubEvaluationRepo(evals);

        ComputeArtisanAvgRating uc = new ComputeArtisanAvgRating(artisanRepo, artisanRepo, evalRepo);
        Float avg = uc.execute(1);
        System.out.printf("Average rating: %.2f%n", avg);
        System.out.printf("Artisan rating updated to: %.2f%n", artisan.getAverageRating());
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 6. SuspendUser
    // -------------------------------------------------------------------------
    static void testSuspendUser() {
        System.out.println("--- SuspendUser ---");
        Client client = new Client(5, "Amine", "amine@m.com", "hash", "061",
                                   "123 Rue", "Fes", null, new Date());
        StubUserRepo repo = new StubUserRepo(client);

        SuspendUser uc = new SuspendUser(repo);
        uc.execute(5);
        System.out.println("isSuspended: " + client.isSuspended());
        System.out.println("wasSuspendedWithinDays(7): " + client.wasSuspendedWithinDays(7));
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 7. SmartArtisanMatcher
    // -------------------------------------------------------------------------
    static void testSmartArtisanMatcher() {
        System.out.println("--- SmartArtisanMatcher ---");
        Artisan a1 = new Artisan(1, "Ali",    "ali@m.com",    "hash", "060", "bio", "Fes", 4.5f, true, null, new Date());
        Artisan a2 = new Artisan(2, "Hassan", "hassan@m.com", "hash", "061", "bio", "Fes", 3.8f, true, null, new Date());
        a1.setResponseRate(0.9);
        a2.setResponseRate(0.6);

        StubArtisanRepo artisanRepo = new StubArtisanRepo(a1, a2);
        StubEvaluationRepo evalRepo = new StubEvaluationRepo(Collections.emptyList());
        evalRepo.setCountResult(5.0);

        ServiceRequest req = new ServiceRequest(1, "Fix electricity", "desc",
            ServiceRequest.ServiceStatus.PENDING, "Fes", new Date(), null, 1, null, 1);
        req.setClientLocation(new double[]{34.03, -5.00});

        SmartArtisanMatcher uc = new SmartArtisanMatcher(artisanRepo, evalRepo);
        List<SmartArtisanMatcher.ArtisanMatch> matches = uc.execute(req, 3);
        matches.forEach(m -> System.out.println(m));
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 8. DynamicPricingSuggestion
    // -------------------------------------------------------------------------
    static void testDynamicPricingSuggestion() {
        System.out.println("--- DynamicPricingSuggestion ---");
        Artisan artisan = new Artisan(1, "Youssef", "y@m.com", "hash", "060",
                                      "bio", "Fes", 4.2f, true, null, new Date());
        StubServiceRequestRepo repo = new StubServiceRequestRepo(null);
        repo.setAvgPrice(150.0);
        repo.setAvgBookings(20.0);

        DynamicPricingSuggestion uc = new DynamicPricingSuggestion(repo);
        DynamicPricingSuggestion.PricingAdvice advice = uc.execute(artisan, 1, 200.0);
        System.out.println(advice);
        System.out.println("Explanation: " + advice.explanation);
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 9. ChurnRiskPredictor
    // -------------------------------------------------------------------------
    static void testChurnRiskPredictor() {
        System.out.println("--- ChurnRiskPredictor ---");
        Client c1 = new Client(1, "Omar", "omar@m.com", "hash", "060", "addr", "Fes", null, new Date());
        Client c2 = new Client(2, "Sara", "sara@m.com", "hash", "061", "addr", "Fes", null, new Date());

        StubUserRepo userRepo = new StubUserRepo(c1, c2);
        StubServiceRequestRepo requestRepo = new StubServiceRequestRepo(null);
        requestRepo.setLastActivity(LocalDate.now().minusDays(45));
        requestRepo.setCountByUser(2);

        ChurnRiskPredictor uc = new ChurnRiskPredictor(userRepo, requestRepo);
        List<ChurnRiskPredictor.ChurnRisk> risks = uc.execute(5);
        risks.forEach(r -> System.out.println(r));
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 10. WorkloadBalancer
    // -------------------------------------------------------------------------
    static void testWorkloadBalancer() {
        System.out.println("--- WorkloadBalancer ---");
        Artisan a1 = new Artisan(1, "Ali",    "ali@m.com",    "hash", "060", "bio", "Fes", 4f, true, null, new Date());
        Artisan a2 = new Artisan(2, "Hassan", "hassan@m.com", "hash", "061", "bio", "Fes", 3f, true, null, new Date());
        a1.setMaxConcurrentJobs(3);
        a2.setMaxConcurrentJobs(2);

        ServiceRequest r1 = new ServiceRequest(1, "Plumbing",     "desc", ServiceRequest.ServiceStatus.PENDING, "Fes", new Date(), null, 1, null, 1);
        ServiceRequest r2 = new ServiceRequest(2, "Electrician",  "desc", ServiceRequest.ServiceStatus.PENDING, "Fes", new Date(), null, 2, null, 2);
        ServiceRequest r3 = new ServiceRequest(3, "Painting",     "desc", ServiceRequest.ServiceStatus.PENDING, "Fes", new Date(), null, 3, null, 1);
        r1.setCategoryComplexityTier(CategoryComplexityTier.HIGH);
        r2.setCategoryComplexityTier(CategoryComplexityTier.LOW);
        r3.setCategoryComplexityTier(CategoryComplexityTier.MEDIUM);

        StubArtisanRepo artisanRepo = new StubArtisanRepo(a1, a2);
        StubServiceRequestRepo requestRepo = new StubServiceRequestRepo(r1);
        requestRepo.setPending(Arrays.asList(r1, r2, r3));

        WorkloadBalancer uc = new WorkloadBalancer(artisanRepo, requestRepo);
        WorkloadBalancer.BalancingResult result = uc.execute();
        System.out.println(result);
        result.assignments.forEach(a -> System.out.println("  " + a));
        if (!result.overflow.isEmpty()) {
            System.out.println("  Overflow: " + result.overflow.size() + " request(s)");
        }
        System.out.println();
    }

    // -------------------------------------------------------------------------
    // 11. AttachMediaToRequest
    // -------------------------------------------------------------------------
    static void testAttachMedia() {
        System.out.println("--- AttachMediaToRequest ---");
        ServiceRequest req = new ServiceRequest(1, "Fix sink", "leak",
            ServiceRequest.ServiceStatus.PENDING, "Fes", new Date(), null, 1, null, 1);
        StubServiceRequestRepo repo = new StubServiceRequestRepo(req);

        Media media = new Media(1, "http://cdn/photo.jpg", Media.MediaType.IMAGE, new Date(), 1);

        AttachMediaToRequest uc = new AttachMediaToRequest(repo);
        uc.execute(1, media);
        System.out.println();
    }

    // =========================================================================
    // Stub repositories (in-memory, no DB needed)
    // =========================================================================

    static class StubArtisanRepo implements IArtisanReader, IArtisanWriter {
        private final Map<Integer, Artisan> store = new LinkedHashMap<>();
        

        StubArtisanRepo(Artisan... artisans) {
            for (Artisan a : artisans) store.put(a.getId(), a);
        }

        @Override public Artisan findById(Integer id) { return store.get(id); }
        @Override public List<Artisan> findAll() { return new ArrayList<>(store.values()); }
        @Override public List<Artisan> findByCity(String city) {
            List<Artisan> r = new ArrayList<>();
            store.values().stream().filter(a -> city.equals(a.getCity())).forEach(r::add);
            return r;
        }
        @Override public List<Artisan> findAvailable() { return new ArrayList<>(store.values()); }
        @Override public List<Artisan> findVerifiedByCategory(Integer categoryId) { return new ArrayList<>(store.values()); }
        @Override public void save(Artisan a) { store.put(a.getId(), a); }
        @Override public void update(Artisan a) { store.put(a.getId(), a); System.out.println("  [stub] Artisan updated: " + a.getId()); }
        @Override public void delete(Integer id) { store.remove(id); }
    }

    static class StubCategoryRepo implements ICategoryReader, ICategoryWriter {
        private final Map<Integer, Category> store = new LinkedHashMap<>();

        StubCategoryRepo(Category... cats) { for (Category c : cats) store.put(c.getId(), c); }

        @Override public Category findById(Integer id) { return store.get(id); }
        @Override public List<Category> findAll() { return new ArrayList<>(store.values()); }
        @Override public List<Category> findPending() {
            List<Category> r = new ArrayList<>();
            store.values().stream().filter(c -> Boolean.TRUE.equals(c.getIsPending())).forEach(r::add);
            return r;
        }
        @Override public void save(Category c) { store.put(c.getId(), c); }
        @Override public void update(Category c) { store.put(c.getId(), c); System.out.println("  [stub] Category updated: " + c.getId()); }
        @Override public void delete(Integer id) { store.remove(id); }
    }

    static class StubEvaluationRepo implements IEvaluation {
        private final List<Evaluation> all;
        private double countResult = 0.0;

        StubEvaluationRepo(List<Evaluation> all) { this.all = new ArrayList<>(all); }
        void setCountResult(double v) { this.countResult = v; }

        @Override public Evaluation findById(Integer id) { return all.stream().filter(e -> e.getId().equals(id)).findFirst().orElse(null); }
        @Override public List<Evaluation> findAll() { return all; }
        @Override public List<Evaluation> findByArtisanId(Integer artisanId) { return all; }
        @Override public void save(Evaluation e) { all.add(e); }
        @Override public void update(Evaluation e) {}
        @Override public void delete(Integer id) { all.removeIf(e -> e.getId().equals(id)); }
        @Override public double countByArtisanAndCategory(Integer artisanId, Integer categoryId) { return countResult; }
    }

    static class StubServiceRequestRepo implements IServiceRequest {
        private ServiceRequest single;
        private List<ServiceRequest> pending = new ArrayList<>();
        private double avgPrice = 100.0;
        private double avgBookings = 10.0;
        private LocalDate lastActivity = LocalDate.now().minusDays(10);
        private int countByUser = 5;

        StubServiceRequestRepo(ServiceRequest single) {
            this.single = single;
            if (single != null) pending.add(single);
        }

        void setAvgPrice(double v) { avgPrice = v; }
        void setAvgBookings(double v) { avgBookings = v; }
        void setLastActivity(LocalDate d) { lastActivity = d; }
        void setCountByUser(int n) { countByUser = n; }
        void setPending(List<ServiceRequest> list) { pending = list; }

        @Override public ServiceRequest findById(Integer id) { return single; }
        @Override public List<ServiceRequest> findAll() { return pending; }
        @Override public List<ServiceRequest> findByStatus(ServiceRequest.ServiceStatus s) { return pending; }
        @Override public void save(ServiceRequest r) {}
        @Override public void update(ServiceRequest r) { System.out.println("  [stub] ServiceRequest updated: " + (r != null ? r.getId() : "null")); }
        @Override public void delete(Integer id) {}
        @Override public List<ServiceRequest> findPending() { return pending; }
        @Override public LocalDate lastActivityDate(Integer id) { return lastActivity; }
        @Override public int countByUser(Integer id) { return countByUser; }
        @Override public boolean hadLowRatingRecently(Integer id, int days) { return false; }
        @Override public LocalDate lastRequestDate(Integer id) { return lastActivity; }
        @Override public double averagePriceByCategory(int categoryId) { return avgPrice; }
        @Override public double avgMonthlyBookingsByCategory(int categoryId) { return avgBookings; }
    }

    static class StubUserRepo implements IUser {
        private final Map<Integer, User> store = new LinkedHashMap<>();

        StubUserRepo(User... users) { for (User u : users) store.put(u.getId(), u); }

        @Override public User findById(Integer id) { return store.get(id); }
        @Override public List<User> findAll() { return new ArrayList<>(store.values()); }
        @Override public void save(User u) { store.put(u.getId(), u); }
        @Override public void update(User u) { store.put(u.getId(), u); System.out.println("  [stub] User updated: " + u.getId()); }
        @Override public void delete(Integer id) { store.remove(id); }
        @Override public List<User> findActive() { return new ArrayList<>(store.values()); }
    }
}*/


package Presentation;


import infrastructure.Config.DatabaseConnection;


import javax.swing.*;


/**
 * Application entry point.
 * Obtains the singleton DB connection, wires all repositories,
 * and launches the Swing UI.
 */

public class Main {

    public static void main(String[] args) {

        Runtime.getRuntime().addShutdownHook(new Thread(() -> {
            try {
                DatabaseConnection.getConnection().close();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }));

        SwingUtilities.invokeLater(() -> {
            AppConfig.createMainFrame().setVisible(true);
        });
    }
}
