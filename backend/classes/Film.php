<?php
// backend/classes/Film.php
// Erft van Model (abstracte basisklasse)

require_once __DIR__ . '\\Model.php';

class Film extends Model
{
    private int     $id;
    private string  $titel;
    private string  $beschrijving;
    private string  $genre;
    private int     $duur;
    private string  $leeftijd;
    private string  $taal;
    private string  $regisseur;
    private string  $cast;
    private ?string $poster;
    private ?string $trailerUrl;

    public function __construct(
        int $id, string $titel, string $beschrijving, string $genre,
        int $duur, string $leeftijd, string $taal, string $regisseur,
        string $cast, ?string $poster, ?string $trailerUrl
    ) {
        $this->id           = $id;
        $this->titel        = $titel;
        $this->beschrijving = $beschrijving;
        $this->genre        = $genre;
        $this->duur         = $duur;
        $this->leeftijd     = $leeftijd;
        $this->taal         = $taal;
        $this->regisseur    = $regisseur;
        $this->cast         = $cast;
        $this->poster       = $poster;
        $this->trailerUrl   = $trailerUrl;
    }

    // Getters
    public function getId(): int            { return $this->id; }
    public function getTitel(): string      { return $this->titel; }
    public function getBeschrijving(): string { return $this->beschrijving; }
    public function getGenre(): string      { return $this->genre; }
    public function getDuur(): int          { return $this->duur; }
    public function getLeeftijd(): string   { return $this->leeftijd; }
    public function getTaal(): string       { return $this->taal; }
    public function getRegisseur(): string  { return $this->regisseur; }
    public function getCast(): string       { return $this->cast; }
    public function getPoster(): ?string    { return $this->poster; }
    public function getTrailerUrl(): ?string { return $this->trailerUrl; }

    public function getDuurFormatted(): string
    {
        $uren    = intdiv($this->duur, 60);
        $minuten = $this->duur % 60;
        return $uren > 0 ? "{$uren}u {$minuten}m" : "{$minuten}m";
    }

    // Verplichte implementatie van abstracte methode
    protected static function vanRij(array $row): static
    {
        return new self(
            $row['id'], $row['titel'], $row['beschrijving'] ?? '',
            $row['genre'] ?? '', $row['duur'], $row['leeftijd'] ?? '',
            $row['taal'] ?? '', $row['regisseur'] ?? '', $row['cast'] ?? '',
            $row['poster'] ?? null, $row['trailer_url'] ?? null
        );
    }

    // READ
    public static function findById(int $id): ?self
    {
        $row = self::zoekOpId('films', $id); // gebruik methode van Model
        return $row ? self::vanRij($row) : null;
    }

    public static function alle(): array
    {
        try {
            $stmt = self::db()->query("SELECT * FROM films ORDER BY titel ASC");
            return array_map([self::class, 'vanRij'], $stmt->fetchAll());
        } catch (PDOException $e) { return []; }
    }

    // CREATE
    public static function create(array $data): bool
    {
        try {
            $stmt = self::db()->prepare("
                INSERT INTO films (titel, beschrijving, genre, duur, leeftijd, taal, regisseur, cast, poster, trailer_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                htmlspecialchars($data['titel']),
                htmlspecialchars($data['beschrijving'] ?? ''),
                htmlspecialchars($data['genre'] ?? ''),
                (int) $data['duur'],
                htmlspecialchars($data['leeftijd'] ?? ''),
                htmlspecialchars($data['taal'] ?? ''),
                htmlspecialchars($data['regisseur'] ?? ''),
                htmlspecialchars($data['cast'] ?? ''),
                $data['poster'] ?? null,
                $data['trailer_url'] ?? null,
            ]);
        } catch (PDOException $e) { return false; }
    }

    // UPDATE
    public static function update(int $id, array $data): bool
    {
        try {
            $stmt = self::db()->prepare("
                UPDATE films SET titel=?, beschrijving=?, genre=?, duur=?, leeftijd=?,
                taal=?, regisseur=?, cast=?, poster=?, trailer_url=? WHERE id=?
            ");
            return $stmt->execute([
                htmlspecialchars($data['titel']),
                htmlspecialchars($data['beschrijving'] ?? ''),
                htmlspecialchars($data['genre'] ?? ''),
                (int) $data['duur'],
                htmlspecialchars($data['leeftijd'] ?? ''),
                htmlspecialchars($data['taal'] ?? ''),
                htmlspecialchars($data['regisseur'] ?? ''),
                htmlspecialchars($data['cast'] ?? ''),
                $data['poster'] ?? null,
                $data['trailer_url'] ?? null,
                $id
            ]);
        } catch (PDOException $e) { return false; }
    }

    // DELETE — gebruik methode van Model
    public static function delete(int $id): bool
    {
        return self::verwijder('films', $id);
    }
}