<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    echo json_encode(['success' => true, 'bericht' => 'Uitgelogd']);
} else {
    // GET: check of gebruiker ingelogd is
    echo json_encode([
        'success'    => true,
        'ingelogd'   => isset($_SESSION['user_id']),
        'gebruiker'  => isset($_SESSION['user_id']) ? [
            'id'       => $_SESSION['user_id'],
            'voornaam' => $_SESSION['voornaam'] ?? '',
            'rol'      => $_SESSION['rol'] ?? 'klant'
        ] : null
    ]);
}