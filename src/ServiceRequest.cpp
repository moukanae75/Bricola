#include "header/ServiceRequest.h"

ServiceRequest::ServiceRequest() : id(0), status(ServiceStatus::PENDING), clientID(0), artisanID(0) {}

void ServiceRequest::updateStatus(ServiceStatus newStatus) {
    status = newStatus;
}

void ServiceRequest::cancelRequest() {
    status = ServiceStatus::CANCELLED;
}
