#ifndef ARTISAN_H
#define ARTISAN_H

#include "User.h"
#include "ServiceRequest.h"
#include "evaluation.h"
#include <vector>
#include <string>

class Artisan : public User {
private:
    std::string metier;
    bool is_disponible;
    double average_rating;
    std::vector<std::string> competences;

public:
    using User::User;

    // Propriétés de base
    void updateAvailability(bool status);
    bool getAvailability() const;
    void setAverageRating(double rating);
    double getAverageRating() const;

    // --- Fonctionnalités du diagramme de cas d'utilisation ---

    // 2. Accepter service
    void accepterService(ServiceRequest& request);

    // 3. Refuser service
    void refuserService(ServiceRequest& request);

    // 4. Modifier service (ex: Activer service / Désactiver service)
    void modifierService(ServiceRequest& request, ServiceStatus newStatus);

    // 5. Consulter services assignés
    void consulterServicesAssignes(const std::vector<ServiceRequest>& allRequests) const;

    // 6. Consulter évaluations
    void consulterEvaluations(const std::vector<Evaluation>& allEvaluations) const;

    // 7. Proposer compétence
    void proposerCompetence(const std::string& competence);

    const std::vector<std::string>& getCompetences() const;
};

#endif // ARTISAN_H