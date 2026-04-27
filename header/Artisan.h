#include "User.h"

class Artisan : public User {
private:
    std::string metier;
    bool is_disponible;
    double average_rating;

public:
    using User::User;
    void updateAvailability(bool status) { is_disponible = status; }
    void setAverageRating(double rating) { average_rating = rating; }
    double getAverageRating() const { return average_rating; }
};