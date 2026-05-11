package application.usecases;

import domain.entities.ServiceRequest;
import domain.repositories.IServiceRequest;

public class GetServiceStatus {

    private final IServiceRequest serviceRequestRepository;

    public GetServiceStatus(IServiceRequest serviceRequestRepository) {
        this.serviceRequestRepository = serviceRequestRepository;
    }

    public ServiceRequest.ServiceStatus execute(Integer requestId) {
        ServiceRequest request = serviceRequestRepository.findById(requestId);
        return request != null ? request.getStatus() : null;
    }
}
