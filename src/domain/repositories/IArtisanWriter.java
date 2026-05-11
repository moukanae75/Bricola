package domain.repositories;

import domain.entities.Artisan;


public interface IArtisanWriter {
    void save(Artisan artisan);
    void update(Artisan artisan);
    void delete(Integer id);
}
