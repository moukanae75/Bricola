#include <iostream>
#include <string>
#include <windows.h>
#include "mysql.h"

using namespace std;

int main()
{
    MYSQL* conn;
    
    // Initialize MySQL connection
    conn = mysql_init(0);
    
    // Connect to the database
    // mysql_real_connect(connection, server, user, password, database, port, unix_socket, client_flag)
    conn = mysql_real_connect(conn, "localhost", "root", "", "bricola", 3306, NULL, 0);
    
    if (conn) {
        cout << "Connexion reussie a la base de donnees 'bricola' !" << endl;
        
        // --- ADDING SOMETHING TO THE DATABASE ---
        // Create an SQL query string to insert a new client
        string query = "INSERT INTO client (nom, email, mot_de_passe, telephone, adresse, date_inscription) "
                       "VALUES ('Mouka', 'mouka@example.com', '123456', '0600000000', 'Algeria', '2026-04-24')";
                       
        // Convert string to const char* because mysql_query only accepts plain C-strings
        const char* q = query.c_str(); 
        
        // Execute the query
        int query_state = mysql_query(conn, q);
        
        if (query_state == 0) { // 0 means success
            cout << "Client ajoute avec succes !" << endl;
        } else {
            cout << "Erreur lors de l'ajout : " << mysql_error(conn) << endl;
        }
        // ----------------------------------------
        
    } else {
        cout << "Erreur de connexion : " << mysql_error(conn) << endl;
    }

    // Close the connection
    mysql_close(conn);
    
    return 0;
}
