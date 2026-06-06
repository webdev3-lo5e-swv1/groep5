<?php
// backend/classes/Zaal.php
// OOP: private properties, constructor, getters, static methods

require_once __DIR__ . '/../config/db.php';

class Zaal
{
    private int    $id;
    private int    $bioscoopId;
    private string $naam;
    private string $type;

    public function __construct(int $id, int $bioscoopId, string $naam, string $type)
    {
        $this->id         = $id;
        $this->bioscoopId = $bioscoopId;
        $this->naam       = $naam;
        $this->type       = $type;
    }

    // ── Getters ──────────────────────────────────────────
    public function getId(): int          { return $this->id; }
    public function getBioscoopId(): int  { return $this->bioscoopId; }
    public function getNaam(): string     { return $this->naam; }
    public function getType(): string     { return $this->type; }

    // ── READ: één zaal op ID ──────────────────────────────
    public static function findById(int $id): ?self
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM zalen WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $row  = $stmt->fetch();
            if (!$row) return null;
            return new self($row['id'], $row['bioscoop_id'], $row['naam'], $row['type']);
        } catch (PDOException $e) {
            return null;
        }
    }

    // ── READ: alle stoelen van een zaal ───────────────────
    public static function getStoelen(int $zaalId): array
    {
        try {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT * FROM stoelen
                WHERE zaal_id = ?
                ORDER BY rij ASC, nummer ASC
            ");
            $stmt->execute([$zaalId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}