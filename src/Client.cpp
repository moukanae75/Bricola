#include "../header/Client.h"
#include <iostream>

using namespace std;

void Client::saveToDB(MYSQL* conn) {
    if (!conn) return;
    string query = "INSERT INTO client (nom, email, mot_de_passe, telephone, adresse, date_inscription) VALUES ('" + nom + "', '" + email + "', '" + password + "', '" + telephone + "', '" + adresse + "', CURDATE())";
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Client ajoute dans la BDD !\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}

void Client::afficherDemandesClient(MYSQL* conn, const string& id_client) const {
    if (!conn) return;
    cout << "\n--- Vos Demandes Creees ---\n";
    string query = "SELECT id_demande, description_demande, statut, fk_artisan FROM demande WHERE fk_client = " + id_client;
    if (mysql_query(conn, query.c_str()) == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row; int count = 0;
            while ((row = mysql_fetch_row(res))) {
                cout << "[ID Demande: " << (row[0] ? row[0] : "NULL") << "] Desc: " << (row[1] ? row[1] : "NULL") << " | Artisan ID: " << (row[3] ? row[3] : "NULL") << " | Statut: " << (row[2] ? row[2] : "NULL") << "\n";
                count++;
            }
            if (count == 0) cout << "(Aucune demande)\n";
            mysql_free_result(res);
        }
    }
}

void Client::afficherTous(MYSQL* conn) {
    if (!conn) return;
    cout << "--- Liste des clients ---\n";
    if (mysql_query(conn, "SELECT id_client, nom, email, telephone FROM client") == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row; int count = 0;
            while ((row = mysql_fetch_row(res))) {
                cout << "[ID: " << (row[0] ? row[0] : "NULL") << "] Nom: " << (row[1] ? row[1] : "NULL") << " | Tel: " << (row[3] ? row[3] : "NULL") << "\n";
                count++;
            }
            if (count == 0) cout << "(Aucun client)\n";
            mysql_free_result(res);
        }
    }
}

void Client::supprimer(MYSQL* conn, const string& id) {
    if (!conn) return;
    string query = "DELETE FROM client WHERE id_client = " + id;
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Client supprime.\n";
    else cout << ">> ERREUR MYSQL (FK probable) : " << mysql_error(conn) << "\n";
}
