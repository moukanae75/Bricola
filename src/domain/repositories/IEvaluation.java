package domain.repositories;

import domain.entities.Evaluation;
import java.util.List;

public interface IEvaluation {
    Evaluation findById(Integer id);
    List<Evaluation> findAll();
    List<Evaluation> findByArtisanId(Integer artisanId);
    void save(Evaluation evaluation);
    void update(Evaluation evaluation);
    void delete(Integer id);
    /** Returns the number of evaluations for the given artisan in the given category. */
    double countByArtisanAndCategory(Integer artisanId, Integer categoryId);
}
