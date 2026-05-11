package domain.entities;

import java.util.Date;

public class Report {

    public enum ReportType {
        USERS_SUMMARY, SERVICES_SUMMARY, EVALUATIONS_SUMMARY
    }

    private Integer id;
    private Date generatedAt;
    private ReportType type;
    private String content;
    private Integer adminId;

    public Report() {}

    public Report(Integer id, Date generatedAt, ReportType type, String content, Integer adminId) {
        this.id = id;
        this.generatedAt = generatedAt;
        this.type = type;
        this.content = content;
        this.adminId = adminId;
    }

    // Getters and Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public Date getGeneratedAt() { return generatedAt; }
    public void setGeneratedAt(Date generatedAt) { this.generatedAt = generatedAt; }
    public ReportType getType() { return type; }
    public void setType(ReportType type) { this.type = type; }
    public String getContent() { return content; }
    public void setContent(String content) { this.content = content; }
    public Integer getAdminId() { return adminId; }
    public void setAdminId(Integer adminId) { this.adminId = adminId; }
}
