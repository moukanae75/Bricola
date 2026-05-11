package infrastructure.persistence;

import domain.entities.Artisan;
import domain.repositories.IArtisanReader;
import domain.repositories.IArtisanWriter;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class MySQLArtisanRepo implements IArtisanReader, IArtisanWriter {

    private final Connection connection;

    public MySQLArtisanRepo(Connection connection) {
        this.connection = connection;
    }

    // -----------------------------------------------------------------------
    // IArtisanReader
    // -----------------------------------------------------------------------

    @Override
    public Artisan findById(Integer id) {
        String query = "SELECT * FROM artisan WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                return mapResultSetToArtisan(rs);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public List<Artisan> findAll() {
        List<Artisan> artisans = new ArrayList<>();
        String query = "SELECT * FROM artisan";
        // Use Statement (no parameters) — avoids double-execution bug from original
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) {
                artisans.add(mapResultSetToArtisan(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return artisans;
    }

    @Override
    public List<Artisan> findByCity(String city) {
        List<Artisan> result = new ArrayList<>();
        String query = "SELECT * FROM artisan WHERE city = ?";
        // Fixed: use PreparedStatement so the city parameter is actually bound
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, city);
            ResultSet rs = stmt.executeQuery();
            while (rs.next()) {
                result.add(mapResultSetToArtisan(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return result;
    }

    @Override
    public List<Artisan> findAvailable() {
        List<Artisan> result = new ArrayList<>();
        // An artisan is "available" when they are verified and have no IN_PROGRESS requests
        String query =
            "SELECT a.* FROM artisan a " +
            "WHERE a.is_verified = 1 " +
            "AND (SELECT COUNT(*) FROM service_request sr " +
            "     WHERE sr.artisan_id = a.id AND sr.status = 'IN_PROGRESS') < 3";
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) {
                result.add(mapResultSetToArtisan(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return result;
    }

    @Override
    public List<Artisan> findVerifiedByCategory(Integer categoryId) {
        List<Artisan> result = new ArrayList<>();
        String query =
            "SELECT DISTINCT a.* FROM artisan a " +
            "JOIN artisan_category ac ON a.id = ac.artisan_id " +
            "WHERE ac.category_id = ? AND a.is_verified = 1";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, categoryId);
            ResultSet rs = stmt.executeQuery();
            while (rs.next()) {
                result.add(mapResultSetToArtisan(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return result;
    }

    // -----------------------------------------------------------------------
    // IArtisanWriter
    // -----------------------------------------------------------------------

    @Override
    public void save(Artisan artisan) {
        String query =
            "INSERT INTO artisan (name, email, password_hash, phone, bio, city, " +
            "average_rating, is_verified, portfolio, registered_at) " +
            "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(query, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, artisan.getName());
            stmt.setString(2, artisan.getEmail());
            stmt.setString(3, artisan.getPasswordHash());
            stmt.setString(4, artisan.getPhone());
            stmt.setString(5, artisan.getBio());
            stmt.setString(6, artisan.getCity());
            stmt.setFloat(7, artisan.getAverageRating());
            stmt.setBoolean(8, artisan.getIsVerified() != null && artisan.getIsVerified());
            stmt.setString(9, artisan.getPortfolio());
            stmt.setDate(10, artisan.getRegisteredAt() != null
                ? new java.sql.Date(artisan.getRegisteredAt().getTime())
                : new java.sql.Date(System.currentTimeMillis()));
            stmt.executeUpdate();
            ResultSet generatedKeys = stmt.getGeneratedKeys();
            if (generatedKeys.next()) {
                artisan.setId(generatedKeys.getInt(1));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void update(Artisan artisan) {
        String query =
            "UPDATE artisan SET name = ?, email = ?, phone = ?, bio = ?, city = ?, " +
            "average_rating = ?, is_verified = ?, portfolio = ? WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, artisan.getName());
            stmt.setString(2, artisan.getEmail());
            stmt.setString(3, artisan.getPhone());
            stmt.setString(4, artisan.getBio());
            stmt.setString(5, artisan.getCity());
            stmt.setFloat(6, artisan.getAverageRating());
            stmt.setBoolean(7, artisan.getIsVerified() != null && artisan.getIsVerified());
            stmt.setString(8, artisan.getPortfolio());
            stmt.setInt(9, artisan.getId());
            int rows = stmt.executeUpdate();
            System.out.println(rows > 0 ? "Artisan updated: " + artisan.getId() : "No artisan found: " + artisan.getId());
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void delete(Integer id) {
        String query = "DELETE FROM artisan WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    // -----------------------------------------------------------------------
    // Mapping
    // -----------------------------------------------------------------------

    private Artisan mapResultSetToArtisan(ResultSet rs) throws SQLException {
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
}
