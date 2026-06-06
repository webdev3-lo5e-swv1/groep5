<?php
// backend/classes/Voorstelling.php
// OOP: private properties, constructor, getters, static methods

require_once __DIR__ . '/../config/db.php';

class Voorstelling
{
    private int    $id;
    private int    $filmId;
    private int    $zaalId;
    private string $datum;
    private string $starttijd;
    private string $eindtijd;
    private float  $prijs;

    public function __construct(
        int    $id,
        int    $filmId,
        int    $zaalId,
        string $datum,
        string $starttijd,
        string $eindtijd,
        float  $prijs
    ) {
        $this->id        = $id;
        $this->filmId    = $filmId;
        $this->zaalId    = $zaalId;
        $this->datum     = $datum;
        $this->starttijd = $starttijd;
        $this->eindtijd  = $eindtijd;
        $this->prijs     = $prijs;
    }

    // ── Getters ──────────────────────────────────────────
    public function getId(): int        { return $this->id; }
    public function getFilmId(): int    { return $this->filmId; }
    public function getZaalId(): int    { return $this->zaalId; }
    public function getDatum(): string  { return $this->datum; }
    public function getStarttijd(): string { return $this->starttijd; }
    public function getEindtijd(): string  { return $this->eindtijd; }
    public function getPrijs(): float   { return $this->prijs; }

    public function getDatumFormatted(): string
    {
        return date('d M Y', strtotime($this->datum));
    }

    public function getTijdFormatted(): string
    {
        return substr($this->starttijd, 0, 5) . ' – ' . substr($this->eindtijd, 0, 5);
    }

    // ── READ: alle voorstellingen van een film ────────────
    public static function vanFilm(int $filmId): array
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT v.*, z.naam as zaal_naam, z.type as zaal_type,
                       b.naam as bioscoop_naam, b.stad
                FROM voorstellingen v
                JOIN zalen z     ON v.zaal_id = z.id
                JOIN bioscopen b ON z.bioscoop_id = b.id
                WHERE v.film_id = ? AND v.datum >= CURDATE()
                ORDER BY v.datum ASC, v.starttijd ASC
            ");
            $stmt->execute([$filmId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // ── READ: één voorstelling op ID ─────────────────────
    public static function findById(int $id): ?self
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM voorstellingen WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $row  = $stmt->fetch();
            if (!$row) return null;
            return new self(
                $row['id'], $row['film_id'], $row['zaal_id'],
                $row['datum'], $row['starttijd'], $row['eindtijd'], $row['prijs']
            );
        } catch (PDOException $e) {
            return null;
        }
    }

    // ── READ: bezette stoelen van een voorstelling ────────
    public static function getBezetteStoelen(int $voorstellingId): array
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT rs.stoel_id
                FROM reservering_stoelen rs
                JOIN reserveringen r ON rs.reservering_id = r.id
                WHERE r.voorstelling_id = ? AND r.status != 'geannuleerd'
            ");
            $stmt->execute([$voorstellingId]);
            return array_column($stmt->fetchAll(), 'stoel_id');
        } catch (PDOException $e) {
            return [];
        }
    }
}