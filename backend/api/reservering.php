<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '\\..\\..\\config\\db.php';
require_once __DIR__ . '\\..\\..\\classes\\Reservering.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Niet ingelogd'));
    exit;
}

$methode = $_SERVER['REQUEST_METHOD'];

if ($methode === 'GET') {
    $reserveringen = Reservering::vanGebruiker((int) $_SESSION['user_id']);
    echo json_encode(array('success' => true, 'data' => $reserveringen));

} elseif ($methode === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    $voorstellingId = (int) (isset($body['voorstelling_id']) ? $body['voorstelling_id'] : 0);
    $stoelIds       = isset($body['stoel_ids']) ? $body['stoel_ids'] : array();

    if (!$voorstellingId || empty($stoelIds)) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Voorstelling of stoelen ontbreken'));
        exit;
    }

    // Prijs ophalen
    try {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT prijs FROM voorstellingen WHERE id = ?");
        $stmt->execute(array($voorstellingId));
        $row  = $stmt->fetch();
        if (!$row) {
            http_response_code(404);
            echo json_encode(array('success' => false, 'message' => 'Voorstelling niet gevonden'));
            exit;
        }
        $totaal = count($stoelIds) * $row['prijs'];
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Databasefout'));
        exit;
    }

    $code = Reservering::create((int) $_SESSION['user_id'], $voorstellingId, $totaal, $stoelIds);

    if ($code) {
        echo json_encode(array('success' => true, 'code' => $code, 'totaal' => $totaal));
    } else {
        http_response_code(500);
        echo json_encode(array('success' => false, 'message' => 'Reservering mislukt'));
    }
}