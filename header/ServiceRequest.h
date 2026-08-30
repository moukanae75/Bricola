#ifndef SERVICEREQUEST_H
#define SERVICEREQUEST_H

#include <string>
#include "mysql.h"

enum class ServiceStatus {CONFIRMED,REFUSED,PENDING,COMPLETE,CANCELLED};

class ServiceRequest {
private:
    int id;
    ServiceStatus status;
    std::string description;
    std::string clientID;
    std::string artisanID;

public:
    ServiceRequest(std::string desc, std::string cID, std::string aID);
 
    void updateStatus(ServiceStatus newStatus, MYSQL* conn);
    void cancelRequest(); 
    void saveToDB(MYSQL* conn);
    void setId(int id) { this->id = id; }
    
    ServiceStatus getStatus() const { return status; }
};

#endif