#include "header/evaluation.h"

Evaluation::Evaluation(double n, std::string c, int clientID, int artisanID)
    : id(0), note(n), commentaire(c), fk_client(clientID), fk_artisan(artisanID) {
    if (note < 0.0) note = 0.0;
    if (note > 5.0) note = 5.0;
}

double Evaluation::computeArtisanAvg(const std::vector<Evaluation>& evals) {
    if(evals.empty()) return 0.0;
    double sum = 0.0;
    for(const auto& e : evals) {
        sum += e.getNote();
    }
    return sum / evals.size();
}
