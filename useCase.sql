CREATE DATABASE IF NOT EXISTS job_dating_youcode;
USE job_dating_youcode;

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'apprenant') DEFAULT 'apprenant',
    nom VARCHAR(50),
    prenom VARCHAR(50),
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO users (id, email, password, role, nom, prenom) 
VALUES (1, 'admin@youcode.ma', '$2y$10$YourHashedPasswordHere', 'admin', 'Admin', 'YouCode');

CREATE TABLE IF NOT EXISTS admins (
    user_id INT PRIMARY KEY,
    CONSTRAINT fk_admin_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS apprenants (
    user_id INT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    promotion VARCHAR(50),
    specialisation VARCHAR(100),
    CONSTRAINT fk_apprenant_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    secteur VARCHAR(100),
    ville VARCHAR(100),
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(30),
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    type_contrat VARCHAR(50),
    competences TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_deleted BOOLEAN DEFAULT FALSE,
    CONSTRAINT fk_annonce_entreprise
        FOREIGN KEY (entreprise_id)
        REFERENCES entreprises(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apprenant_id INT NOT NULL,
    annonce_id INT NOT NULL,
    date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP,
    message TEXT,
    cv_path VARCHAR(255),
    statut ENUM('en_attente','validee','refusee') DEFAULT 'en_attente',
    CONSTRAINT fk_candidature_apprenant
        FOREIGN KEY (apprenant_id)
        REFERENCES apprenants(user_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_candidature_annonce
        FOREIGN KEY (annonce_id)
        REFERENCES annonces(id)
        ON DELETE CASCADE
);
