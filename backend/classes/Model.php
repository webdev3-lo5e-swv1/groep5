<?php
// backend/classes/Model.php
// Abstracte basisklasse — Film, User, Reservering, Voorstelling, Zaal erven hiervan
// Rubric: "Abstracte databaseklasse" + "Overerving toegepast"

require_once __DIR__ . '\\..\\config\\db.php';

abstract class Model
{
    // Gedeelde DB connectie voor alle child classes
    protected static function db(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    // Elke child class MOET dit implementeren
    abstract protected static function vanRij(array $row): static;

    // Generieke findById herbruikbaar in alle child classes
    protected static function zoekOpId(string $tabel, int $id): ?array
    {
        try {
            $stmt = self::db()->prepare("SELECT * FROM {$tabel} WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Generieke delete herbruikbaar in alle child classes
    protected static function verwijder(string $tabel, int $id): bool
    {
        try {
            $stmt = self::db()->prepare("DELETE FROM {$tabel} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}