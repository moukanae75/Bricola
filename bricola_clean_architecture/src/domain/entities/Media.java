package domain.entities;

import java.util.Date;

public class Media {

    public enum MediaType {
        IMAGE, VIDEO, DOCUMENT
    }

    private Integer id;
    private String fileUrl;
    private MediaType type;
    private Date uploadedAt;
    private Integer serviceRequestId;

    public Media() {}

    public Media(Integer id, String fileUrl, MediaType type, Date uploadedAt, Integer serviceRequestId) {
        this.id = id;
        this.fileUrl = fileUrl;
        this.type = type;
        this.uploadedAt = uploadedAt;
        this.serviceRequestId = serviceRequestId;
    }

    public String getUrl() {
        return fileUrl;
    }

    // Getters and Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public String getFileUrl() { return fileUrl; }
    public void setFileUrl(String fileUrl) { this.fileUrl = fileUrl; }
    public MediaType getType() { return type; }
    public void setType(MediaType type) { this.type = type; }
    public Date getUploadedAt() { return uploadedAt; }
    public void setUploadedAt(Date uploadedAt) { this.uploadedAt = uploadedAt; }
    public Integer getServiceRequestId() { return serviceRequestId; }
    public void setServiceRequestId(Integer serviceRequestId) { this.serviceRequestId = serviceRequestId; }
}
