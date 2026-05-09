#ifndef USER_H
#define USER_H

#include <string>
#include "mysql.h"

class User {
protected:
    int id;
    std::string nom;
    std::string email;
    std::string password;
    std::string telephone;

public:
    User(int id, std::string n, std::string e, std::string t);

    bool login(std::string email, std::string password); // old method
    void registerUser(std::string password);
    void setHashedPassword(std::string hashed) { this->password = hashed; }
    
    std::string hashPassword(std::string password) const;

    int getId() const { return id; }
    std::string getNom() const { return nom; }
    std::string getEmail() const { return email; }
    std::string getTelephone() const { return telephone; }
    std::string getPassword() const { return password; }

    static bool loginUser(MYSQL* conn, const std::string& email, const std::string& password_input, std::string& out_role, std::string& out_id);
};

#endif