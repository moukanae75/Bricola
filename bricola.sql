create database Bricola

use Bricola

create table client(
	id_client int primary key identity,
	nom varchar(100),
	email varchar(100),
	mot_de_passe varchar(100),
	telephone varchar(40),
	adresse varchar(100),
	date_inscription date

)

create table administrateur
(
	id_admin int primary key identity,
	nom_admin varchar(100),
	email_admin varchar(100),
	mot_de_passe_admin varchar(100),
	telephone_admin varchar(40),
)

create table artisan
(
	id_artisan int primary key identity,
	metier_artisan varchar(100),
	dispo_artisan varchar check (dispo_artisan in('disponible','pas disponible'))
)

create table desservice 
(
	id_service int primary key identity ,
	nom_service varchar(100),
	description_service varchar(200),
	tarif_service float
)




create table demande(
	id_demande int primary key identity,
	date_creation_demande date ,
	statut varchar(50) check (statut in('confirmer','refuser','en cours','complete')),
	description_demande varchar(200) ,

	 fk_client int foreign key references client(id_client),
	 fk_artisan int  foreign key references artisan(id_artisan),
	 fk_service int foreign key references desservice(id_service)
)

create table reparation (
	id_reparation int primary key identity,
	date_debut_reparation date,
	date_fin_reparation date,
	cout_reparation float,
	fk_demande int foreign key references demande(id_demande)
)

create table paiement(
	id_paiement int primary key identity ,
	montant_paiement float ,
	methode_paiement varchar(40) check (methode_paiement in('par carte','par espece')),
	statut_paiement varchar(50) check (statut_paiement in('payer','non payer')),
	date_paiement date ,
	fk_demande int foreign key references demande(id_demande)
)

create table evaluation (
	id_evaluation int primary key identity ,
	note_evaluation float check(note_evaluation between 0 and 5),
	commantaire varchar(100),
	date_evaluation date,
	
	fk_client int foreign key references client(id_client),
	fk_artisan int  foreign key references artisan(id_artisan)
)

create table rapport_financier(
	id_rapport int primary key identity, 
	period_rapport date,
	revenus_rapport float,
	depenses_rapport float,

	fk_demande int foreign key references demande(id_demande)

)