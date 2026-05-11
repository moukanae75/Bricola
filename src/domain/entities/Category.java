package domain.entities;

public class Category {

    private Integer id;
    private String name;
    private Boolean isApproved;
    private Boolean isPending;

    public Category() {}

    public Category(Integer id, String name, Boolean isApproved, Boolean isPending) {
        this.id = id;
        this.name = name;
        this.isApproved = isApproved;
        this.isPending = isPending;
    }

    public void approve() {
        this.isApproved = true;
        this.isPending = false;
    }

    public void reject() {
        this.isApproved = false;
        this.isPending = false;
    }

    public void markPending() {
        this.isPending = true;
    }

    // Getters and Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public Boolean getIsApproved() { return isApproved; }
    public void setIsApproved(Boolean isApproved) { this.isApproved = isApproved; }
    public Boolean getIsPending() { return isPending; }
    public void setIsPending(Boolean isPending) { this.isPending = isPending; }
}
