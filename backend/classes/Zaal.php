<?php
// backend/classes/Zaal.php
// Erft van Model

require_once __DIR__ . '\Model.php';

class Zaal extends Model
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

    public function getId(): int         { return $this->id; }
    public function getBioscoopId(): int { return $this->bioscoopId; }
    public function getNaam(): string    { return $this->naam; }
    public function getType(): string    { return $this->type; }

    protected static function vanRij(array $row): static
    {
        return new self($row['id'], $row['bioscoop_id'], $row['naam'], $row['type']);
    }

    public static function findById(int $id): ?self
    {
        $row = self::zoekOpId('zalen', $id);
        return $row ? self::vanRij($row) : null;
    }

    public static function getStoelen(int $zaalId): array
    {
        try {
            $stmt = self::db()->prepare("SELECT * FROM stoelen WHERE zaal_id = ? ORDER BY rij ASC, nummer ASC");
            $stmt->execute([$zaalId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) { return []; }
    }
}