<?php
// backend/api/admin/reserveringen.php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '\..\..\..\config\db.php';
require_once __DIR__ . '\..\..\..\classes\Reservering.php';

$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : '';
if (!isset($_SESSION['user_id']) || $rol !== 'medewerker') {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Geen toegang'));
    exit;
}

$methode = $_SERVER['REQUEST_METHOD'];
$db      = Database::getInstance()->getConnection();

try {
    if ($methode === 'GET') {
        $status = isset($_GET['status']) ? $_GET['status'] : 'alle';
        $sql    = "
            SELECT r.*, f.titel, v.datum, v.starttijd,
                   CONCAT(u.voornaam, ' ', u.achternaam) as gebruiker, u.email
            FROM reserveringen r
            JOIN voorstellingen v ON r.voorstelling_id = v.id
            JOIN films f ON v.film_id = f.id
            LEFT JOIN users u ON r.user_id = u.id
        ";
        if ($status !== 'alle') {
            $stmt = $db->prepare($sql . " WHERE r.status = ? ORDER BY r.aangemaakt_op DESC");
            $stmt->execute(array($status));
        } else {
            $stmt = $db->query($sql . " ORDER BY r.aangemaakt_op DESC");
        }
        echo json_encode(array('success' => true, 'data' => $stmt->fetchAll()));

    } elseif ($methode === 'PUT') {
        $id   = (int) (isset($_GET['id']) ? $_GET['id'] : 0);
        $body = json_decode(file_get_contents('php://input'), true);
        $status_val = isset($body['status']) ? $body['status'] : '';
        $ok   = Reservering::updateStatus($id, htmlspecialchars($status_val));
        echo json_encode(array('success' => $ok));
    } else {
        http_response_code(405);
        echo json_encode(array('success' => false, 'message' => 'Methode niet toegestaan'));
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Databasefout'));
}