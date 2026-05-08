-- MBO Cinemas database schema
-- Gebruik in XAMPP: open phpMyAdmin, maak database aan en importeer dit bestand

CREATE DATABASE IF NOT EXISTS mbo_cinemas;
USE mbo_cinemas;

-- Gebruikers (klanten + medewerkers)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam VARCHAR(50) NOT NULL,
    achternaam VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL,
    rol ENUM('klant', 'medewerker') DEFAULT 'klant',
    onthoud_token VARCHAR(255) DEFAULT NULL,
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Films
CREATE TABLE films (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(100) NOT NULL,
    beschrijving TEXT,
    genre VARCHAR(50),
    duur INT NOT NULL,
    leeftijd VARCHAR(5),
    taal VARCHAR(50),
    regisseur VARCHAR(100),
    cast TEXT,
    poster VARCHAR(255),
    trailer_url VARCHAR(255),
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Bioscopen / vestigingen
CREATE TABLE bioscopen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    stad VARCHAR(50) NOT NULL,
    adres VARCHAR(150)
);

-- Zalen
CREATE TABLE zalen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bioscoop_id INT NOT NULL,
    naam VARCHAR(50) NOT NULL,
    type ENUM('normaal', 'IMAX') DEFAULT 'normaal',
    FOREIGN KEY (bioscoop_id) REFERENCES bioscopen(id)
);

-- Stoelen per zaal
CREATE TABLE stoelen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zaal_id INT NOT NULL,
    rij CHAR(1) NOT NULL,
    nummer INT NOT NULL,
    type ENUM('standaard', 'deluxe', 'mindervalide') DEFAULT 'standaard',
    FOREIGN KEY (zaal_id) REFERENCES zalen(id)
);

-- Voorstellingen
CREATE TABLE voorstellingen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    film_id INT NOT NULL,
    zaal_id INT NOT NULL,
    datum DATE NOT NULL,
    starttijd TIME NOT NULL,
    eindtijd TIME NOT NULL,
    prijs DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (film_id) REFERENCES films(id),
    FOREIGN KEY (zaal_id) REFERENCES zalen(id)
);

-- Reserveringen
CREATE TABLE reserveringen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    voorstelling_id INT NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    totaal DECIMAL(6,2) NOT NULL,
    status ENUM('in_behandeling', 'betaald', 'geannuleerd') DEFAULT 'in_behandeling',
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (voorstelling_id) REFERENCES voorstellingen(id)
);

-- Gereserveerde stoelen per reservering
CREATE TABLE reservering_stoelen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservering_id INT NOT NULL,
    stoel_id INT NOT NULL,
    FOREIGN KEY (reservering_id) REFERENCES reserveringen(id),
    FOREIGN KEY (stoel_id) REFERENCES stoelen(id)
);

-- Extra's (popcorn, drinken etc.)
CREATE TABLE extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    prijs DECIMAL(5,2) NOT NULL
);

-- Bestelde extra's per reservering
CREATE TABLE reservering_extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservering_id INT NOT NULL,
    extra_id INT NOT NULL,
    aantal INT DEFAULT 1,
    FOREIGN KEY (reservering_id) REFERENCES reserveringen(id),
    FOREIGN KEY (extra_id) REFERENCES extras(id)
);