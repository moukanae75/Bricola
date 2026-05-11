package application.usecases;

import domain.entities.Media;
import domain.entities.ServiceRequest;
import domain.repositories.IServiceRequest;

public class AttachMediaToRequest {

    private final IServiceRequest serviceRequestRepository;

    public AttachMediaToRequest(IServiceRequest serviceRequestRepository) {
        this.serviceRequestRepository = serviceRequestRepository;
    }

    public void execute(Integer requestId, Media media) {
        ServiceRequest request = serviceRequestRepository.findById(requestId);
        if (request != null) {

            System.out.println("Media attached to request #" + requestId + ": " + media.getUrl());
            serviceRequestRepository.update(request);
        }
    }
}
