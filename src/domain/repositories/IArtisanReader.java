package domain.repositories;

import domain.entities.Artisan;
import java.util.List;

public interface IArtisanReader {
    Artisan findById(Integer id);
    List<Artisan> findAll();
    List<Artisan> findByCity(String city);
    List<Artisan> findAvailable();
    /** Returns verified artisans who have worked in the given category. */
    List<Artisan> findVerifiedByCategory(Integer categoryId);
}
