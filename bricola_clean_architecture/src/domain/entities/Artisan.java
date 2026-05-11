package domain.entities;

import java.util.Date;

public class Artisan extends User {

    private String bio;
    private String city;
    private Float averageRating;
    private Boolean isVerified;
    private String portfolio;
    private Date registeredAt;

    // Operational fields used by use cases
    private int maxConcurrentJobs = 3;       // default max parallel jobs
    private double responseRate = 0.8;       // default 80% response rate [0,1]
    private double latitude = 0.0;
    private double longitude = 0.0;

    public Artisan() {
        super();
    }

    public Artisan(Integer id, String name, String email, String passwordHash, String phone,
                   String bio, String city, Float averageRating, Boolean isVerified,
                   String portfolio, Date registeredAt) {
        super(id, name, email, passwordHash, phone);
        this.bio = bio;
        this.city = city;
        this.averageRating = averageRating != null ? averageRating : 0f;
        this.isVerified = isVerified != null ? isVerified : false;
        this.portfolio = portfolio;
        this.registeredAt = registeredAt;
    }

    @Override
    public void register() {
        System.out.println("Artisan registered: " + getName());
    }

    public void acceptRequest(Integer requestId) {
        System.out.println("Request accepted: " + requestId);
    }

    public void declineRequest(Integer requestId) {
        System.out.println("Request declined: " + requestId);
    }

    public void updateStatus(String status) {
        System.out.println("Status updated to: " + status);
    }

    public void proposeSkill(String name) {
        System.out.println("New skill proposed: " + name);
    }

    /**
     * Marks this artisan as verified.
     * Throws if already verified.
     */
    public void verify() {
        if (Boolean.TRUE.equals(this.isVerified)) {
            throw new IllegalStateException("Artisan is already verified");
        }
        this.isVerified = true;
    }

    public boolean isVerified() {
        return Boolean.TRUE.equals(isVerified);
    }

    /**
     * Euclidean approximation of distance in km to a given [lat, lon] location.
     * In production this would use the Haversine formula or a geo service.
     *
     * @param clientLocation  Object expected to be a double[]{lat, lon}
     */
    public double distanceTo(Object clientLocation) {
        if (clientLocation instanceof double[]) {
            double[] coords = (double[]) clientLocation;
            double dLat = this.latitude - coords[0];
            double dLon = this.longitude - coords[1];
            // 1 degree ≈ 111 km
            return Math.sqrt(dLat * dLat + dLon * dLon) * 111.0;
        }
        return 10.0; // default distance when location is unknown
    }

    // Getters and Setters
    public String getBio() { return bio; }
    public void setBio(String bio) { this.bio = bio; }
    public String getCity() { return city; }
    public void setCity(String city) { this.city = city; }
    public Float getAverageRating() { return averageRating != null ? averageRating : 0f; }
    public void setAverageRating(Float averageRating) { this.averageRating = averageRating; }
    public Boolean getIsVerified() { return isVerified; }
    public void setIsVerified(Boolean isVerified) { this.isVerified = isVerified; }
    public String getPortfolio() { return portfolio; }
    public void setPortfolio(String portfolio) { this.portfolio = portfolio; }
    public Date getRegisteredAt() { return registeredAt; }
    public void setRegisteredAt(Date registeredAt) { this.registeredAt = registeredAt; }
    public int getMaxConcurrentJobs() { return maxConcurrentJobs; }
    public void setMaxConcurrentJobs(int maxConcurrentJobs) { this.maxConcurrentJobs = maxConcurrentJobs; }
    public double getResponseRate() { return responseRate; }
    public void setResponseRate(double responseRate) { this.responseRate = responseRate; }
    public double getLatitude() { return latitude; }
    public void setLatitude(double latitude) { this.latitude = latitude; }
    public double getLongitude() { return longitude; }
    public void setLongitude(double longitude) { this.longitude = longitude; }
}
