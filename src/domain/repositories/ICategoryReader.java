package domain.repositories;

import domain.entities.Category;
import java.util.List;

public interface ICategoryReader {
    Category findById(Integer id);
    List<Category> findAll();
    List<Category> findPending();
}
