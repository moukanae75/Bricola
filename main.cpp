#include <iostream>
#include <vector>
#include <string>
#include <windows.h>
#include "mysql.h"

using namespace std;

// Fonction de nettoyage rapide du buffer pour eviter les problemes de getline()
void clearBuffer() {
    if(cin.peek() == '\n') cin.ignore();
}

int main() {
    // ---- 1. Connexion a la Base de Donnees ----
    MYSQL* conn;
    conn = mysql_init(0);
    conn = mysql_real_connect(conn, "localhost", "root", "", "bricola", 3306, NULL, 0);

    int choix;

    do {
        // --- Affichage du Menu ---
        cout << "\n=======================================================\n";
        cout << "         Systeme de Gestion Bricola      \n";
        cout << "=======================================================\n";
        if (conn) cout << "[+] Connecte a la base de donnees MYSQL ('bricola')\n";
        else      cout << "[-] Erreur : Non connecte a MYSQL !\n";
        cout << "=======================================================\n";
        
        cout << "1. Ajouter un client\n";
        cout << "2. Ajouter un artisan\n";
        cout << "3. Modifier l'etat d'un artisan (dispo / pas dispo)\n";
        cout << "4. Creer une demande de service\n";
        cout << "5. Ajouter une evaluation a un artisan\n";
        cout << "6. Afficher les clients et les artisans\n";
        cout << "7. Supprimer un client (par ID)\n";
        cout << "8. Supprimer un artisan (par ID)\n";
        cout << "0. Quitter\n";
        cout << "=======================================================\n";
        cout << "Choisissez une operation : ";
        cin >> choix;

        cout << "\n";

        if (choix == 1) { // Ajouter un client
            string nom, email, mot_de_passe, telephone, adresse;
            clearBuffer();
            cout << "Entrez le nom : "; getline(cin, nom);
            cout << "Entrez l'email : "; getline(cin, email);
            cout << "Entrez le mot de passe : "; getline(cin, mot_de_passe);
            cout << "Entrez le telephone : "; getline(cin, telephone);
            cout << "Entrez l'adresse : "; getline(cin, adresse);

            if (conn) {
                string query = "INSERT INTO client (nom, email, mot_de_passe, telephone, adresse, date_inscription) "
                               "VALUES ('" + nom + "', '" + email + "', '" + mot_de_passe + "', '" + telephone + "', '" + adresse + "', CURDATE())";
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : Client ajoute dans la BDD !\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 2) { // Ajouter un artisan
            // Note: D'après votre bricola.sql, la table artisan n'a pas (nom, email), elle a juste (metier, dispo)
            string metier;
            clearBuffer();
            cout << "Entrez le metier de cet artisan (ex: Peintre, Plombier) : "; 
            getline(cin, metier);

            if (conn) {
                string query = "INSERT INTO artisan (metier_artisan, dispo_artisan) VALUES ('" + metier + "', 'disponible')";
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : Artisan ajoute dans la BDD et marque comme (disponible) !\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 3) { // Modifier état artisan
            string id_artisan, etat;
            cout << "ID de l'artisan a modifier : "; cin >> id_artisan;
            cout << "Nouvel etat (1 = disponible, 2 = pas disponible) : "; cin >> etat;
            
            string dispo = (etat == "1") ? "disponible" : "pas disponible";

            if (conn) {
                string query = "UPDATE artisan SET dispo_artisan = '" + dispo + "' WHERE id_artisan = " + id_artisan;
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : L'etat de l'artisan " << id_artisan << " est devenu (" << dispo << ").\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 4) { // Créer demande
            string desc, id_client, id_artisan;
            clearBuffer();
            cout << "Description bref de la demande : "; getline(cin, desc);
            cout << "ID du client demandeur : "; cin >> id_client;
            cout << "ID de l'artisan requis : "; cin >> id_artisan;

            if (conn) {
                // statut enum('confirmer', 'refuser', 'en cours', 'complete')
                string query = "INSERT INTO demande (date_creation_demande, statut, description_demande, fk_client, fk_artisan) "
                               "VALUES (CURDATE(), 'en cours', '" + desc + "', " + id_client + ", " + id_artisan + ")";
                
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : Demande cree avec l'etat (en cours).\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n"
                         << "   * Astuce: Verifiez que les ID client et artisan existent vraiment pour eviter le blocage des cles etrangeres (Foreign Keys)!\n";
                }
            }
        }
        else if (choix == 5) { // Ajouter evaluation
            string note, comm, id_client, id_artisan;
            cout << "ID du client evaluant : "; cin >> id_client;
            cout << "ID de l'artisan evalue : "; cin >> id_artisan;
            
            clearBuffer();
            cout << "Commentaire : "; getline(cin, comm);
            cout << "Note (entre 0 et 5) : "; cin >> note;

            if (conn) {
                string query = "INSERT INTO evaluation (note_evaluation, commentaire, date_evaluation, fk_client, fk_artisan) "
                               "VALUES (" + note + ", '" + comm + "', CURDATE(), " + id_client + ", " + id_artisan + ")";
                
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : Evaluation enregistree !\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 6) { // Lecture des données clients + artisans
            if (conn) {
                // ---- LECTURE DES CLIENTS ----
                cout << "--- Liste des clients ---\n";
                if (mysql_query(conn, "SELECT id_client, nom, email, telephone FROM client") == 0) {
                    MYSQL_RES* res = mysql_store_result(conn);
                    if (res) {
                        MYSQL_ROW row;
                        int count = 0;
                        while ((row = mysql_fetch_row(res))) {
                            cout << "[ID: " << (row[0] ? row[0] : "NULL") 
                                 << "]  Nom: " << (row[1] ? row[1] : "NULL")
                                 << "  | Email: " << (row[2] ? row[2] : "NULL") 
                                 << "  | Tel: " << (row[3] ? row[3] : "NULL") << "\n";
                            count++;
                        }
                        if (count == 0) cout << "(Aucun client enregistre)\n";
                        mysql_free_result(res);
                    }
                }

                // ---- LECTURE DES ARTISANS ----
                cout << "\n--- Liste des artisans ---\n";
                if (mysql_query(conn, "SELECT id_artisan, metier_artisan, dispo_artisan FROM artisan") == 0) {
                    MYSQL_RES* res = mysql_store_result(conn);
                    if (res) {
                        MYSQL_ROW row;
                        int count = 0;
                        while ((row = mysql_fetch_row(res))) {
                            cout << "[ID: " << (row[0] ? row[0] : "NULL") 
                                 << "]  Metier: " << (row[1] ? row[1] : "NULL")
                                 << "  | Etat: " << (row[2] ? row[2] : "NULL") << "\n";
                            count++;
                        }
                        if (count == 0) cout << "(Aucun artisan enregistre)\n";
                        mysql_free_result(res);
                    }
                }
            }
        }
        else if (choix == 7) { // Supprimer Client
            string id_client;
            cout << "Entrez l'ID du client a supprimer : "; cin >> id_client;

            if (conn) {
                string query = "DELETE FROM client WHERE id_client = " + id_client;
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : Le client avec l'ID " << id_client << " a ete supprime.\n";
                } else {
                    cout << ">> ERREUR MYSQL : Impossible de supprimer ce client.\n"
                         << "   Raison probable: Ce client a deja une demande (Demande) ou une evaluation, veuillez les supprimer d'abord (Foreign Key Restraint).\n"
                         << "   Erreur Technique: " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 8) { // Supprimer Artisan
            string id_artisan;
            cout << "Entrez l'ID de l'artisan a supprimer : "; cin >> id_artisan;

            if (conn) {
                string query = "DELETE FROM artisan WHERE id_artisan = " + id_artisan;
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES : L'artisan avec l'ID " << id_artisan << " a ete supprime.\n";
                } else {
                    cout << ">> ERREUR MYSQL : Impossible de supprimer cet artisan.\n"
                         << "   Raison probable: Cet artisan a deja recu une demande ou une evaluation.\n"
                         << "   Erreur Technique: " << mysql_error(conn) << "\n";
                }
            }
        }

    } while (choix != 0);

    // Fermeture de la connexion
    if (conn) {
        mysql_close(conn);
        cout << "[!] Connexion MYSQL terminee.\n";
    }

    cout << "A bientot !\n";
    return 0;
}
