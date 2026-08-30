#include <string>


enum class MediaType {IMAGE,VIDEO,DOCUMENT};




class Media {
private:
    int id;
    std::string fileName;
    std::string filePath;
    MediaType type;

public:
    Media(std::string name, std::string path, MediaType t);

   
    std::string getUrl() const; 
    
    
    void saveToDisk();
    void loadFromDisk(int mediaId);
};