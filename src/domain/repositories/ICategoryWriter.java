package domain.repositories;

import domain.entities.Category;

public interface ICategoryWriter {
    void save(Category category);
    void update(Category category);
    void delete(Integer id);
}
