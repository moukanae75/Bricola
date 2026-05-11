package infrastructure.persistence;

import domain.entities.ServiceRequest;
import domain.repositories.IServiceRequest;

import java.sql.*;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

public class MySQLServiceRequestRepo implements IServiceRequest {

    private final Connection connection;

    public MySQLServiceRequestRepo(Connection connection) {
        this.connection = connection;
    }

    @Override
    public ServiceRequest findById(Integer id) {
        String query = "SELECT * FROM service_request WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return mapResultSetToRequest(rs);
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public List<ServiceRequest> findAll() {
        List<ServiceRequest> list = new ArrayList<>();
        String query = "SELECT * FROM service_request";
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) list.add(mapResultSetToRequest(rs));
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return list;
    }

    @Override
    public List<ServiceRequest> findByStatus(ServiceRequest.ServiceStatus status) {
        List<ServiceRequest> list = new ArrayList<>();
        String query = "SELECT * FROM service_request WHERE status = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, status.name());
            ResultSet rs = stmt.executeQuery();
            while (rs.next()) list.add(mapResultSetToRequest(rs));
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return list;
    }

    @Override
    public void save(ServiceRequest request) {
        String query =
            "INSERT INTO service_request (title, description, status, location, requested_at, " +
            "scheduled_at, client_id, artisan_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(query, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, request.getTitle());
            stmt.setString(2, request.getDescription());
            stmt.setString(3, request.getStatus() != null ? request.getStatus().name() : ServiceRequest.ServiceStatus.PENDING.name());
            stmt.setString(4, request.getLocation());
            stmt.setTimestamp(5, request.getRequestedAt() != null
                ? new Timestamp(request.getRequestedAt().getTime())
                : new Timestamp(System.currentTimeMillis()));
            stmt.setTimestamp(6, request.getScheduledAt() != null
                ? new Timestamp(request.getScheduledAt().getTime()) : null);
            stmt.setInt(7, request.getClientId());
            if (request.getArtisanId() != null) stmt.setInt(8, request.getArtisanId());
            else stmt.setNull(8, Types.INTEGER);
            if (request.getCategoryId() != null) stmt.setInt(9, request.getCategoryId());
            else stmt.setNull(9, Types.INTEGER);
            stmt.executeUpdate();
            ResultSet keys = stmt.getGeneratedKeys();
            if (keys.next()) request.setId(keys.getInt(1));
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void update(ServiceRequest request) {
        String query =
            "UPDATE service_request SET title=?, description=?, status=?, location=?, " +
            "scheduled_at=?, artisan_id=?, category_id=? WHERE id=?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, request.getTitle());
            stmt.setString(2, request.getDescription());
            stmt.setString(3, request.getStatus().name());
            stmt.setString(4, request.getLocation());
            stmt.setTimestamp(5, request.getScheduledAt() != null
                ? new Timestamp(request.getScheduledAt().getTime()) : null);
            if (request.getArtisanId() != null) stmt.setInt(6, request.getArtisanId());
            else stmt.setNull(6, Types.INTEGER);
            if (request.getCategoryId() != null) stmt.setInt(7, request.getCategoryId());
            else stmt.setNull(7, Types.INTEGER);
            stmt.setInt(8, request.getId());
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void delete(Integer id) {
        String query = "DELETE FROM service_request WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public List<ServiceRequest> findPending() {
        return findByStatus(ServiceRequest.ServiceStatus.PENDING);
    }

    @Override
    public LocalDate lastActivityDate(Integer userId) {
        // Most recent request activity (either as client or artisan)
        String query =
            "SELECT MAX(requested_at) FROM service_request " +
            "WHERE client_id = ? OR artisan_id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next() && rs.getDate(1) != null) {
                return rs.getDate(1).toLocalDate();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public int countByUser(Integer userId) {
        String query =
            "SELECT COUNT(*) FROM service_request WHERE client_id = ? OR artisan_id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return rs.getInt(1);
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return 0;
    }

    @Override
    public boolean hadLowRatingRecently(Integer userId, int withinDays) {
        // A "low rating" is a rating of 1 or 2 submitted in the past withinDays days
        String query =
            "SELECT COUNT(*) FROM evaluation e " +
            "JOIN service_request sr ON e.service_request_id = sr.id " +
            "WHERE (sr.client_id = ? OR sr.artisan_id = ?) " +
            "AND e.rating <= 2 " +
            "AND e.submitted_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            stmt.setInt(2, userId);
            stmt.setInt(3, withinDays);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return rs.getInt(1) > 0;
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return false;
    }

    @Override
    public LocalDate lastRequestDate(Integer userId) {
        String query =
            "SELECT MAX(requested_at) FROM service_request WHERE client_id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, userId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next() && rs.getDate(1) != null) {
                return rs.getDate(1).toLocalDate();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public double averagePriceByCategory(int categoryId) {
        // The DB schema has no price column — return a default market estimate
        // In a production system, price would be stored per request or quotation
        return 100.0;
    }

    @Override
    public double avgMonthlyBookingsByCategory(int categoryId) {
        // Count completed requests in the last 30 days for this category
        String query =
            "SELECT COUNT(*) FROM service_request " +
            "WHERE category_id = ? AND status = 'COMPLETED' " +
            "AND requested_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, categoryId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return rs.getDouble(1);
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return 10.0; // default when no data
    }

    private ServiceRequest mapResultSetToRequest(ResultSet rs) throws SQLException {
        ServiceRequest req = new ServiceRequest(
            rs.getInt("id"),
            rs.getString("title"),
            rs.getString("description"),
            ServiceRequest.ServiceStatus.valueOf(rs.getString("status")),
            rs.getString("location"),
            rs.getTimestamp("requested_at"),
            rs.getTimestamp("scheduled_at")
        );
        req.setClientId(rs.getInt("client_id"));
        int artisanId = rs.getInt("artisan_id");
        if (!rs.wasNull()) req.setArtisanId(artisanId);
        int categoryId = rs.getInt("category_id");
        if (!rs.wasNull()) req.setCategoryId(categoryId);
        return req;
    }
}
