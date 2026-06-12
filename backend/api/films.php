<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Pad klopt met jouw structuur: backend/api/ → backend/config/
require_once __DIR__ . '/../../config/db.php';

$methode = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();

    // ── GET: films ophalen ────────────────────────────────
    if ($methode === 'GET') {

        $zoek  = $_GET['zoek']  ?? '';
        $genre = $_GET['genre'] ?? '';

        $sql    = "SELECT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
                          MIN(v.datum) as eerste_datum, MIN(v.starttijd) as eerste_tijd
                   FROM films f
                   JOIN voorstellingen v ON f.id = v.film_id
                   WHERE v.datum >= CURDATE()";

        $params = [];

        if (!empty($zoek)) {
            $sql     .= " AND (f.titel LIKE ? OR f.genre LIKE ?)";
            $params[] = "%$zoek%";
            $params[] = "%$zoek%";
        }

        if (!empty($genre)) {
            $sql     .= " AND f.genre LIKE ?";
            $params[] = "%$genre%";
        }

        $sql .= " GROUP BY f.id ORDER BY eerste_datum ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $films = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $films]);

    // ── POST: film toevoegen ──────────────────────────────
    } elseif ($methode === 'POST') {

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['titel']) || empty($body['genre']) || empty($body['duur'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verplichte velden ontbreken']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO films (titel, genre, duur, leeftijd, beschrijving) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            htmlspecialchars($body['titel']),
            htmlspecialchars($body['genre']),
            (int) $body['duur'],
            htmlspecialchars($body['leeftijd'] ?? ''),
            htmlspecialchars($body['beschrijving'] ?? '')
        ]);

        echo json_encode(['success' => true, 'message' => 'Film toegevoegd', 'id' => $db->lastInsertId()]);

    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Databasefout']);
}