#include "../header/ServiceRequest.h"
#include <iostream>

using namespace std;

ServiceRequest::ServiceRequest(string desc, string cID, string aID) : description(desc), clientID(cID), artisanID(aID), status(ServiceStatus::PENDING), id(0) {}

void ServiceRequest::updateStatus(ServiceStatus newStatus, MYSQL* conn) {
    status = newStatus;
    if (!conn) return;
    
    string statusStr = "en cours";
    if (newStatus == ServiceStatus::CONFIRMED) statusStr = "confirmer";
    else if (newStatus == ServiceStatus::REFUSED) statusStr = "refuser";
    
    string query = "UPDATE demande SET statut = '" + statusStr + "' WHERE id_demande = " + std::to_string(id);
    if (mysql_query(conn, query.c_str()) == 0) {
        cout << ">> SUCCES : Statut mis a jour (" << statusStr << ").\n";
    } else {
        cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
    }
}

void ServiceRequest::cancelRequest() {
    status = ServiceStatus::CANCELLED;
}

void ServiceRequest::saveToDB(MYSQL* conn) {
    if (!conn) return;
    string query = "INSERT INTO demande (date_creation_demande, statut, description_demande, fk_client, fk_artisan) VALUES (CURDATE(), 'en cours', '" + description + "', " + clientID + ", " + artisanID + ")";
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Demande cree avec l'etat (en cours).\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}
