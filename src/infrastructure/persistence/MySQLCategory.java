package infrastructure.persistence;

import domain.entities.Category;
import domain.repositories.ICategoryReader;
import domain.repositories.ICategoryWriter;

import java.sql.*;
import java.util.ArrayList;
import java.util.List;

public class MySQLCategory implements ICategoryWriter, ICategoryReader {

    private final Connection connection;

    public MySQLCategory(Connection connection) {
        this.connection = connection;
    }

    @Override
    public Category findById(Integer id) {
        String query = "SELECT * FROM category WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            ResultSet rs = stmt.executeQuery();
            if (rs.next()) {
                return mapResultSetToCategory(rs);
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public List<Category> findAll() {
        List<Category> categories = new ArrayList<>();
        String query = "SELECT * FROM category";
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) {
                categories.add(mapResultSetToCategory(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return categories;
    }

    @Override
    public List<Category> findPending() {
        List<Category> categories = new ArrayList<>();
        String query = "SELECT * FROM category WHERE is_pending = 1";
        try (Statement stmt = connection.createStatement();
             ResultSet rs = stmt.executeQuery(query)) {
            while (rs.next()) {
                categories.add(mapResultSetToCategory(rs));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return categories;
    }

    @Override
    public void save(Category category) {
        String query = "INSERT INTO category (name, is_approved, is_pending) VALUES (?, ?, ?)";
        try (PreparedStatement stmt = connection.prepareStatement(query, Statement.RETURN_GENERATED_KEYS)) {
            stmt.setString(1, category.getName());
            stmt.setBoolean(2, Boolean.TRUE.equals(category.getIsApproved()));
            stmt.setBoolean(3, Boolean.TRUE.equals(category.getIsPending()));
            stmt.executeUpdate();
            ResultSet generatedKeys = stmt.getGeneratedKeys();
            if (generatedKeys.next()) {
                category.setId(generatedKeys.getInt(1));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void update(Category category) {
        String query = "UPDATE category SET name = ?, is_approved = ?, is_pending = ? WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setString(1, category.getName());
            stmt.setBoolean(2, Boolean.TRUE.equals(category.getIsApproved()));
            stmt.setBoolean(3, Boolean.TRUE.equals(category.getIsPending()));
            stmt.setInt(4, category.getId());
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void delete(Integer id) {
        String query = "DELETE FROM category WHERE id = ?";
        try (PreparedStatement stmt = connection.prepareStatement(query)) {
            stmt.setInt(1, id);
            stmt.executeUpdate();
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    private Category mapResultSetToCategory(ResultSet rs) throws SQLException {
        return new Category(
            rs.getInt("id"),
            rs.getString("name"),
            rs.getBoolean("is_approved"),
            rs.getBoolean("is_pending")
        );
    }
}
