<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    echo json_encode(array('success' => true, 'bericht' => 'Uitgelogd'));
} else {
    // GET: check of gebruiker ingelogd is
    $ingelogd = isset($_SESSION['user_id']);
    $gebruiker = null;
    if ($ingelogd) {
        $voornaam = isset($_SESSION['voornaam']) ? $_SESSION['voornaam'] : '';
        $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'klant';
        $gebruiker = array(
            'id'       => $_SESSION['user_id'],
            'voornaam' => $voornaam,
            'rol'      => $rol
        );
    }
    echo json_encode(array(
        'success'    => true,
        'ingelogd'   => $ingelogd,
        'gebruiker'  => $gebruiker
    ));
}