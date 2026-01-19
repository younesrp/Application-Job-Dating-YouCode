CREATE DATABASE job_dating_youcode;
USE job_dating_youcode;

CREATE TABLE roles (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);
INSERT INTO roles (name) VALUES ('admin'), ('apprenant');

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_role
        FOREIGN KEY (role_id)
        REFERENCES roles(id)
);

CREATE TABLE students (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    promotion VARCHAR(50),
    specialization VARCHAR(100),
    CONSTRAINT fk_student_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE companies (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    sector VARCHAR(100),
    location VARCHAR(150),
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(30),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE job_offers (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    company_id INT NOT NULL,
    contract_type VARCHAR(50),
    location VARCHAR(150),
    image VARCHAR(255),
    description TEXT,
    deleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_offer_company
        FOREIGN KEY (company_id)
        REFERENCES companies(id)
        ON DELETE RESTRICT
);

CREATE TABLE skills (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE job_offer_skills (
    job_offer_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (job_offer_id, skill_id),
    CONSTRAINT fk_jos_offer
        FOREIGN KEY (job_offer_id)
        REFERENCES job_offers(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_jos_skill
        FOREIGN KEY (skill_id)
        REFERENCES skills(id)
        ON DELETE CASCADE
);
