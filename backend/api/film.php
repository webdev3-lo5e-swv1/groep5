<?php
// backend/api/film.php
// Geeft ÉÉN film terug met voorstellingen — gebruikt door film.php detailpagina

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../classes/Film.php';
require_once __DIR__ . '/../../classes/Voorstelling.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geen ID opgegeven']);
    exit;
}

$film = Film::findById($id);

if (!$film) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Film niet gevonden']);
    exit;
}

$voorstellingen = Voorstelling::vanFilm($id);

echo json_encode([
    'success' => true,
    'data'    => [
        'id'             => $film->getId(),
        'titel'          => $film->getTitel(),
        'beschrijving'   => $film->getBeschrijving(),
        'genre'          => $film->getGenre(),
        'duur'           => $film->getDuur(),
        'duur_formatted' => $film->getDuurFormatted(),
        'leeftijd'       => $film->getLeeftijd(),
        'taal'           => $film->getTaal(),
        'regisseur'      => $film->getRegisseur(),
        'cast'           => $film->getCast(),
        'poster'         => $film->getPoster(),
        'trailer_url'    => $film->getTrailerUrl(),
        'voorstellingen' => $voorstellingen
    ]
]);