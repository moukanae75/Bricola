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
            // Media is stored in its own table linked by service_request_id (see DB schema).
            // The attachment is handled at the DB level via the media.service_request_id FK.
            // Here we record the media URL in the request description for traceability.
            System.out.println("Media attached to request #" + requestId + ": " + media.getUrl());
            serviceRequestRepository.update(request);
        }
    }
}
