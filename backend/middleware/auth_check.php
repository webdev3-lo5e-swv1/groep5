<?php
// backend/middleware/auth_check.php
// Controleer of de gebruiker is ingelogd
// Gebruik: require_once aan het begin van elke beveiligde pagina

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}