CREATE DATABASE Bricola;
GO

USE Bricola;
GO

CREATE TABLE administrateur (
    id_admin INT PRIMARY KEY IDENTITY,
    nom_admin VARCHAR(100)NOT NULL,
    email_admin VARCHAR(100) UNIQUE NOT NULL ,
    mot_de_passe_admin VARCHAR(100) NOT NULL ,
    telephone_admin VARCHAR(20) NOT NULL 
);

CREATE TABLE client (
    id_client INT PRIMARY KEY IDENTITY,
    nom_client VARCHAR(100) NOT NULL ,
    email_client VARCHAR(100) UNIQUE NOT NULL ,
    mot_de_passe_client VARCHAR(100) NOT NULL ,
    telephone_client VARCHAR(20) NOT NULL ,
    adresse VARCHAR(200) NOT NULL ,
	ville VARCHAR(50) NOT NULL ,
    date_inscription DATE DEFAULT (GETDATE())
);

CREATE TABLE artisan (
    id_artisan INT PRIMARY KEY IDENTITY,
	nom_artisan VARCHAR(100) NOT NULL ,
    email_artisan VARCHAR(100) UNIQUE NOT NULL  ,
    mot_de_passe_artisan VARCHAR(100) NOT NULL ,
    telephone_artisan VARCHAR(20) NOT NULL ,
    adresse VARCHAR(200) NOT NULL ,
	ville VARCHAR(50) NOT NULL ,
    dispo_artisan BIT NOT NULL DEFAULT 1
);

CREATE TABLE metier (
    id_metier INT PRIMARY KEY IDENTITY,
    nom_metier VARCHAR(100) UNIQUE NOT NULL
	)

CREATE TABLE artisan_metier (
    fk_artisan INT,
    fk_metier INT,
    PRIMARY KEY (fk_artisan, fk_metier), 
    FOREIGN KEY (fk_artisan) REFERENCES artisan(id_artisan),
    FOREIGN KEY (fk_metier) REFERENCES metier(id_metier)
);

CREATE TABLE demande (
    id_demande INT PRIMARY KEY IDENTITY,
    date_creation_demande DATE DEFAULT (GETDATE()),
    statut VARCHAR(50) CHECK (statut IN ('confirmer','refuser','en cours','complete')),
	type_demande VARCHAR(20) CHECK (type_demande IN ('reparation', 'service')),
    description_demande VARCHAR(200),

    fk_client INT NOT NULL,
    fk_artisan INT ,

    FOREIGN KEY (fk_client) REFERENCES client(id_client),
    FOREIGN KEY (fk_artisan) REFERENCES artisan(id_artisan)
    
);

CREATE TABLE desservice (
    id_service INT PRIMARY KEY IDENTITY,
    nom_service VARCHAR(100) NOT NULL,
    description_service VARCHAR(200) NOT NULL,
    tarif_service DECIMAL(10,2) NOT NULL,
    fk_demande INT unique,

    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE reparation (
    id_reparation INT PRIMARY KEY IDENTITY,
	description_panne VARCHAR(250) NOT NULL,
    date_debut_reparation DATE NOT NULL,
    date_fin_reparation DATE NULL,
    cout_reparation DECIMAL(10,2) ,

    fk_demande INT unique,
    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE paiement (
    id_paiement INT PRIMARY KEY IDENTITY,
    montant_paiement DECIMAL(10,2) NOT NULL,
    methode_paiement VARCHAR(50) NOT NULL,
    statut_paiement VARCHAR(50) NOT NULL,
    date_paiement DATE NOT NULL,

    fk_demande INT,
    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE evaluation (
    id_evaluation INT PRIMARY KEY IDENTITY,
    note_evaluation INT CHECK (note_evaluation BETWEEN 1 AND 5),
    commentaire VARCHAR(200) NOT NULL,
    date_evaluation DATE NOT NULL,

    fk_client INT,
    fk_demande INT,

    FOREIGN KEY (fk_client) REFERENCES client(id_client),
    FOREIGN KEY (fk_demande) REFERENCES demande(id_demande)
);

CREATE TABLE rapport_financier (
    id_rapport INT PRIMARY KEY IDENTITY,
    date_generation DATE DEFAULT (GETDATE()),
    debut_periode DATE NOT NULL, 
    fin_periode DATE NOT NULL,   
    revenus_totaux DECIMAL(10,2) ,
    depenses_totaux DECIMAL(10,2),
    nombre_demandes_completees INT NOT NULL,
    
    
    fk_admin_generateur INT, 
    FOREIGN KEY (fk_admin_generateur) REFERENCES administrateur(id_admin)
);

INSERT INTO rapport_financier (debut_periode, fin_periode, revenus_totaux, nombre_demandes_completees, fk_admin_generateur)
SELECT 
    '2026-03-01', 
    '2026-03-31', 
    SUM(montant_paiement), 
    COUNT(DISTINCT fk_demande),
    1 -- The ID of the admin running the report
FROM paiement
WHERE date_paiement BETWEEN '2026-03-01' AND '2026-03-31'
AND statut_paiement = 'succès';