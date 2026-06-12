<?php
// backend/api/films.php
// Geeft films terug als JSON met paginering, filters, sortering

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
        $sort      = $_GET['sort'] ?? 'datum';
        $genre     = $_GET['genre']  ?? '';
        $datum     = $_GET['datum']  ?? '';
        $tijd      = $_GET['tijd']   ?? '';

        $where  = ["v.datum >= CURDATE()"];
        $params = [];

        if (!empty($genre)) {
            $genres = explode(',', $genre);
            $placeholders = implode(',', array_fill(0, count($genres), '?'));
            $genreCondities = array_map(fn($g) => "f.genre LIKE ?", $genres);
            $where[] = '(' . implode(' OR ', $genreCondities) . ')';
            foreach ($genres as $g) { $params[] = '%' . trim($g) . '%'; }
        }

        if (!empty($datum)) {
            $where[] = "v.datum = ?";
            $params[] = $datum;
        }

        if (!empty($tijd)) {
            if ($tijd === 'ochtend') { $where[] = "v.starttijd < '12:00'"; }
            elseif ($tijd === 'middag') { $where[] = "v.starttijd >= '12:00' AND v.starttijd < '17:00'"; }
            elseif ($tijd === 'avond') { $where[] = "v.starttijd >= '17:00'"; }
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $orderSQL = match($sort) {
            'titel'  => 'f.titel ASC',
            'rating' => 'f.titel ASC',
            default  => 'eerste_datum ASC, f.titel ASC'
        };

        // Totaal tellen
        $countStmt = $db->prepare("
            SELECT COUNT(DISTINCT f.id) as totaal
            FROM films f JOIN voorstellingen v ON f.id = v.film_id $whereSQL
        ");
        $countStmt->execute($params);
        $totaal = (int) $countStmt->fetchColumn();

        // Films ophalen met tijden
        $stmt = $db->prepare("
            SELECT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
                   MIN(v.datum) as eerste_datum,
                   GROUP_CONCAT(DISTINCT TIME_FORMAT(v.starttijd, '%H:%i') ORDER BY v.starttijd ASC) as tijden_raw
            FROM films f JOIN voorstellingen v ON f.id = v.film_id
            $whereSQL
            GROUP BY f.id
            ORDER BY $orderSQL
            LIMIT $perPagina OFFSET $offset
        ");
        $stmt->execute($params);
        $films = $stmt->fetchAll();

        // Tijden als array
        foreach ($films as &$film) {
            $film['tijden']     = $film['tijden_raw'] ? explode(',', $film['tijden_raw']) : [];
            $film['beoordeling'] = rand(60, 95); // placeholder
            unset($film['tijden_raw']);
        }

        echo json_encode(['success' => true, 'films' => $films, 'totaal' => $totaal, 'pagina' => $pagina]);

    } elseif ($methode === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['titel']) || empty($body['duur'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verplichte velden ontbreken']);
            exit;
        }
        $stmt = $db->prepare("INSERT INTO films (titel, genre, duur, leeftijd, beschrijving) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            htmlspecialchars($body['titel']),
            htmlspecialchars($body['genre'] ?? ''),
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