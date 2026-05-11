#include "../header/Artisan.h"
#include <iostream>

using namespace std;

void Artisan::saveToDB(MYSQL* conn) {
    if (!conn) return;
    string query = "INSERT INTO artisan (nom, email, mot_de_passe, telephone, metier_artisan, dispo_artisan) VALUES ('" + nom + "', '" + email + "', '" + password + "', '" + telephone + "', '" + metier + "', 'disponible')";
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Artisan ajoute !\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}

void Artisan::updateAvailability(bool status, MYSQL* conn, const string& id_artisan) {
    is_disponible = status;
    if (!conn) return;
    string dispo = status ? "disponible" : "pas disponible";
    string query = "UPDATE artisan SET dispo_artisan = '" + dispo + "' WHERE id_artisan = " + id_artisan;
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Etat modifie en (" << dispo << ").\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}

bool Artisan::getAvailability() const { return is_disponible; }

//void Artisan::setAverageRating(double rating) { average_rating = rating; }
double Artisan::getAverageRating() const { return average_rating; }

void Artisan::accepterService(ServiceRequest& request, MYSQL* conn) {
    request.updateStatus(ServiceStatus::CONFIRMED, conn);
    cout << "Service accepte par l'artisan.\n";
}

void Artisan::refuserService(ServiceRequest& request, MYSQL* conn) {
    request.updateStatus(ServiceStatus::REFUSED, conn);
    cout << "Service refuse par l'artisan.\n";
}

void Artisan::consulterServicesAssignes(MYSQL* conn, const string& id_artisan) const {
    if (!conn) return;
    cout << "\n--- Vos Demandes Assigneess ---\n";
    string query = "SELECT id_demande, description_demande, statut FROM demande WHERE fk_artisan = " + id_artisan;
    if (mysql_query(conn, query.c_str()) == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row; int count = 0;
            while ((row = mysql_fetch_row(res))) {
                cout << "[ID Demande: " << (row[0] ? row[0] : "NULL") << "] Desc: " << (row[1] ? row[1] : "NULL") << " | Statut: " << (row[2] ? row[2] : "NULL") << "\n";
                count++;
            }
            if (count == 0) cout << "(Aucune demande)\n";
            mysql_free_result(res);
        }
    }
}

void Artisan::proposerCompetence(const string& competence, MYSQL* conn, const string& id_artisan) {
    competences.push_back(competence);
    if (!conn) return;
    string query = "INSERT INTO competence_artisan (nom_competence, fk_artisan) VALUES ('" + competence + "', " + id_artisan + ")";
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Competence ajoutee !\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}

double Artisan::fetchAverageRating(MYSQL* conn, const string& id_artisan) {
    if (!conn) return 0.0;
    string query = "SELECT AVG(note_evaluation) FROM evaluation WHERE fk_artisan = " + id_artisan;
    if (mysql_query(conn, query.c_str()) == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row = mysql_fetch_row(res);
            double avg = 0.0;
            if (row && row[0]) avg = std::stod(row[0]);
            mysql_free_result(res);
            this->average_rating = avg;
            return avg;
        }
    }
    return 0.0;
}

void Artisan::afficherTous(MYSQL* conn) {
    if (!conn) return;
    cout << "\n--- Liste des artisans ---\n";
    if (mysql_query(conn, "SELECT id_artisan, nom, email, telephone, metier_artisan, dispo_artisan FROM artisan") == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row; int count = 0;
            while ((row = mysql_fetch_row(res))) {
                cout << "[ID: " << (row[0] ? row[0] : "NULL") << "] Nom: " << (row[1] ? row[1] : "NULL") << " | Metier: " << (row[4] ? row[4] : "NULL") << " | Etat: " << (row[5] ? row[5] : "NULL") << "\n";
                count++;
            }
            if (count == 0) cout << "(Aucun artisan)\n";
            mysql_free_result(res);
        }
    }
}

void Artisan::supprimer(MYSQL* conn, const string& id) {
    if (!conn) return;
    string query = "DELETE FROM artisan WHERE id_artisan = " + id;
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Artisan supprime.\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}
