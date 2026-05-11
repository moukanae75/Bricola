package application.usecases;

import domain.entities.Category;
import domain.repositories.ICategoryReader;
import domain.repositories.ICategoryWriter;

public class ApproveCategory {

    private final ICategoryWriter categoryRepositoryW;
    private final ICategoryReader categoryRepositoryR;

    public ApproveCategory(ICategoryWriter categoryRepositoryW, ICategoryReader categoryRepositoryR) {
        this.categoryRepositoryW = categoryRepositoryW;
        this.categoryRepositoryR = categoryRepositoryR;
    }

    public void execute(Integer categoryId) {
        Category category = categoryRepositoryR.findById(categoryId);
        if (category != null) {
            category.approve();
            categoryRepositoryW.update(category);
        }
    }
}
