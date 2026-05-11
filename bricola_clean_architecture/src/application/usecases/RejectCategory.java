package application.usecases;

import domain.entities.Category;
import domain.repositories.ICategoryReader;
import domain.repositories.ICategoryWriter;

public class RejectCategory {

    private final ICategoryWriter categoryRepositoryW;
    private final ICategoryReader categoryRepositoryR;

    public RejectCategory(ICategoryWriter categoryRepositoryW, ICategoryReader categoryRepositoryR) {
        this.categoryRepositoryW = categoryRepositoryW;
        this.categoryRepositoryR = categoryRepositoryR;
    }

    public void execute(Integer categoryId) {
        Category category = categoryRepositoryR.findById(categoryId);
        if (category != null) {
            category.reject();
            categoryRepositoryW.update(category);
        }
    }
}
