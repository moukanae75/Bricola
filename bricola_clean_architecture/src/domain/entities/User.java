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

    /** Suspends this user and records the suspension date. */
    public void suspend() {
        this.suspended = true;
        this.suspendedAt = LocalDate.now();
    }

    /**
     * Returns true if the user was suspended within the last {@code days} days.
     */
    public boolean wasSuspendedWithinDays(int days) {
        if (!suspended || suspendedAt == null) return false;
        return suspendedAt.isAfter(LocalDate.now().minusDays(days));
    }

    public boolean isSuspended() { return suspended; }

    // Getters and Setters
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
}
