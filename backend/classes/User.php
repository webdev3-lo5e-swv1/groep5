<?php

require_once __DIR__ . '/../config/db.php';

class User
{
    private int    $id;
    private string $voornaam;
    private string $achternaam;
    private string $email;
    private string $wachtwoord;
    private string $rol;

    public function __construct(int $id, string $voornaam, string $achternaam, string $email, string $wachtwoord, string $rol = 'klant')
    {
        $this->id         = $id;
        $this->voornaam   = $voornaam;
        $this->achternaam = $achternaam;
        $this->email      = $email;
        $this->wachtwoord = $wachtwoord;
        $this->rol        = $rol;
    }

    // Getters
    public function getId(): int          { return $this->id; }
    public function getVoornaam(): string  { return $this->voornaam; }
    public function getAchternaam(): string{ return $this->achternaam; }
    public function getNaam(): string      { return $this->voornaam . ' ' . $this->achternaam; }
    public function getEmail(): string    { return $this->email; }
    public function getWachtwoord(): string{ return $this->wachtwoord; }
    public function getRol(): string      { return $this->rol; }

    //zoek gebruiker op e-mail
    public static function findByEmail(string $email): ?self
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, voornaam, achternaam, email, wachtwoord, rol FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row  = $stmt->fetch();
            if (!$row) return null;
            return new self($row['id'], $row['voornaam'], $row['achternaam'], $row['email'], $row['wachtwoord'], $row['rol']);
        } catch (PDOException $e) {
            return null;
        }
    }

    //zoek gebruiker op ID
    public static function findById(int $id): ?self
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, voornaam, achternaam, email, wachtwoord, rol FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $row  = $stmt->fetch();
            if (!$row) return null;
            return new self($row['id'], $row['voornaam'], $row['achternaam'], $row['email'], $row['wachtwoord'], $row['rol']);
        } catch (PDOException $e) {
            return null;
        }
    }

    //nieuwe gebruiker aanmaken 
    public static function create(string $voornaam, string $achternaam, string $email, string $hashedWachtwoord): bool
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO users (voornaam, achternaam, email, wachtwoord, rol) VALUES (?, ?, ?, ?, 'klant')");
            return $stmt->execute([$voornaam, $achternaam, $email, $hashedWachtwoord]);
        } catch (PDOException $e) {
            return false;
        }
    }
}