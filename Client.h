#include "User.h"
#include <vector>

class Client : public User {
private:
    std::string adresse;
    std::string date_inscription;

public:
    using User::User; 
    void setAdresse(std::string addr) { adresse = addr; }
};