-- Active: 1753357376335@@127.0.0.1@3306@memory
-- Création de la base de données "memory"
CREATE DATABASE IF NOT EXISTS memory;
USE memory;

-- Table pour les profils individuels
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour le classement et les scores individuels 
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    num_pairs INT NOT NULL,
    num_flips INT NOT NULL,
    score DECIMAL(10, 4) NOT NULL,
    game_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);