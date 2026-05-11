package domain.repositories;

import domain.entities.Artisan;


public interface IArtisanWriter {
    void save(Artisan artisan);
    /** Updates the artisan record in the database. */
    void update(Artisan artisan);
    void delete(Integer id);
}
