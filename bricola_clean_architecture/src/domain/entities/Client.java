package domain.entities;

import java.util.Date;

public class Client extends User {

    private String address;
    private String city;
    private String profilePhoto;
    private Date createdAt;

    public Client() {
        super();
    }

    public Client(Integer id, String name, String email, String passwordHash, String phone,
                  String address, String city, String profilePhoto, Date createdAt) {
        super(id, name, email, passwordHash, phone);
        this.address = address;
        this.city = city;
        this.profilePhoto = profilePhoto;
        this.createdAt = createdAt;
    }

    @Override
    public void register() {
        System.out.println("Client registered: " + getName());
    }

    public void submitServiceRequest() {
        System.out.println("Service request submitted by: " + getName());
    }

    public void viewArtisanProfile() {
        System.out.println("Viewing artisan profile");
    }

    public void leaveEvaluation() {
        System.out.println("Evaluation submitted");
    }

    // Getters and Setters
    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }
    public String getCity() { return city; }
    public void setCity(String city) { this.city = city; }
    public String getProfilePhoto() { return profilePhoto; }
    public void setProfilePhoto(String profilePhoto) { this.profilePhoto = profilePhoto; }
    public Date getCreatedAt() { return createdAt; }
    public void setCreatedAt(Date createdAt) { this.createdAt = createdAt; }
}
