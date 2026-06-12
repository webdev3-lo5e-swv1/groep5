<?php
// backend/api/admin/planning.php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../config/db.php';

if (!isset($_SESSION['user_id']) || (isset($_SESSION['rol']) ? $_SESSION['rol'] : '') !== 'medewerker') {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Geen toegang'));
    exit;
}

$methode = $_SERVER['REQUEST_METHOD'];
$db      = Database::getInstance()->getConnection();

try {
    if ($methode === 'GET') {
        $stmt = $db->query("
            SELECT v.*, f.titel, z.naam as zaal, b.naam as bioscoop, b.stad
            FROM voorstellingen v
            JOIN films f ON v.film_id = f.id
            JOIN zalen z ON v.zaal_id = z.id
            JOIN bioscopen b ON z.bioscoop_id = b.id
            WHERE v.datum >= CURDATE()
            ORDER BY v.datum ASC, v.starttijd ASC
        ");
        echo json_encode(array('success' => true, 'data' => $stmt->fetchAll()));

    } elseif ($methode === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $stmt = $db->prepare("
            INSERT INTO voorstellingen (film_id, zaal_id, datum, starttijd, eindtijd, prijs)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute(array(
            (int)   $body['film_id'],
            (int)   $body['zaal_id'],
            htmlspecialchars($body['datum']),
            htmlspecialchars($body['starttijd']),
            htmlspecialchars($body['eindtijd']),
            (float) $body['prijs']
        ));
        echo json_encode(array('success' => $ok, 'id' => $ok ? $db->lastInsertId() : null));

    } elseif ($methode === 'DELETE') {
        $id   = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $stmt = $db->prepare("DELETE FROM voorstellingen WHERE id = ?");
        $ok   = $stmt->execute(array($id));
        echo json_encode(array('success' => $ok));
    } else {
        http_response_code(405);
        echo json_encode(array('success' => false, 'message' => 'Methode niet toegestaan'));
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Databasefout'));
}