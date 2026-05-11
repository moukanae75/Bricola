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
#include "header/Media.h"

using namespace std;

void clearBuffer() {
    if(cin.peek() == '\n') cin.ignore();
}

int main() {
    MYSQL* conn = mysql_init(0);
    conn = mysql_real_connect(conn, "localhost", "root", "", "bricola", 3306, NULL, 0);
    bool dbConnected = (conn != nullptr);

    bool isLogged = false;
    string currentRole = "";
    string currentId = "";

    while (true) {
        system("cls");
        cout << "=======================================================\n";
        cout << "         Systeme de Gestion Bricola      \n";
        cout << "=======================================================\n";
        if (dbConnected) cout << "[+] MYSQL Connecte\n"; else cout << "[-] Erreur MYSQL\n";
        cout << "=======================================================\n";

        if (!isLogged) {
            cout << "--- MENU AUTHENTIFICATION ---\n";
            cout << "1. Se connecter (Login)\n";
            cout << "2. Creer un compte Client\n";
            cout << "3. Creer un compte Artisan\n";
            cout << "0. Quitter l'application\n";
            cout << "Choix : ";
            int choix; cin >> choix;
            
            if (choix == 0) break;
            else if (choix == 1) {
                string email, pass;
                clearBuffer();
                cout << "Email : "; getline(cin, email);
                cout << "Mot de passe: "; getline(cin, pass);
                
                if (User::loginUser(conn, email, pass, currentRole, currentId)) {
                    isLogged = true;
                    cout << "\n[+] Bienvenue ! Vous etes connecte en tant que : " << currentRole << "\n";
                    system("pause");
                } else {
                    cout << "\n[-] Identifiants incorrects !\n";
                    system("pause");
                }
            }
            else if (choix == 2) {
                string nom, email, mot_de_passe, telephone, adresse;
                clearBuffer();
                cout << "Nom : "; getline(cin, nom);
                cout << "Email : "; getline(cin, email);
                cout << "Mot de passe : "; getline(cin, mot_de_passe);
                cout << "Telephone : "; getline(cin, telephone);
                cout << "Adresse : "; getline(cin, adresse);

                Client nouveauClient(0, nom, email, telephone);
                nouveauClient.setPassword(mot_de_passe);
                nouveauClient.setAdresse(adresse);
                nouveauClient.saveToDB(conn);
                system("pause");
            }
            else if (choix == 3) {
                string nom, email, mot_de_passe, telephone, metier;
                clearBuffer();
                cout << "Nom : "; getline(cin, nom);
                cout << "Email : "; getline(cin, email);
                cout << "Mot de passe : "; getline(cin, mot_de_passe);
                cout << "Telephone : "; getline(cin, telephone);
                cout << "Metier (ex: Plombier) : "; getline(cin, metier);

                Artisan nouvelArtisan(0, nom, email, telephone);
                nouvelArtisan.setPassword(mot_de_passe);
                nouvelArtisan.setMetier(metier);
                nouvelArtisan.saveToDB(conn);
                system("pause");
            }
        } 
        else { 
            if (currentRole == "admin") {
                system("cls");
                cout << "--- MENU ADMINISTRATEUR ---\n";
                cout << "1. Afficher tous les clients et artisans\n";
                cout << "2. Supprimer un client\n";
                cout << "3. Supprimer un artisan\n";
                cout << "9. Se deconnecter\n";
                cout << "Choix : ";
                int choix; cin >> choix;
                if (choix == 9) { isLogged = false; currentRole = ""; currentId = ""; }
                else if (choix == 1) { Client::afficherTous(conn); Artisan::afficherTous(conn); system("pause"); }
                else if (choix == 2) {
                    string id_c; cout << "ID Client : "; cin >> id_c;
                    Client::supprimer(conn, id_c); system("pause");
                }
                else if (choix == 3) {
                    string id_a; cout << "ID Artisan : "; cin >> id_a;
                    Artisan::supprimer(conn, id_a); system("pause");
                }
            }
            else if (currentRole == "client") {
                system("cls");
                Client objClient(stoi(currentId), "", "", "");
                cout << "--- MENU CLIENT (ID: " << currentId << ") ---\n";
                cout << "1. Afficher les artisans disponibles\n";
                cout << "2. Creer une demande de service\n";
                cout << "3. Mes demandes et leur statut\n";
                cout << "4. Evaluer un artisan\n";
                cout << "9. Se deconnecter\n";
                cout << "Choix : ";
                int choix; cin >> choix;
                if (choix == 9) { isLogged = false; currentRole = ""; currentId = ""; }
                else if (choix == 1) { Artisan::afficherTous(conn); system("pause"); }
                else if (choix == 2) {
                    string id_a, desc, fichier;
                    cout << "ID Artisan cible : "; cin >> id_a;
                    clearBuffer();
                    cout << "Description de la demande : "; getline(cin, desc);
                    cout << "Voulez-vous joindre un fichier ? Laissez vide sinon : "; getline(cin, fichier);
                    
                    ServiceRequest req(desc, currentId, id_a);
                    req.saveToDB(conn);
                    system("pause");
                }
                else if (choix == 3) { objClient.afficherDemandesClient(conn, currentId); system("pause"); }
                else if (choix == 4) {
                    string id_a, note, comm;
                    cout << "ID Artisan : "; cin >> id_a;
                    clearBuffer();
                    cout << "Note (0 a 5) : "; getline(cin, note);
                    cout << "Commentaire : "; getline(cin, comm);
                    
                    Evaluation eval(note, comm, currentId, id_a);
                    eval.saveToDB(conn);
                    system("pause");
                }
            }
            else if (currentRole == "artisan") {
                system("cls");
                Artisan tempArtisan(stoi(currentId), "", "", "");
                tempArtisan.fetchAverageRating(conn, currentId);
                
                cout << "--- MENU ARTISAN (ID: " << currentId << ") | Ma Note : " << tempArtisan.getAverageRating() << "/5 ---\n";
                cout << "1. Changer ma disponibilite\n";
                cout << "2. Afficher mes demandes assignees\n";
                cout << "3. Accepter une demande\n";
                cout << "4. Refuser une demande\n";
                cout << "5. Ajouter une competence\n";
                cout << "9. Se deconnecter\n";
                cout << "Choix : ";
                int choix; cin >> choix;
                if (choix == 9) { isLogged = false; currentRole = ""; currentId = ""; }
                else if (choix == 1) {
                    string etat; cout << "Entrez 1 pour Disponible, 2 pour Pas disponible : "; cin >> etat;
                    tempArtisan.updateAvailability((etat == "1"), conn, currentId); system("pause");
                }
                else if (choix == 2) { tempArtisan.consulterServicesAssignes(conn, currentId); system("pause"); }
                else if (choix == 3) {
                    string id_d; cout << "ID Demande a ACCEPTER : "; cin >> id_d;
                    ServiceRequest req("", "", "");
                    req.setId(stoi(id_d));
                    tempArtisan.accepterService(req, conn); system("pause");
                }
                else if (choix == 4) {
                    string id_d; cout << "ID Demande a REFUSER : "; cin >> id_d;
                    ServiceRequest req("", "", "");
                    req.setId(stoi(id_d));
                    tempArtisan.refuserService(req, conn); system("pause");
                }
                else if (choix == 5) {
                    string comp; clearBuffer();
                    cout << "Entrez votre nouvelle competence (ex: Electricite) : "; getline(cin, comp);
                    tempArtisan.proposerCompetence(comp, conn, currentId); system("pause");
                }
            }
        }
    }
    if (conn) mysql_close(conn);
    cout << "A bientot !\n";
    return 0;
}
