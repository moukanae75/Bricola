#include "header/User.h"
#include <iostream>
#include <sstream>



User::User(int _id, std::string n, std::string e, std::string t) {
    id = _id;
    nom = n;
    email = e;
    telephone = t;
}
std::string to_hex(unsigned long n) {
    if (n == 0) return "0"; 
    std::string res;
    std::string base = "0123456789abcdef";
    while (n > 0)
    {
        res = base[n % 16] + res; 
        n /= 16; 
    }
    return res;
}

std::string User::hashPassword(std::string password) const {
    // Custom Hash Logic (Algorithme DJB2 modifié pour la demo)
    unsigned long hash = 5381;
    for (char c : password) { // :
        hash = ((hash * 32) + hash) + c; /* hash * 32 + c */
    }
    
    return to_hex(hash);
}



void User::registerUser(std::string pwd) {
    this->password = hashPassword(pwd);
}

bool User::login(std::string inputEmail, std::string inputPassword) {
    std::string hashedInput = hashPassword(inputPassword);
    return (this->email == inputEmail && this->password == hashedInput);
}
