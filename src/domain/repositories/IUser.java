package domain.repositories;

import domain.entities.User;
import java.util.List;

public interface IUser {
    User findById(Integer id);
    List<User> findAll();
    void save(User user);
    void update(User user);
    void delete(Integer id);
    List<User> findActive();
}
