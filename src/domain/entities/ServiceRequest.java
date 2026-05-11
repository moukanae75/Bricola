package domain.entities;

import domain.enumeration.CategoryComplexityTier;
import java.util.Date;

public class ServiceRequest {

    public enum ServiceStatus {
        PENDING, ACCEPTED, IN_PROGRESS, COMPLETED, CANCELLED, DECLINED
    }

    private Integer id;
    private String title;
    private String description;
    private ServiceStatus status;
    private String location;
    private Date requestedAt;
    private Date scheduledAt;

    // FK fields matching the DB schema
    private Integer clientId;
    private Integer artisanId;
    private Integer categoryId;

    // Operational fields used by use cases
    private CategoryComplexityTier categoryComplexityTier = CategoryComplexityTier.MEDIUM;
    private boolean urgent = false;

    // Client location as [lat, lon] for proximity calculations
    private double[] clientLocation = {0.0, 0.0};

    public ServiceRequest() {}

    public ServiceRequest(Integer id, String title, String description, ServiceStatus status,
                          String location, Date requestedAt, Date scheduledAt) {
        this.id = id;
        this.title = title;
        this.description = description;
        this.status = status;
        this.location = location;
        this.requestedAt = requestedAt;
        this.scheduledAt = scheduledAt;
    }

    public ServiceRequest(Integer id, String title, String description, ServiceStatus status,
                          String location, Date requestedAt, Date scheduledAt,
                          Integer clientId, Integer artisanId, Integer categoryId) {
        this(id, title, description, status, location, requestedAt, scheduledAt);
        this.clientId = clientId;
        this.artisanId = artisanId;
        this.categoryId = categoryId;
    }

    public void cancel() {
        this.status = ServiceStatus.CANCELLED;
    }

    // Getters and Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }
    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }
    public ServiceStatus getStatus() { return status; }
    public void setStatus(ServiceStatus status) { this.status = status; }
    public String getLocation() { return location; }
    public void setLocation(String location) { this.location = location; }
    public Date getRequestedAt() { return requestedAt; }
    public void setRequestedAt(Date requestedAt) { this.requestedAt = requestedAt; }
    public Date getScheduledAt() { return scheduledAt; }
    public void setScheduledAt(Date scheduledAt) { this.scheduledAt = scheduledAt; }
    public Integer getClientId() { return clientId; }
    public void setClientId(Integer clientId) { this.clientId = clientId; }
    public Integer getArtisanId() { return artisanId; }
    public void setArtisanId(Integer artisanId) { this.artisanId = artisanId; }

    /** Returns the category ID of this request. */
    public Integer getCategoryId() { return categoryId; }
    public void setCategoryId(Integer categoryId) { this.categoryId = categoryId; }

    /** Returns the complexity tier of this request's category. */
    public CategoryComplexityTier getCategoryComplexityTier() { return categoryComplexityTier; }
    public void setCategoryComplexityTier(CategoryComplexityTier categoryComplexityTier) {
        this.categoryComplexityTier = categoryComplexityTier;
    }

    /** Returns true if this request has been flagged as urgent. */
    public boolean isUrgent() { return urgent; }
    public void setUrgent(boolean urgent) { this.urgent = urgent; }

    /**
     * Returns the client's geographic location as a double[]{lat, lon}.
     * Used by proximity-based matching algorithms.
     */
    public double[] getClientLocation() { return clientLocation; }
    public void setClientLocation(double[] clientLocation) { this.clientLocation = clientLocation; }
}
