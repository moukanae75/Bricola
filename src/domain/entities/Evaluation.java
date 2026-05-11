package domain.entities;

import java.util.Date;

public class Evaluation {

    private Integer id;
    private Integer rating;
    private String comment;
    private Date submittedAt;
    private Boolean isVisible;
    private Integer clientId;
    private Integer serviceRequestId;

    public Evaluation() {}

    public Evaluation(Integer id, Integer rating, String comment, Date submittedAt, Boolean isVisible) {
        this.id = id;
        this.rating = rating;
        this.comment = comment;
        this.submittedAt = submittedAt;
        this.isVisible = isVisible;
    }

    public Evaluation(Integer id, Integer rating, String comment, Date submittedAt,
                      Boolean isVisible, Integer clientId, Integer serviceRequestId) {
        this(id, rating, comment, submittedAt, isVisible);
        this.clientId = clientId;
        this.serviceRequestId = serviceRequestId;
    }

    public void submit() {
        this.submittedAt = new Date();
    }

    // Getters and Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public Integer getRating() { return rating; }
    public void setRating(Integer rating) { this.rating = rating; }
    public String getComment() { return comment; }
    public void setComment(String comment) { this.comment = comment; }
    public Date getSubmittedAt() { return submittedAt; }
    public void setSubmittedAt(Date submittedAt) { this.submittedAt = submittedAt; }
    public Boolean getIsVisible() { return isVisible; }
    public void setIsVisible(Boolean isVisible) { this.isVisible = isVisible; }
    public Integer getClientId() { return clientId; }
    public void setClientId(Integer clientId) { this.clientId = clientId; }
    public Integer getServiceRequestId() { return serviceRequestId; }
    public void setServiceRequestId(Integer serviceRequestId) { this.serviceRequestId = serviceRequestId; }
}
