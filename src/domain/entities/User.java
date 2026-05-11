package domain.entities;

import java.time.LocalDate;

public abstract class User {

    private Integer id;
    private String name;
    private String email;
    private String passwordHash;
    private String phone;
    private boolean suspended = false;
    private LocalDate suspendedAt = null;

    public User() {}

    public User(Integer id, String name, String email, String passwordHash, String phone) {
        this.id = id;
        this.name = name;
        this.email = email;
        this.passwordHash = passwordHash;
        this.phone = phone;
    }

    public abstract void register();

    public boolean login(String email, String pwd) {
        return this.email.equals(email) && this.passwordHash.equals(pwd);
    }

    public void suspend() {
        this.suspended = true;
        this.suspendedAt = LocalDate.now();
    }


    

    public boolean isSuspended() { return suspended; }

 
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
    public String getPasswordHash() { return passwordHash; }
    public void setPasswordHash(String passwordHash) { this.passwordHash = passwordHash; }
    public String getPhone() { return phone; }
    public void setPhone(String phone) { this.phone = phone; }

    public boolean isActive() {
    return !suspended;
}
    public void setActive(boolean boolean1) {
        
        throw new UnsupportedOperationException("Unimplemented method 'setActive'");
    }

    public boolean wasSuspendedWithinDays(int days) {
    return false;
    }
}