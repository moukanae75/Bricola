package application.usecases;

import domain.entities.Artisan;
import domain.repositories.IArtisanReader;
import domain.repositories.IArtisanWriter;

public class VerifyArtisan {

    private final IArtisanReader artisanRepositoryR;
    private final IArtisanWriter artisanRepositoryW;

    public VerifyArtisan(IArtisanReader artisanRepositoryR, IArtisanWriter artisanRepositoryW) {
        this.artisanRepositoryR = artisanRepositoryR;
        this.artisanRepositoryW = artisanRepositoryW;
    }

    public boolean execute(Integer artisanId) {
        Artisan artisan = artisanRepositoryR.findById(artisanId);
        if (artisan == null) {
            return false;
        }
        artisan.verify();
        // Fixed: pass the full Artisan object to match IArtisanWriter.update(Artisan)
        artisanRepositoryW.update(artisan);
        return true;
    }
}
