#include <iostream>
#include <vector>
#include <string>
#include <windows.h>
#include "mysql.h"
#include "header/User.h"
#include "header/Client.h"
#include "header/Artisan.h"
#include "header/evaluation.h"
#include "header/ServiceRequest.h"
#include "header/Media.h" // Integration des classes originales C++

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
        cout << "9. Se connecter (Login)\n";
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

            // ---- INTEGRATION OOP (Test en memoire) ----
            Client nouveauClient(0, nom, email, telephone);
            nouveauClient.setAdresse(adresse);
            nouveauClient.registerUser(mot_de_passe);
            cout << "\n>> [OOP] Test de la Classe Client avec heritage et register:\n";
            cout << "[Client] Nom: " << nom << " | Tel: " << nouveauClient.getTelephone() << "\n";
            cout << ">> [OOP] Test d'authentification Hash (" << email << ") : " 
                 << (nouveauClient.login(email, mot_de_passe) ? "Succes" : "Echec") << "\n\n";
            // -------------------------------------------

            if (conn) {
                string query = "INSERT INTO client (nom, email, mot_de_passe, telephone, adresse, date_inscription) "
                               "VALUES ('" + nom + "', '" + email + "', '" + nouveauClient.getPassword() + "', '" + telephone + "', '" + adresse + "', CURDATE())";
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES (MySQL) : Client ajoute dans la BDD !\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 2) { // Ajouter un artisan
            string metier;
            clearBuffer();
            cout << "Entrez le metier de cet artisan (ex: Peintre, Plombier) : "; 
            getline(cin, metier);

            // ---- INTEGRATION OOP (Test en memoire) ----
            Artisan nouvelArtisan(0, "Nouveau Artisan", "artisan@mail.com", "060000");
            nouvelArtisan.updateAvailability(true);
            cout << "\n>> [OOP] Test de la Classe Artisan: Artisan en memoire !\n";
            cout << "\n";
            // -------------------------------------------

            if (conn) {
                string query = "INSERT INTO artisan (metier_artisan, dispo_artisan) VALUES ('" + metier + "', 'disponible')";
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES (MySQL) : Artisan ajoute dans la BDD et marque comme (disponible) !\n";
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

            // ---- INTEGRATION OOP (Test en memoire) ----
            Artisan objArtisan(stoi(id_artisan), "Nom", "Email", "tel");
            objArtisan.updateAvailability((etat == "1"));
            cout << "\n>> [OOP] Mise a jour Etat Artisan en memoire terminee\n";
            cout << "\n";
            // -------------------------------------------

            if (conn) {
                string query = "UPDATE artisan SET dispo_artisan = '" + dispo + "' WHERE id_artisan = " + id_artisan;
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES (MySQL) : L'etat de l'artisan " << id_artisan << " est devenu (" << dispo << ").\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 4) { // Créer demande
            string desc, id_client, id_artisan, nomFichier;
            int typeMedia;
            clearBuffer();
            cout << "Description bref de la demande : "; getline(cin, desc);
            cout << "ID du client demandeur : "; cin >> id_client;
            cout << "ID de l'artisan requis : "; cin >> id_artisan;
            clearBuffer();
            cout << "Nom du fichier a joindre (ex: photo_panne.jpg) : "; getline(cin, nomFichier);
            cout << "Type du fichier (1=IMAGE, 2=VIDEO, 3=DOCUMENT) : "; cin >> typeMedia;

            // Determiner le type du media
            MediaType mt = MediaType::IMAGE;
            if (typeMedia == 2) mt = MediaType::VIDEO;
            else if (typeMedia == 3) mt = MediaType::DOCUMENT;

            // ---- INTEGRATION OOP ----
            int c_id = 0, a_id = 0;
            try { c_id = stoi(id_client); a_id = stoi(id_artisan); } catch(...) {}

            ServiceRequest req;
            req.updateStatus(ServiceStatus::PENDING);

            

            // Afficher les informations de la demande
            cout << "\n=== Recapitulatif de la demande ===\n";
            cout << "Description  : " << desc << "\n";
            cout << "Client ID    : " << id_client << "\n";
            cout << "Artisan ID   : " << id_artisan << "\n";
            cout << "Statut       : En cours\n";
            cout << "Fichier      : " << nomFichier << "\n";
            cout << "URL Media    : " << m.getUrl() << "\n";
            cout << "===================================\n\n";
            // -------------------------------------------

            if (conn) {
                string query = "INSERT INTO demande (date_creation_demande, statut, description_demande, fk_client, fk_artisan) "
                               "VALUES (CURDATE(), 'en cours', '" + desc + "', " + id_client + ", " + id_artisan + ")";
                
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES (MySQL) : Demande cree avec l'etat (en cours).\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n"
                         << "   * Astuce: Verifiez que les ID client et artisan existent vraiment.\n";
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

            // ---- INTEGRATION OOP (Test en memoire) ----
            float n = 0.0f; int c_id = 0, a_id = 0;
            try { n = stof(note); c_id = stoi(id_client); a_id = stoi(id_artisan); } catch(...) {}
            Evaluation eval(n, comm, c_id, a_id);
            cout << "\n>> [OOP] Test Evaluation cree en memoire: Note: " << eval.getNote() << "/5 \n";
            
            std::vector<Evaluation> evalList; // STL vector test
            evalList.push_back(eval);
            
            cout << ">> [OOP] Note moyenne temporelle calculée static : " << Evaluation::computeArtisanAvg(evalList) << "/5\n\n";
            // -------------------------------------------

            if (conn) {
                string query = "INSERT INTO evaluation (note_evaluation, commentaire, date_evaluation, fk_client, fk_artisan) "
                               "VALUES (" + note + ", '" + comm + "', CURDATE(), " + id_client + ", " + id_artisan + ")";
                
                if (mysql_query(conn, query.c_str()) == 0) {
                    cout << ">> SUCCES (MySQL) : Evaluation enregistree !\n";
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
                }
            }
        }
        else if (choix == 6) { // Lecture des données
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
                         << "   Raison probable: Ce client a deja une demande ou evaluation (Foreign Key).\n";
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
                    cout << ">> ERREUR MYSQL : Impossible de supprimer cet artisan.\n";
                }
            }
        }
        else if (choix == 9) { // Se connecter (Login)
            string login_email, login_password;
            clearBuffer();
            cout << "Entrez votre email : "; getline(cin, login_email);
            cout << "Entrez votre mot de passe : "; getline(cin, login_password);

            if (conn) {
                // Chercher le client dans la BDD par email
                string query = "SELECT id_client, nom, mot_de_passe FROM client WHERE email = '" + login_email + "'";
                if (mysql_query(conn, query.c_str()) == 0) {
                    MYSQL_RES* res = mysql_store_result(conn);
                    if (res) {
                        MYSQL_ROW row = mysql_fetch_row(res);
                        if (row) {
                            // Utiliser hashPassword pour verifier le mot de passe
                            User tempUser(0, "", login_email, "");
                            string hashedInput = tempUser.hashPassword(login_password);
                            string storedHash = row[2] ? row[2] : "";

                            if (hashedInput == storedHash) {
                                cout << "\n>> [+] CONNEXION REUSSIE ! Bienvenue, " << (row[1] ? row[1] : "Utilisateur") << " !\n";
                                cout << ">> [OOP] Hash verifie avec succes (Login Validation)\n";
                            } else {
                                cout << "\n>> [-] ECHEC : Mot de passe incorrect !\n";
                                cout << ">> [OOP] Hash ne correspond pas (Login Refused)\n";
                            }
                        } else {
                            cout << "\n>> [-] ECHEC : Aucun compte trouve avec cet email.\n";
                        }
                        mysql_free_result(res);
                    }
                } else {
                    cout << ">> ERREUR MYSQL : " << mysql_error(conn) << "\n";
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
