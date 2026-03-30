-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2026 at 01:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bricola`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrateur`
--

CREATE TABLE `administrateur` (
  `id_admin` int(11) NOT NULL,
  `nom_admin` varchar(100) NOT NULL,
  `email_admin` varchar(100) NOT NULL,
  `mot_de_passe_admin` varchar(100) NOT NULL,
  `telephone_admin` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artisan`
--

CREATE TABLE `artisan` (
  `id_artisan` int(11) NOT NULL,
  `nom_artisan` varchar(100) NOT NULL,
  `email_artisan` varchar(100) NOT NULL,
  `mot_de_passe_artisan` varchar(100) NOT NULL,
  `telephone_artisan` varchar(20) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `dispo_artisan` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artisan_metier`
--

CREATE TABLE `artisan_metier` (
  `fk_artisan` int(11) NOT NULL,
  `fk_metier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id_client` int(11) NOT NULL,
  `nom_client` varchar(100) NOT NULL,
  `email_client` varchar(100) NOT NULL,
  `mot_de_passe_client` varchar(100) NOT NULL,
  `telephone_client` varchar(20) NOT NULL,
  `adresse` varchar(200) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `date_inscription` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demande`
--

CREATE TABLE `demande` (
  `id_demande` int(11) NOT NULL,
  `date_creation_demande` date DEFAULT curdate(),
  `statut` enum('confirmer','refuser','en cours','complete') NOT NULL DEFAULT 'en cours',
  `type_demande` enum('reparation','service') NOT NULL,
  `description_demande` varchar(200) NOT NULL,
  `fk_client` int(11) NOT NULL,
  `fk_artisan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `desservice`
--

CREATE TABLE `desservice` (
  `id_service` int(11) NOT NULL,
  `nom_service` varchar(100) NOT NULL,
  `description_service` varchar(200) NOT NULL,
  `tarif_service` decimal(10,2) NOT NULL,
  `fk_demande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation`
--

CREATE TABLE `evaluation` (
  `id_evaluation` int(11) NOT NULL,
  `note_evaluation` tinyint(4) NOT NULL,
  `commentaire` varchar(200) NOT NULL,
  `date_evaluation` date NOT NULL,
  `fk_client` int(11) NOT NULL,
  `fk_demande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `evaluation`
--
DELIMITER $$
CREATE TRIGGER `before_evaluation_insert` BEFORE INSERT ON `evaluation` FOR EACH ROW BEGIN
    IF NEW.note_evaluation < 1 OR NEW.note_evaluation > 5 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: note_evaluation must be between 1 and 5';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `metier`
--

CREATE TABLE `metier` (
  `id_metier` int(11) NOT NULL,
  `nom_metier` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paiement`
--

CREATE TABLE `paiement` (
  `id_paiement` int(11) NOT NULL,
  `montant_paiement` decimal(10,2) NOT NULL,
  `methode_paiement` varchar(50) NOT NULL,
  `statut_paiement` enum('en attente','succes','echoue') NOT NULL DEFAULT 'en attente',
  `date_paiement` date NOT NULL,
  `fk_demande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rapport_financier`
--

CREATE TABLE `rapport_financier` (
  `id_rapport` int(11) NOT NULL,
  `date_generation` date DEFAULT curdate(),
  `debut_periode` date NOT NULL,
  `fin_periode` date NOT NULL,
  `revenus_totaux` decimal(10,2) DEFAULT NULL,
  `depenses_totaux` decimal(10,2) DEFAULT NULL,
  `nombre_demandes_completees` int(11) NOT NULL,
  `fk_admin_generateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reparation`
--

CREATE TABLE `reparation` (
  `id_reparation` int(11) NOT NULL,
  `description_panne` varchar(250) NOT NULL,
  `date_debut_reparation` date NOT NULL,
  `date_fin_reparation` date DEFAULT NULL,
  `cout_reparation` decimal(10,2) DEFAULT NULL,
  `fk_demande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email_admin` (`email_admin`);

--
-- Indexes for table `artisan`
--
ALTER TABLE `artisan`
  ADD PRIMARY KEY (`id_artisan`),
  ADD UNIQUE KEY `email_artisan` (`email_artisan`);

--
-- Indexes for table `artisan_metier`
--
ALTER TABLE `artisan_metier`
  ADD PRIMARY KEY (`fk_artisan`,`fk_metier`),
  ADD KEY `fk_metier` (`fk_metier`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`),
  ADD UNIQUE KEY `email_client` (`email_client`);

--
-- Indexes for table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`id_demande`),
  ADD KEY `fk_client` (`fk_client`),
  ADD KEY `fk_artisan` (`fk_artisan`);

--
-- Indexes for table `desservice`
--
ALTER TABLE `desservice`
  ADD PRIMARY KEY (`id_service`),
  ADD UNIQUE KEY `fk_demande` (`fk_demande`);

--
-- Indexes for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id_evaluation`),
  ADD KEY `fk_client` (`fk_client`),
  ADD KEY `fk_demande` (`fk_demande`);

--
-- Indexes for table `metier`
--
ALTER TABLE `metier`
  ADD PRIMARY KEY (`id_metier`),
  ADD UNIQUE KEY `nom_metier` (`nom_metier`);

--
-- Indexes for table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `fk_demande` (`fk_demande`);

--
-- Indexes for table `rapport_financier`
--
ALTER TABLE `rapport_financier`
  ADD PRIMARY KEY (`id_rapport`),
  ADD KEY `fk_admin_generateur` (`fk_admin_generateur`);

--
-- Indexes for table `reparation`
--
ALTER TABLE `reparation`
  ADD PRIMARY KEY (`id_reparation`),
  ADD UNIQUE KEY `fk_demande` (`fk_demande`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artisan`
--
ALTER TABLE `artisan`
  MODIFY `id_artisan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `demande`
--
ALTER TABLE `demande`
  MODIFY `id_demande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `desservice`
--
ALTER TABLE `desservice`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id_evaluation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `metier`
--
ALTER TABLE `metier`
  MODIFY `id_metier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rapport_financier`
--
ALTER TABLE `rapport_financier`
  MODIFY `id_rapport` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reparation`
--
ALTER TABLE `reparation`
  MODIFY `id_reparation` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artisan_metier`
--
ALTER TABLE `artisan_metier`
  ADD CONSTRAINT `artisan_metier_ibfk_1` FOREIGN KEY (`fk_artisan`) REFERENCES `artisan` (`id_artisan`) ON DELETE CASCADE,
  ADD CONSTRAINT `artisan_metier_ibfk_2` FOREIGN KEY (`fk_metier`) REFERENCES `metier` (`id_metier`) ON DELETE CASCADE;

--
-- Constraints for table `demande`
--
ALTER TABLE `demande`
  ADD CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`fk_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `demande_ibfk_2` FOREIGN KEY (`fk_artisan`) REFERENCES `artisan` (`id_artisan`) ON DELETE SET NULL;

--
-- Constraints for table `desservice`
--
ALTER TABLE `desservice`
  ADD CONSTRAINT `desservice_ibfk_1` FOREIGN KEY (`fk_demande`) REFERENCES `demande` (`id_demande`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`fk_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`fk_demande`) REFERENCES `demande` (`id_demande`) ON DELETE CASCADE;

--
-- Constraints for table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`fk_demande`) REFERENCES `demande` (`id_demande`) ON DELETE CASCADE;

--
-- Constraints for table `rapport_financier`
--
ALTER TABLE `rapport_financier`
  ADD CONSTRAINT `rapport_financier_ibfk_1` FOREIGN KEY (`fk_admin_generateur`) REFERENCES `administrateur` (`id_admin`) ON DELETE CASCADE;

--
-- Constraints for table `reparation`
--
ALTER TABLE `reparation`
  ADD CONSTRAINT `reparation_ibfk_1` FOREIGN KEY (`fk_demande`) REFERENCES `demande` (`id_demande`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
