package domain.repositories;

import domain.entities.ServiceRequest;
import java.time.LocalDate;
import java.util.List;

public interface IServiceRequest {
    ServiceRequest findById(Integer id);
    List<ServiceRequest> findAll();
    List<ServiceRequest> findByStatus(ServiceRequest.ServiceStatus status);
    void save(ServiceRequest request);
    void update(ServiceRequest request);
    void delete(Integer id);
    List<ServiceRequest> findPending();
    LocalDate lastActivityDate(Integer userId);
    int countByUser(Integer userId);
    boolean hadLowRatingRecently(Integer userId, int withinDays);
    LocalDate lastRequestDate(Integer userId);
    double averagePriceByCategory(int categoryId);
    double avgMonthlyBookingsByCategory(int categoryId);
}
