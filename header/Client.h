#ifndef CLIENT_H
#define CLIENT_H

#include "User.h"
#include <string>

class Client : public User {
private:
    std::string adresse;
    std::string date_inscription;

public:
    using User::User; 
    void setAdresse(std::string addr) { adresse = addr; }
    
    void saveToDB(MYSQL* conn);
    void afficherDemandesClient(MYSQL* conn, const std::string& id_client) const;
    
    static void afficherTous(MYSQL* conn);
    static void supprimer(MYSQL* conn, const std::string& id);
};

#endif