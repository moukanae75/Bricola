CREATE DATABASE bricola;
USE bricola;

CREATE TABLE client (
    id_client INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(100),
    telephone VARCHAR(40),
    adresse VARCHAR(100),
    date_inscription DATE
);

CREATE TABLE administrateur (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom_admin VARCHAR(100),
    email_admin VARCHAR(100),
    mot_de_passe_admin VARCHAR(100),
    telephone_admin VARCHAR(40)
);

CREATE TABLE artisan (
    id_artisan INT AUTO_INCREMENT PRIMARY KEY,
    metier_artisan VARCHAR(100),
    dispo_artisan ENUM('disponible','pas disponible')
);

CREATE TABLE desservice (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100),
    description_service VARCHAR(200),
    tarif_service FLOAT
);

CREATE TABLE demande (
    id_demande INT AUTO_INCREMENT PRIMARY KEY,
    date_creation_demande DATE,
    statut ENUM('confirmer','refuser','en cours','complete'),
    description_demande VARCHAR(200),

    fk_client INT,
    fk_artisan INT,
    fk_service INT,

    FOREIGN KEY (fk_client) REFERENCES client(id_client),
    FOREIGN KEY (fk_artisan) REFERENCES artisan(id_artisan),
    FOREIGN KEY (fk_service) REFERENCES desservice(id_service)
);

CREATE TABLE reparation (
    id_reparation INT AUTO_INCREMENT PRIMARY KEY,
    date_debut_reparation DATE,
    date_fin_reparation DATE,
    cout_reparation FLOAT,
    fk_demande INT,

    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE paiement (
    id_paiement INT AUTO_INCREMENT PRIMARY KEY,
    montant_paiement FLOAT,
    methode_paiement ENUM('par carte','par espece'),
    statut_paiement ENUM('payer','non payer'),
    date_paiement DATE,
    fk_demande INT,

    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE evaluation (
    id_evaluation INT AUTO_INCREMENT PRIMARY KEY,
    note_evaluation FLOAT CHECK (note_evaluation BETWEEN 0 AND 5),
    commentaire VARCHAR(100),
    date_evaluation DATE,

    fk_client INT,
    fk_artisan INT,

    FOREIGN KEY (fk_client) REFERENCES client(id_client),
    FOREIGN KEY (fk_artisan) REFERENCES artisan(id_artisan)
);

CREATE TABLE rapport_financier (
    id_rapport INT AUTO_INCREMENT PRIMARY KEY,
    period_rapport DATE,
    revenus_rapport FLOAT,
    depenses_rapport FLOAT,
    fk_demande INT,

    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);