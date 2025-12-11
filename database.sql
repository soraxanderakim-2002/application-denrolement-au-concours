-- ==================== ENROLL CONCOURS DATABASE STRUCTURE ====================
-- Base de données pour le système d'enregistrement aux concours nationaux du Cameroun
-- Version: 1.0.0
-- Date: Décembre 2025

-- ==================== CREATE DATABASE ====================

CREATE DATABASE IF NOT EXISTS enroll_concours_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE enroll_concours_db;

-- ==================== TABLE CONCOURS ====================

CREATE TABLE IF NOT EXISTS concours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL UNIQUE,
    sigle VARCHAR(50) NOT NULL,
    description TEXT,
    date_debut DATE,
    date_fin DATE,
    date_concours DATE,
    montant_inscription DECIMAL(10,2) DEFAULT 25000,
    statut ENUM('actif', 'fermé', 'en_cours', 'terminé') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_statut (statut)
);

-- ==================== TABLE REGIONS ====================

CREATE TABLE IF NOT EXISTS regions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    code_region VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==================== TABLE FILIERES ====================

CREATE TABLE IF NOT EXISTS filieres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==================== TABLE CANDIDATS ====================

CREATE TABLE IF NOT EXISTS candidats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    telephone VARCHAR(20),
    date_naissance DATE,
    sexe ENUM('M', 'F'),
    region_id INT,
    filiere_id INT,
    concours_id INT,
    numero_inscription VARCHAR(50) UNIQUE,
    statut ENUM('en_attente', 'validé', 'rejeté', 'abandonné') DEFAULT 'en_attente',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_validation DATETIME,
    centre_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id),
    FOREIGN KEY (filiere_id) REFERENCES filieres(id),
    FOREIGN KEY (concours_id) REFERENCES concours(id),
    FOREIGN KEY (centre_id) REFERENCES centres(id),
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_concours (concours_id),
    INDEX idx_region (region_id),
    INDEX idx_filiere (filiere_id)
);

-- ==================== TABLE PAIEMENTS ====================

CREATE TABLE IF NOT EXISTS paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    candidat_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    methode_paiement VARCHAR(100) NOT NULL,
    reference_paiement VARCHAR(255),
    statut ENUM('en_attente', 'validé', 'rejeté', 'remboursé') DEFAULT 'en_attente',
    date_paiement DATETIME,
    date_validation DATETIME,
    notes TEXT,
    admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (candidat_id) REFERENCES candidats(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES admins(id),
    INDEX idx_statut (statut),
    INDEX idx_candidat (candidat_id),
    INDEX idx_date_paiement (date_paiement)
);

-- ==================== TABLE CENTRES ====================

CREATE TABLE IF NOT EXISTS centres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    ville VARCHAR(100),
    region_id INT,
    adresse TEXT,
    telephone VARCHAR(20),
    responsable VARCHAR(255),
    email_responsable VARCHAR(255),
    nombre_places INT DEFAULT 500,
    places_occupees INT DEFAULT 0,
    statut ENUM('actif', 'inactif', 'ferme') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id),
    INDEX idx_region (region_id),
    INDEX idx_statut (statut)
);

-- ==================== TABLE ADMINS ====================

CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    prenom VARCHAR(255),
    nom VARCHAR(255),
    role ENUM('super_admin', 'admin', 'moderateur', 'observateur') DEFAULT 'admin',
    statut ENUM('actif', 'inactif', 'suspendu') DEFAULT 'actif',
    dernier_acces DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- ==================== TABLE LOGS ====================

CREATE TABLE IF NOT EXISTS logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    table_name VARCHAR(100),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id),
    INDEX idx_admin (admin_id),
    INDEX idx_date (date_action),
    INDEX idx_action (action)
);

-- ==================== TABLE SESSIONS ====================

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    admin_id INT NOT NULL,
    data TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_id),
    INDEX idx_last_activity (last_activity)
);

-- ==================== TABLE PARAMETRES ====================

CREATE TABLE IF NOT EXISTS parametres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur LONGTEXT,
    type VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==================== TABLE EXPORT_LOGS ====================

CREATE TABLE IF NOT EXISTS export_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    type_export VARCHAR(50),
    fichier_nom VARCHAR(255),
    nombre_records INT,
    date_export TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id),
    INDEX idx_admin (admin_id)
);

-- ==================== INSERT DATA ====================

-- Insérer les régions du Cameroun
INSERT INTO regions (nom, code_region) VALUES
('Adamaoua', 'AD'),
('Centre', 'CE'),
('Est', 'ES'),
('Extrême-Nord', 'EN'),
('Littoral', 'LI'),
('Nord', 'NO'),
('Nord-Ouest', 'NW'),
('Ouest', 'OW'),
('Sud', 'SU'),
('Sud-Ouest', 'SW');

-- Insérer les filières
INSERT INTO filieres (nom, description) VALUES
('Ingénierie', 'Filière d\'études d\'ingénierie'),
('Médecine', 'Filière d\'études de médecine et santé'),
('Droit', 'Filière d\'études juridiques'),
('Économie', 'Filière d\'études économiques'),
('Lettres', 'Filière d\'études littéraires'),
('Sciences', 'Filière d\'études scientifiques'),
('Informatique', 'Filière d\'études informatiques');

-- Insérer les concours
INSERT INTO concours (nom, sigle, description, date_debut, date_fin, date_concours, montant_inscription, statut) VALUES
('Concours d\'Entrée à l\'Université Publique', 'CEUP', 'Concours national d\'entrée à l\'université publique', '2025-01-01', '2025-03-31', '2025-06-15', 25000, 'actif'),
('Concours de la Fonction Publique', 'CFP', 'Concours de recrutement à la fonction publique', '2025-02-01', '2025-04-30', '2025-07-20', 25000, 'actif'),
('Concours Militaire', 'CM', 'Concours de formation militaire', '2025-01-15', '2025-04-15', '2025-08-10', 35000, 'actif'),
('Concours de Police', 'CONCOP', 'Concours de formation de la gendarmerie', '2025-03-01', '2025-05-31', '2025-09-05', 25000, 'actif');

-- Insérer l'administrateur par défaut
INSERT INTO admins (username, password, email, prenom, nom, role, statut) VALUES
('admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'admin@enroll.cm', 'Admin', 'Principal', 'super_admin', 'actif');

-- Insérer les paramètres par défaut
INSERT INTO parametres (cle, valeur, type, description) VALUES
('site_name', 'Enroll Concours', 'string', 'Nom du site'),
('site_url', 'http://localhost/php-enroll-project/enroll-concours', 'string', 'URL du site'),
('email_contact', 'contact@enroll.cm', 'string', 'Email de contact'),
('telephone_contact', '+237 6XX XXX XXX', 'string', 'Téléphone de contact'),
('montant_inscription_defaut', '25000', 'decimal', 'Montant d\'inscription par défaut'),
('devise', 'FCFA', 'string', 'Devise utilisée'),
('delai_inscription_jours', '90', 'integer', 'Délai limite d\'inscription'),
('delai_paiement_jours', '30', 'integer', 'Délai pour payer après inscription'),
('smtp_serveur', 'mail.enroll.cm', 'string', 'Serveur SMTP'),
('smtp_port', '587', 'integer', 'Port SMTP'),
('smtp_email', 'noreply@enroll.cm', 'string', 'Email SMTP'),
('smtp_use_tls', '1', 'boolean', 'Utiliser TLS'),
('timezone', 'Africa/Douala', 'string', 'Fuseau horaire');

-- ==================== CREATE VIEWS ====================

-- Vue pour les statistiques
CREATE OR REPLACE VIEW vw_statistiques_inscrits AS
SELECT 
    c.id as concours_id,
    c.nom as concours_nom,
    c.sigle,
    r.nom as region_nom,
    f.nom as filiere_nom,
    COUNT(ca.id) as total_inscrits,
    SUM(CASE WHEN ca.statut = 'validé' THEN 1 ELSE 0 END) as inscrits_valides,
    SUM(CASE WHEN ca.statut = 'en_attente' THEN 1 ELSE 0 END) as inscrits_en_attente,
    SUM(CASE WHEN ca.statut = 'rejeté' THEN 1 ELSE 0 END) as inscrits_rejetes,
    SUM(CASE WHEN ca.statut = 'abandonné' THEN 1 ELSE 0 END) as inscrits_abandonnes
FROM candidats ca
LEFT JOIN concours c ON ca.concours_id = c.id
LEFT JOIN regions r ON ca.region_id = r.id
LEFT JOIN filieres f ON ca.filiere_id = f.id
GROUP BY c.id, r.id, f.id;

-- Vue pour les paiements
CREATE OR REPLACE VIEW vw_statistiques_paiements AS
SELECT 
    c.id as concours_id,
    c.nom as concours_nom,
    COUNT(p.id) as total_paiements,
    SUM(CASE WHEN p.statut = 'validé' THEN 1 ELSE 0 END) as paiements_valides,
    SUM(CASE WHEN p.statut = 'en_attente' THEN 1 ELSE 0 END) as paiements_en_attente,
    SUM(CASE WHEN p.statut = 'rejeté' THEN 1 ELSE 0 END) as paiements_rejetes,
    SUM(CASE WHEN p.statut = 'validé' THEN p.montant ELSE 0 END) as montant_recu,
    SUM(p.montant) as montant_total
FROM candidats ca
LEFT JOIN concours c ON ca.concours_id = c.id
LEFT JOIN paiements p ON ca.id = p.candidat_id
GROUP BY c.id;

-- ==================== CREATE INDEXES ====================

CREATE INDEX idx_candidats_date_inscription ON candidats(date_inscription);
CREATE INDEX idx_paiements_statut_date ON paiements(statut, date_paiement);
CREATE INDEX idx_centres_places ON centres(nombre_places, places_occupees);

-- ==================== FINAL STATEMENTS ====================

-- Afficher les informations de base de données
SELECT 'Base de données créée avec succès' AS message;
SELECT COUNT(*) as nombre_regions FROM regions;
SELECT COUNT(*) as nombre_filieres FROM filieres;
SELECT COUNT(*) as nombre_concours FROM concours;

-- Fin du script
-- La base de données est maintenant prête à être utilisée avec l'application PHP
