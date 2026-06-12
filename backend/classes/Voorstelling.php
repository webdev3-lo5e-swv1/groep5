<?php
// backend/classes/Voorstelling.php
// Erft van Model

require_once dirname(__FILE__) . '/Model.php';

class Voorstelling extends Model
{
    private int    $id;
    private int    $filmId;
    private int    $zaalId;
    private string $datum;
    private string $starttijd;
    private string $eindtijd;
    private float  $prijs;

    public function __construct(int $id, int $filmId, int $zaalId, string $datum, string $starttijd, string $eindtijd, float $prijs)
    {
        $this->id        = $id;
        $this->filmId    = $filmId;
        $this->zaalId    = $zaalId;
        $this->datum     = $datum;
        $this->starttijd = $starttijd;
        $this->eindtijd  = $eindtijd;
        $this->prijs     = $prijs;
    }

    public function getId(): int          { return $this->id; }
    public function getFilmId(): int      { return $this->filmId; }
    public function getZaalId(): int      { return $this->zaalId; }
    public function getDatum(): string    { return $this->datum; }
    public function getStarttijd(): string { return $this->starttijd; }
    public function getEindtijd(): string  { return $this->eindtijd; }
    public function getPrijs(): float     { return $this->prijs; }
    public function getDatumFormatted(): string { return date('d M Y', strtotime($this->datum)); }
    public function getTijdFormatted(): string  { return substr($this->starttijd, 0, 5) . ' – ' . substr($this->eindtijd, 0, 5); }

    protected static function vanRij(array $row): static
    {
        return new self($row['id'], $row['film_id'], $row['zaal_id'], $row['datum'], $row['starttijd'], $row['eindtijd'], $row['prijs']);
    }

    public static function findById(int $id): ?self
    {
        $row = self::zoekOpId('voorstellingen', $id);
        return $row ? self::vanRij($row) : null;
    }

    public static function vanFilm(int $filmId): array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT v.*, z.naam as zaal_naam, z.type as zaal_type, b.naam as bioscoop_naam, b.stad
                FROM voorstellingen v
                JOIN zalen z ON v.zaal_id = z.id
                JOIN bioscopen b ON z.bioscoop_id = b.id
                WHERE v.film_id = ? AND v.datum >= CURDATE()
                ORDER BY v.datum ASC, v.starttijd ASC
            ");
            $stmt->execute([$filmId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public static function getBezetteStoelen(int $voorstellingId): array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT rs.stoel_id FROM reservering_stoelen rs
                JOIN reserveringen r ON rs.reservering_id = r.id
                WHERE r.voorstelling_id = ? AND r.status != 'geannuleerd'
            ");
            $stmt->execute([$voorstellingId]);
            return array_column($stmt->fetchAll(), 'stoel_id');
        } catch (PDOException $e) { return []; }
    }
}