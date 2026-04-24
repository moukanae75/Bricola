#include <vector>
#include <string>

enum class ServiceStatus {CONFIRMED,REFUSED,PENDING,COMPLETE,CANCELLED};


class ServiceRequest {
private:
    int id;
    ServiceStatus status;
    std::string description;
    int clientID;
    int artisanID;

public:
    ServiceRequest();
 
    void updateStatus(ServiceStatus newStatus);
    void cancelRequest(); 
    
    ServiceStatus getStatus() const { return status; }
};