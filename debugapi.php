<?php
// debug-api.php — zet dit tijdelijk in je root, bezoek localhost/project4/debug-api.php
// Verwijder dit na het debuggen!

require_once 'backend/config/db.php';

echo "<h2>Database test</h2>";

try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color:green'>✅ Database verbinding OK</p>";

    $films = $db->query("SELECT COUNT(*) as n FROM films")->fetch();
    echo "<p>Films in DB: <strong>{$films['n']}</strong></p>";

    $voorstellingen = $db->query("SELECT COUNT(*) as n FROM voorstellingen WHERE datum >= CURDATE()")->fetch();
    echo "<p>Komende voorstellingen: <strong>{$voorstellingen['n']}</strong></p>";

    $metPoster = $db->query("SELECT COUNT(*) as n FROM films WHERE poster IS NOT NULL AND poster != ''")->fetch();
    echo "<p>Films met poster: <strong>{$metPoster['n']}</strong></p>";

    echo "<h3>Test API response:</h3>";
    $stmt = $db->query("
        SELECT f.id, f.titel, f.poster, MIN(v.datum) as datum
        FROM films f JOIN voorstellingen v ON f.id = v.film_id
        WHERE v.datum >= CURDATE()
        GROUP BY f.id LIMIT 3
    ");
    $rows = $stmt->fetchAll();
    echo "<pre>" . json_encode($rows, JSON_PRETTY_PRINT) . "</pre>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Database fout: " . htmlspecialchars($e->getMessage()) . "</p>";
}