#ifndef USER_H
#define USER_H


#include <string>
class User {
protected:
    int id;
    std::string nom;
    std::string email;
    std::string password;//hashed pass a discuter !!!!!!(also algo of hashing)
    std::string telephone;

public:
    User(int id, std::string n, std::string e, std::string t);
    //virtual ~User() = default;

    bool login(std::string email, std::string password);
    void registerUser(std::string password);
    
   // std::string hashPassword(std::string password);

    int getId() const { return id; }
    std::string getEmail() const { return email; }
    std::string getTelephone() const { return telephone; }
};

#endif