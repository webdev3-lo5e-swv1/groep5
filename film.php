<?php
header('Content-Type: application/json');

$db = new mysqli('localhost', 'root', '', 'jouw_database');

$result = $db->query("SELECT * FROM films");

$films = [];

while ($rij = $result->fetch_assoc()) {
    $films[] = [
        'id'          => $rij['id'],
        'titel'       => $rij['titel'],
        'genre'       => $rij['genre'],
        'duur'        => $rij['duur'],
        'leeftijd'    => $rij['PEGI'] . '+',        // PEGI → "16+"
        'poster'      => $rij['poster'],            // zelfde naam
        'beoordeling' => round($rij['rating'] * 10), // 9.3 → 93 (0-100 voor balk)
        'tijden'      => explode(',', $rij['tijden']),
        'jaar'        => $rij['jaar'],
        'beschrijving'=> $rij['beschrijving'],
    ];
}

echo json_encode([
    'films'  => $films,
    'totaal' => count($films)
]);