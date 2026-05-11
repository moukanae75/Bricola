package application.usecases;

import domain.entities.Artisan;
import domain.entities.Evaluation;
import domain.repositories.IArtisanReader;
import domain.repositories.IArtisanWriter;
import domain.repositories.IEvaluation;

import java.util.List;

public class ComputeArtisanAvgRating {

    private final IArtisanReader artisanRepositoryR;
    private final IArtisanWriter artisanRepositoryW;
    private final IEvaluation evaluationRepository;

    public ComputeArtisanAvgRating(IArtisanReader artisanRepositoryR,
                                    IArtisanWriter artisanRepositoryW,
                                    IEvaluation evaluationRepository) {
        this.artisanRepositoryR = artisanRepositoryR;
        this.artisanRepositoryW = artisanRepositoryW;
        this.evaluationRepository = evaluationRepository;
    }

    public Float execute(Integer artisanId) {
        List<Evaluation> evaluations = evaluationRepository.findByArtisanId(artisanId);
        if (evaluations.isEmpty()) return 0.0f;

        float sum = 0;
        for (Evaluation eval : evaluations) {
            sum += eval.getRating();
        }
        float avg = sum / evaluations.size();

        Artisan artisan = artisanRepositoryR.findById(artisanId);
        if (artisan != null) {
            artisan.setAverageRating(avg);
            // Fixed: pass the full Artisan object (not just the ID) to match IArtisanWriter.update(Artisan)
            artisanRepositoryW.update(artisan);
        }

        return avg;
    }
}
