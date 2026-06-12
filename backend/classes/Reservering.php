<?php
// backend/classes/Reservering.php
// Erft van Model

require_once dirname(__FILE__) . '/Model.php';

class Reservering extends Model
{
    private int    $id;
    private ?int   $userId;
    private int    $voorstellingId;
    private string $code;
    private float  $totaal;
    private string $status;
    private string $aangemaaktOp;

    public function __construct(int $id, ?int $userId, int $voorstellingId, string $code, float $totaal, string $status, string $aangemaaktOp)
    {
        $this->id             = $id;
        $this->userId         = $userId;
        $this->voorstellingId = $voorstellingId;
        $this->code           = $code;
        $this->totaal         = $totaal;
        $this->status         = $status;
        $this->aangemaaktOp   = $aangemaaktOp;
    }

    public function getId(): int              { return $this->id; }
    public function getUserId(): ?int         { return $this->userId; }
    public function getVoorstellingId(): int  { return $this->voorstellingId; }
    public function getCode(): string         { return $this->code; }
    public function getTotaal(): float        { return $this->totaal; }
    public function getStatus(): string       { return $this->status; }
    public function getAangemaaktOp(): string { return $this->aangemaaktOp; }

    protected static function vanRij(array $row): static
    {
        return new self($row['id'], $row['user_id'], $row['voorstelling_id'], $row['code'], $row['totaal'], $row['status'], $row['aangemaakt_op']);
    }

    public static function vanGebruiker(int $userId): array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT r.*, f.titel, f.poster, v.datum, v.starttijd, b.naam as bioscoop, z.naam as zaal
                FROM reserveringen r
                JOIN voorstellingen v ON r.voorstelling_id = v.id
                JOIN films f ON v.film_id = f.id
                JOIN zalen z ON v.zaal_id = z.id
                JOIN bioscopen b ON z.bioscoop_id = b.id
                WHERE r.user_id = ? ORDER BY r.aangemaakt_op DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public static function opCode(string $code): ?array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT r.*, f.titel, f.poster, v.datum, v.starttijd, b.naam as bioscoop, z.naam as zaal
                FROM reserveringen r
                JOIN voorstellingen v ON r.voorstelling_id = v.id
                JOIN films f ON v.film_id = f.id
                JOIN zalen z ON v.zaal_id = z.id
                JOIN bioscopen b ON z.bioscoop_id = b.id
                WHERE r.code = ? LIMIT 1
            ");
            $stmt->execute([$code]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) { return null; }
    }

    public static function getStoelen(int $reserveringId): array
    {
        try {
            $stmt = self::db()->prepare("
                SELECT s.rij, s.nummer, s.type FROM reservering_stoelen rs
                JOIN stoelen s ON rs.stoel_id = s.id WHERE rs.reservering_id = ?
            ");
            $stmt->execute([$reserveringId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }

    public static function create(?int $userId, int $voorstellingId, float $totaal, array $stoelIds): ?string
    {
        try {
            $db   = self::db();
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO reserveringen (user_id, voorstelling_id, code, totaal, status) VALUES (?, ?, ?, ?, 'in_behandeling')");
            $stmt->execute([$userId, $voorstellingId, $code, $totaal]);
            $reserveringId = (int) $db->lastInsertId();
            $s = $db->prepare("INSERT INTO reservering_stoelen (reservering_id, stoel_id) VALUES (?, ?)");
            foreach ($stoelIds as $stoelId) { $s->execute([$reserveringId, (int) $stoelId]); }
            $db->commit();
            return $code;
        } catch (PDOException $e) {
            self::db()->rollBack();
            return null;
        }
    }

    public static function updateStatus(int $id, string $nieuweStatus): bool
    {
        try {
            $stmt = self::db()->prepare("UPDATE reserveringen SET status = ? WHERE id = ?");
            return $stmt->execute([$nieuweStatus, $id]);
        } catch (PDOException $e) { return false; }
    }

    public static function annuleer(int $id, int $userId): bool
    {
        try {
            $stmt = self::db()->prepare("UPDATE reserveringen SET status = 'geannuleerd' WHERE id = ? AND user_id = ? AND status = 'in_behandeling'");
            return $stmt->execute([$id, $userId]);
        } catch (PDOException $e) { return false; }
    }
}