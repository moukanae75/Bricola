#include <vector>
#include <string>

class Evaluation {
private:
    int id;
    double note; 
    std::string commentaire;
    int fk_client;
    int fk_artisan;

public:
    Evaluation(double n, std::string c, int clientID, int artisanID);

    static double computeArtisanAvg(const std::vector<Evaluation>&  );
    
    double getNote() const { return note; }
};