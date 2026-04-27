#include "header/Media.h"
#include <iostream>
#include <fstream>
#include <sstream>

Media::Media(std::string name, std::string path, MediaType t) : id(0), fileName(name), filePath(path), type(t) {}

std::string Media::getUrl() const {
    return "file://bricola_media/" + filePath;
}

void Media::saveToDisk() {
    // Serialization: ecrire les donnees du Media dans un fichier texte

}

void Media::loadFromDisk(int mediaId) {
    // Deserialization: lire les donnees du Media depuis le fichier texte

}
