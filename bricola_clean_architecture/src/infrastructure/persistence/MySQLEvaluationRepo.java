package infrastructure.persistence;

import domain.entities.Evaluation;
import domain.repositories.IEvaluation;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class MySQLEvaluationRepo implements IEvaluation {

    private final Connection connection;

    public MySQLEvaluationRepo(Connection connection) {
        this.connection = connection;
    }

    @Override
    public Evaluation findById(Integer id) {
        String query = "SELECT * FROM evaluation WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return mapResultSetToEvaluation(rs);
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public List<Evaluation> findAll() {
        List<Evaluation> list = new ArrayList<>();
        String query = "SELECT * FROM evaluation";
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) list.add(mapResultSetToEvaluation(rs));
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return list;
    }

    @Override
    public List<Evaluation> findByArtisanId(Integer artisanId) {
        List<Evaluation> list = new ArrayList<>();
        // Join through service_request to get the artisan's evaluations
        String query =
            "SELECT e.* FROM evaluation e " +
            "JOIN service_request sr ON e.service_request_id = sr.id " +
            "WHERE sr.artisan_id = ? AND e.is_visible = 1";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, artisanId);
            ResultSet rs = stmt.executeQuery();
            while (rs.next()) list.add(mapResultSetToEvaluation(rs));
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return list;
    }

    @Override
    public void save(Evaluation evaluation) {
        String query =
            "INSERT INTO evaluation (rating, comment, submitted_at, is_visible, client_id, service_request_id) " +
            "VALUES (?, ?, ?, ?, ?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(query, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setInt(1, evaluation.getRating());
            stmt.setString(2, evaluation.getComment());
            stmt.setTimestamp(3, evaluation.getSubmittedAt() != null
                ? new Timestamp(evaluation.getSubmittedAt().getTime())
                : new Timestamp(System.currentTimeMillis()));
            stmt.setBoolean(4, Boolean.TRUE.equals(evaluation.getIsVisible()));
            stmt.setInt(5, evaluation.getClientId());
            stmt.setInt(6, evaluation.getServiceRequestId());
            stmt.executeUpdate();
            ResultSet keys = stmt.getGeneratedKeys();
            if (keys.next()) evaluation.setId(keys.getInt(1));
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void update(Evaluation evaluation) {
        String query =
            "UPDATE evaluation SET rating = ?, comment = ?, is_visible = ? WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, evaluation.getRating());
            stmt.setString(2, evaluation.getComment());
            stmt.setBoolean(3, Boolean.TRUE.equals(evaluation.getIsVisible()));
            stmt.setInt(4, evaluation.getId());
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void delete(Integer id) {
        String query = "DELETE FROM evaluation WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public double countByArtisanAndCategory(Integer artisanId, Integer categoryId) {
        String query =
            "SELECT COUNT(*) FROM evaluation e " +
            "JOIN service_request sr ON e.service_request_id = sr.id " +
            "WHERE sr.artisan_id = ? AND sr.category_id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, artisanId);
            stmt.setInt(2, categoryId);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) return rs.getDouble(1);
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return 0.0;
    }

    private Evaluation mapResultSetToEvaluation(ResultSet rs) throws SQLException {
        return new Evaluation(
            rs.getInt("id"),
            rs.getInt("rating"),
            rs.getString("comment"),
            rs.getTimestamp("submitted_at"),
            rs.getBoolean("is_visible"),
            rs.getInt("client_id"),
            rs.getInt("service_request_id")
        );
    }
}
