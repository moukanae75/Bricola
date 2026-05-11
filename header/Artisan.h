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

    void setMetier(std::string m) { metier = m; } // apres l'inscription
    void updateAvailability(bool status, MYSQL* conn, const std::string& id_artisan);
    bool getAvailability() const;
    //void setAverageRating(double rating);
    double getAverageRating() const;

    void saveToDB(MYSQL* conn); // apres l'inscription
    void accepterService(ServiceRequest& request, MYSQL* conn);
    void refuserService(ServiceRequest& request, MYSQL* conn);
    void consulterServicesAssignes(MYSQL* conn, const std::string& id_artisan) const;
    void proposerCompetence(const std::string& competence, MYSQL* conn, const std::string& id_artisan);
    double fetchAverageRating(MYSQL* conn, const std::string& id_artisan);

    static void afficherTous(MYSQL* conn);
    static void supprimer(MYSQL* conn, const std::string& id);
};

#endif