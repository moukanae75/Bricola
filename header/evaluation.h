#ifndef EVALUATION_H
#define EVALUATION_H

#include <vector>
#include <string>
#include "mysql.h"

class Evaluation {
private:
    int id;
    std::string note; 
    std::string commentaire;
    std::string fk_client;
    std::string fk_artisan;

public:
    Evaluation(std::string n, std::string c, std::string clientID, std::string artisanID);

    void saveToDB(MYSQL* conn);
    
    std::string getNote() const { return note; }
};

#endif