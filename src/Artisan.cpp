#include "../header/Artisan.h"
#include <iostream>

void Artisan::updateAvailability(bool status) {
    is_disponible = status;
}

bool Artisan::getAvailability() const {
    return is_disponible;
}

void Artisan::setAverageRating(double rating) {
    average_rating = rating;
}

double Artisan::getAverageRating() const {
    return average_rating;
}

void Artisan::accepterService(ServiceRequest& request) {
    request.updateStatus(ServiceStatus::CONFIRMED);
    std::cout << "Service accepte par l'artisan : " << nom << std::endl;
}

void Artisan::refuserService(ServiceRequest& request) {
    request.updateStatus(ServiceStatus::REFUSED);
    std::cout << "Service refuse par l'artisan : " << nom << std::endl;
}

void Artisan::modifierService(ServiceRequest& request, ServiceStatus newStatus) {
    request.updateStatus(newStatus);
    std::cout << "Statut du service modifie par l'artisan : " << nom << std::endl;
}

void Artisan::consulterServicesAssignes(const std::vector<ServiceRequest>& allRequests) const {
    std::cout << "--- Liste des services assignes a l'artisan " << nom << " (ID: " << id << ") ---" << std::endl;
    // Ici, vous pouvez ajouter une boucle sur allRequests pour filtrer par this->id 
    // et afficher les details de la demande.
}

void Artisan::consulterEvaluations(const std::vector<Evaluation>& allEvaluations) const {
    std::cout << "--- Evaluations de l'artisan " << nom << " (ID: " << id << ") ---" << std::endl;
    // Ici, vous pouvez ajouter une boucle sur allEvaluations pour filtrer par this->id 
    // et afficher la note et le commentaire.
}

void Artisan::proposerCompetence(const std::string& competence) {
    competences.push_back(competence);
    std::cout << "L'artisan " << nom << " a ajoute une nouvelle competence : " << competence << std::endl;
}

const std::vector<std::string>& Artisan::getCompetences() const {
    return competences;
}
