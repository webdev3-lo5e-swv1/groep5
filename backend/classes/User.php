<?php
// backend/classes/User.php
// Erft van Model

require_once dirname(__FILE__) . '/Model.php';

class User extends Model
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

    public function getId(): int           { return $this->id; }
    public function getVoornaam(): string  { return $this->voornaam; }
    public function getAchternaam(): string { return $this->achternaam; }
    public function getNaam(): string      { return $this->voornaam . ' ' . $this->achternaam; }
    public function getEmail(): string     { return $this->email; }
    public function getWachtwoord(): string { return $this->wachtwoord; }
    public function getRol(): string       { return $this->rol; }

    protected static function vanRij(array $row): static
    {
        return new self($row['id'], $row['voornaam'], $row['achternaam'], $row['email'], $row['wachtwoord'], $row['rol'] ?? 'klant');
    }

    // READ
    public static function findByEmail(string $email): ?self
    {
        try {
            $stmt = self::db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row = $stmt->fetch();
            return $row ? self::vanRij($row) : null;
        } catch (PDOException $e) { return null; }
    }

    public static function findById(int $id): ?self
    {
        $row = self::zoekOpId('users', $id);
        return $row ? self::vanRij($row) : null;
    }

    // CREATE
    public static function create(string $voornaam, string $achternaam, string $email, string $hashedWachtwoord): bool
    {
        try {
            $stmt = self::db()->prepare("INSERT INTO users (voornaam, achternaam, email, wachtwoord, rol) VALUES (?, ?, ?, ?, 'klant')");
            return $stmt->execute([$voornaam, $achternaam, $email, $hashedWachtwoord]);
        } catch (PDOException $e) { return false; }
    }
}