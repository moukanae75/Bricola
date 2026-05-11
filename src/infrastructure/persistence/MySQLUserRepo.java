package infrastructure.persistence;

import domain.entities.*;
import domain.repositories.IUser;
import java.sql.*;
import java.util.*;


public class MySQLUserRepo implements IUser {

    private final Connection conn;

    public MySQLUserRepo(Connection conn) {
        this.conn = conn;
    }

    
    @Override
    public List<User> findActive() {
        List<User> users = new ArrayList<>();
        users.addAll(fetchClients());
        users.addAll(fetchArtisans());
        users.addAll(fetchAdmins());
        return users;
    }

    @Override
    public List<User> findAll() {
        return findActive();
    }

    // ── findById: searches all three tables ───────────────────────────────
    @Override
    public User findById(Integer id) {
        User u = findClientById(id);
        if (u != null) return u;
        u = findArtisanById(id);
        if (u != null) return u;
        return findAdminById(id);
    }

    // ── save / update / delete ────────────────────────────────────────────
    @Override
    public void save(User user) {
        if (user instanceof Client)  saveClient((Client) user);
        else if (user instanceof Artisan) saveArtisan((Artisan) user);
        else if (user instanceof Admin)   saveAdmin((Admin) user);
    }

    @Override
    public void update(User user) {
        if (user instanceof Client)  updateClient((Client) user);
        else if (user instanceof Artisan) updateArtisan((Artisan) user);
        else if (user instanceof Admin)   updateAdmin((Admin) user);
    }

    @Override
    public void delete(Integer id) {
        for (String table : new String[]{"client", "artisan", "admin"}) {
            String sql = "DELETE FROM " + table + " WHERE id = ?";
            try (PreparedStatement ps = conn.prepareStatement(sql)) {
                ps.setInt(1, id);
                int affected = ps.executeUpdate();
                if (affected > 0) return; // found and deleted
            } catch (SQLException e) { e.printStackTrace(); }
        }
    }

    // ── Fetch all from each table ─────────────────────────────────────────

    private List<User> fetchClients() {
        List<User> list = new ArrayList<>();
        String sql = "SELECT * FROM client";
        try (PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) list.add(mapClient(rs));
        } catch (SQLException e) { e.printStackTrace(); }
        return list;
    }

    private List<User> fetchArtisans() {
        List<User> list = new ArrayList<>();
        String sql = "SELECT * FROM artisan";
        try (PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) list.add(mapArtisan(rs));
        } catch (SQLException e) { e.printStackTrace(); }
        return list;
    }

    private List<User> fetchAdmins() {
        List<User> list = new ArrayList<>();
        String sql = "SELECT * FROM admin";
        try (PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) list.add(mapAdmin(rs));
        } catch (SQLException e) { e.printStackTrace(); }
        return list;
    }

    // ── Find by ID from each table ────────────────────────────────────────

    private User findClientById(Integer id) {
        String sql = "SELECT * FROM client WHERE id = ?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (rs.next()) return mapClient(rs);
        } catch (SQLException e) { e.printStackTrace(); }
        return null;
    }

    private User findArtisanById(Integer id) {
        String sql = "SELECT * FROM artisan WHERE id = ?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (rs.next()) return mapArtisan(rs);
        } catch (SQLException e) { e.printStackTrace(); }
        return null;
    }

    private User findAdminById(Integer id) {
        String sql = "SELECT * FROM admin WHERE id = ?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, id);
            ResultSet rs = ps.executeQuery();
            if (rs.next()) return mapAdmin(rs);
        } catch (SQLException e) { e.printStackTrace(); }
        return null;
    }

    // ── Mappers ───────────────────────────────────────────────────────────

    private Client mapClient(ResultSet rs) throws SQLException {
        return new Client(
            rs.getInt("id"),
            rs.getString("name"),
            rs.getString("email"),
            rs.getString("password_hash"),
            rs.getString("phone"),
            rs.getString("address"),
            rs.getString("city"),
            rs.getString("profile_photo"),
            rs.getDate("created_at")
        );
    }

    private Artisan mapArtisan(ResultSet rs) throws SQLException {
        return new Artisan(
            rs.getInt("id"),
            rs.getString("name"),
            rs.getString("email"),
            rs.getString("password_hash"),
            rs.getString("phone"),
            rs.getString("bio"),
            rs.getString("city"),
            rs.getFloat("average_rating"),
            rs.getBoolean("is_verified"),
            rs.getString("portfolio"),
            rs.getDate("registered_at")
        );
    }

    private Admin mapAdmin(ResultSet rs) throws SQLException {
        return new Admin(
            rs.getInt("id"),
            rs.getString("name"),
            rs.getString("email"),
            rs.getString("password_hash"),
            rs.getString("phone"),
            rs.getInt("admin_level"),
            rs.getDate("last_login_at")
        );
    }


    private void saveClient(Client c) {
        String sql = "INSERT INTO client (name, email, password_hash, phone, address, city, profile_photo) VALUES (?,?,?,?,?,?,?)";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, c.getName());
            ps.setString(2, c.getEmail());
            ps.setString(3, c.getPasswordHash());
            ps.setString(4, c.getPhone());
            ps.setString(5, c.getAddress());
            ps.setString(6, c.getCity());
            ps.setString(7, c.getProfilePhoto());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }

    private void saveArtisan(Artisan a) {
        String sql = "INSERT INTO artisan (name, email, password_hash, phone, bio, city, average_rating, is_verified, portfolio) VALUES (?,?,?,?,?,?,?,?,?)";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, a.getName());
            ps.setString(2, a.getEmail());
            ps.setString(3, a.getPasswordHash());
            ps.setString(4, a.getPhone());
            ps.setString(5, a.getBio());
            ps.setString(6, a.getCity());
            ps.setFloat(7,  a.getAverageRating());
            ps.setBoolean(8, a.isVerified());
            ps.setString(9, a.getPortfolio());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }

    private void saveAdmin(Admin a) {
        String sql = "INSERT INTO admin (name, email, password_hash, phone, admin_level) VALUES (?,?,?,?,?)";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, a.getName());
            ps.setString(2, a.getEmail());
            ps.setString(3, a.getPasswordHash());
            ps.setString(4, a.getPhone());
            ps.setInt(5,    a.getAdminLevel());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }

    

    private void updateClient(Client c) {
        String sql = "UPDATE client SET name=?, email=?, phone=?, address=?, city=?, profile_photo=? WHERE id=?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, c.getName());
            ps.setString(2, c.getEmail());
            ps.setString(3, c.getPhone());
            ps.setString(4, c.getAddress());
            ps.setString(5, c.getCity());
            ps.setString(6, c.getProfilePhoto());
            ps.setInt(7,    c.getId());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }

    private void updateArtisan(Artisan a) {
        String sql = "UPDATE artisan SET name=?, email=?, phone=?, bio=?, city=?, average_rating=?, is_verified=?, portfolio=? WHERE id=?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, a.getName());
            ps.setString(2, a.getEmail());
            ps.setString(3, a.getPhone());
            ps.setString(4, a.getBio());
            ps.setString(5, a.getCity());
            ps.setFloat(6,  a.getAverageRating());
            ps.setBoolean(7, a.isVerified());
            ps.setString(8, a.getPortfolio());
            ps.setInt(9,    a.getId());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }

    private void updateAdmin(Admin a) {
        String sql = "UPDATE admin SET name=?, email=?, phone=?, admin_level=?, last_login_at=? WHERE id=?";
        try (PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, a.getName());
            ps.setString(2, a.getEmail());
            ps.setString(3, a.getPhone());
            ps.setInt(4,    a.getAdminLevel());
            ps.setDate(5,  a.getLastLoginAt() != null 
    ? new java.sql.Date(a.getLastLoginAt().getTime()) 
    : null);
            ps.setInt(6,    a.getId());
            ps.executeUpdate();
        } catch (SQLException e) { e.printStackTrace(); }
    }


}