<?php
// backend/api/films.php
// Geeft films terug als JSON — gebruikt door films.js
// Rubric: Datacommunicatie endpoint, PDO, prepared statements

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db.php';

$methode = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance()->getConnection();

    if ($methode === 'GET') {
    $pagina    = max(1, (int) ($_GET['pagina']    ?? 1));
    $perPagina = min(24, (int) ($_GET['per_pagina'] ?? 9));
    $offset    = ($pagina - 1) * $perPagina;
    $sort      = $_GET['sort']  ?? 'datum';
    $genre     = $_GET['genre'] ?? '';
    $datum     = $_GET['datum'] ?? '';
    $tijd      = $_GET['tijd']  ?? '';

    $where  = ["v.datum >= CURDATE()"];
    $params = [];

    if (!empty($genre)) {
        $genres = explode(',', $genre);
        $genreCondities = array();
        foreach ($genres as $g) {
            $genreCondities[] = "f.genre LIKE ?";
            $params[] = '%' . trim($g) . '%';
        }
        $where[] = '(' . implode(' OR ', $genreCondities) . ')';
    }

        if (!empty($datum)) {
            $where[] = "v.datum = ?";
            $params[] = $datum;
        }

    if (!empty($tijd)) {
            if ($tijd === 'ochtend')     $where[] = "v.starttijd < '12:00'";
            elseif ($tijd === 'middag')  $where[] = "v.starttijd >= '12:00' AND v.starttijd < '17:00'";
            elseif ($tijd === 'avond')   $where[] = "v.starttijd >= '17:00'";
    }

    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    if ($sort === 'titel') {
        $orderSQL = 'f.titel ASC';
    } else {
        $orderSQL = 'eerste_datum ASC, f.titel ASC';
    }

        // Totaal tellen voor paginering
        $countStmt = $db->prepare("
            SELECT COUNT(DISTINCT f.id) as totaal
            FROM films f
            JOIN voorstellingen v ON f.id = v.film_id
            $whereSQL
        ");
        $countStmt->execute($params);
        $totaal = (int) $countStmt->fetchColumn();

        // Films ophalen met tijden en poster
    $stmt = $db->prepare("
        SELECT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
               MIN(v.datum) as eerste_datum,
                   GROUP_CONCAT(
                       DISTINCT CONCAT(v.id, '|', TIME_FORMAT(v.starttijd, '%H:%i'))
                       ORDER BY v.starttijd ASC
                   ) as tijden_raw
        FROM films f
        JOIN voorstellingen v ON f.id = v.film_id
        $whereSQL
            GROUP BY f.id
        ORDER BY $orderSQL
        LIMIT $perPagina OFFSET $offset
    ");
    $stmt->execute($params);
        $films = $stmt->fetchAll();

        // Tijden als array met voorstelling ID erbij
        foreach ($films as &$film) {
        $tijden = [];
            if ($film['tijden_raw']) {
                foreach (explode(',', $film['tijden_raw']) as $t) {
                    [$vid, $tijd_str] = explode('|', $t);
                    $tijden[] = ['voorstelling_id' => (int)$vid, 'tijd' => $tijd_str];
            }
        }
            $film['tijden']      = $tijden;
            $film['beoordeling'] = 75; // placeholder
            unset($film['tijden_raw']);
        }

        // films.js verwacht: { films: [...], totaal: N }
        echo json_encode(['success' => true, 'films' => $films, 'totaal' => $totaal, 'pagina' => $pagina]);

    } elseif ($methode === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['titel']) || empty($body['duur'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verplichte velden ontbreken']);
            exit;
        }
        $stmt = $db->prepare("INSERT INTO films (titel, genre, duur, leeftijd, beschrijving, poster) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            htmlspecialchars($body['titel']),
            htmlspecialchars($body['genre'] ?? ''),
            (int) $body['duur'],
            htmlspecialchars($body['leeftijd'] ?? ''),
            htmlspecialchars($body['beschrijving'] ?? ''),
            $body['poster'] ?? null
        ]);
        echo json_encode(['success' => true, 'message' => 'Film toegevoegd', 'id' => $db->lastInsertId()]);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Methode niet toegestaan']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Databasefout: ' . $e->getMessage()]);
}