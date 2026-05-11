package domain.entities;

import java.util.Date;

public class Admin extends User {

    private Integer adminLevel;
    private Date lastLoginAt;

    public Admin() {
        super();
    }

    public Admin(Integer id, String name, String email, String passwordHash, String phone,
                 Integer adminLevel, Date lastLoginAt) {
        super(id, name, email, passwordHash, phone);
        this.adminLevel = adminLevel;
        this.lastLoginAt = lastLoginAt;
    }

    @Override
    public void register() {
        System.out.println("Admin registered: " + getName());
    }

    public Integer getAdminLevel() { return adminLevel; }
    public void setAdminLevel(Integer adminLevel) { this.adminLevel = adminLevel; }
    public Date getLastLoginAt() { return lastLoginAt; }
    public void setLastLoginAt(Date lastLoginAt) { this.lastLoginAt = lastLoginAt; }
}
