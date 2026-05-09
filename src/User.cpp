#include "../header/User.h"
#include <iostream>
#include <sstream>

using namespace std;

User::User(int id, std::string n, std::string e, std::string t) : id(id), nom(n), email(e), telephone(t) {}

std::string User::hashPassword(std::string password) const {
    unsigned long hash = 5381;
    for (char c : password) {
        hash = ((hash << 5) + hash) + c;
    }
    std::stringstream ss;
    ss << std::hex << hash;
    return ss.str();
}

void User::registerUser(std::string password) {
    this->password = hashPassword(password);
}

bool User::login(std::string email, std::string password) {
    return (this->email == email && this->password == hashPassword(password));
}

bool User::loginUser(MYSQL* conn, const string& email, const string& password_input, string& out_role, string& out_id) {
    if (!conn) return false;
    
    if (email == "admin" && password_input == "admin") {
        out_role = "admin"; out_id = "0"; return true;
    }

    User tempUser(0, "", email, "");
    string hashedInput = tempUser.hashPassword(password_input);

    string queryClient = "SELECT id_client, mot_de_passe FROM client WHERE email = '" + email + "'";
    if (mysql_query(conn, queryClient.c_str()) == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row = mysql_fetch_row(res);
            if (row) {
                string storedHash = row[1] ? row[1] : "";
                if (hashedInput == storedHash) {
                    out_role = "client"; out_id = row[0] ? row[0] : "";
                    mysql_free_result(res); return true;
                }
            }
            mysql_free_result(res);
        }
    }

    string queryArtisan = "SELECT id_artisan, mot_de_passe FROM artisan WHERE email = '" + email + "'";
    if (mysql_query(conn, queryArtisan.c_str()) == 0) {
        MYSQL_RES* res = mysql_store_result(conn);
        if (res) {
            MYSQL_ROW row = mysql_fetch_row(res);
            if (row) {
                string storedHash = row[1] ? row[1] : "";
                if (hashedInput == storedHash) {
                    out_role = "artisan"; out_id = row[0] ? row[0] : "";
                    mysql_free_result(res); return true;
                }
            }
            mysql_free_result(res);
        }
    }
    return false;
}
