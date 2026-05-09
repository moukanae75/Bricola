#include "../header/evaluation.h"
#include <iostream>

using namespace std;

Evaluation::Evaluation(string n, string c, string clientID, string artisanID) : note(n), commentaire(c), fk_client(clientID), fk_artisan(artisanID), id(0) {}

void Evaluation::saveToDB(MYSQL* conn) {
    if (!conn) return;
    string query = "INSERT INTO evaluation (note_evaluation, commentaire, date_evaluation, fk_client, fk_artisan) VALUES (" + note + ", '" + commentaire + "', CURDATE(), " + fk_client + ", " + fk_artisan + ")";
    if (mysql_query(conn, query.c_str()) == 0) cout << ">> SUCCES : Evaluation enregistree !\n";
    else cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
}
