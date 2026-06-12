<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../config/db.php';

try {
    $db = Database::getInstance()->getConnection();

    $pagina    = max(1, (int) (isset($_GET['pagina']) ? $_GET['pagina'] : 1));
    $perPagina = min(24, (int) (isset($_GET['per_pagina']) ? $_GET['per_pagina'] : 9));
    $offset    = ($pagina - 1) * $perPagina;
    $sort      = isset($_GET['sort'])  ? $_GET['sort']  : 'datum';
    $genre     = isset($_GET['genre']) ? $_GET['genre'] : '';
    $datum     = isset($_GET['datum']) ? $_GET['datum'] : '';
    $tijd      = isset($_GET['tijd'])  ? $_GET['tijd']  : '';

    $where  = array("v.datum >= CURDATE()");
    $params = array();

    if (!empty($genre)) {
        $genres    = explode(',', $genre);
        $condities = array();
        foreach ($genres as $g) {
            $condities[] = "f.genre LIKE ?";
        }
        $where[]   = '(' . implode(' OR ', $condities) . ')';
        foreach ($genres as $g) { $params[] = '%' . trim($g) . '%'; }
    }

    if (!empty($datum)) { $where[] = "v.datum = ?"; $params[] = $datum; }

    if (!empty($tijd)) {
        if ($tijd === 'ochtend')    $where[] = "TIME(v.starttijd) < '12:00:00'";
        elseif ($tijd === 'middag') $where[] = "TIME(v.starttijd) >= '12:00:00' AND TIME(v.starttijd) < '17:00:00'";
        elseif ($tijd === 'avond')  $where[] = "TIME(v.starttijd) >= '17:00:00'";
    }

    $whereSQL = 'WHERE ' . implode(' AND ', $where);
    $orderSQL = $sort === 'titel' ? 'f.titel ASC' : 'MIN(v.datum) ASC, f.titel ASC';

    // Totaal tellen
    $c = $db->prepare("SELECT COUNT(DISTINCT f.id) FROM films f JOIN voorstellingen v ON f.id = v.film_id $whereSQL");
    $c->execute($params);
    $totaal = (int) $c->fetchColumn();

    // Films ophalen
    $stmt = $db->prepare("
        SELECT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
               MIN(v.datum) as eerste_datum,
               GROUP_CONCAT(DISTINCT CONCAT(v.id, ':', TIME_FORMAT(v.starttijd, '%H:%i'))
                   ORDER BY v.datum ASC, v.starttijd ASC SEPARATOR ',') as tijden_raw
        FROM films f
        JOIN voorstellingen v ON f.id = v.film_id
        $whereSQL
        GROUP BY f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster
        ORDER BY $orderSQL
        LIMIT $perPagina OFFSET $offset
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $films = array();
    foreach ($rows as $row) {
        $tijden = array();
        if (!empty($row['tijden_raw'])) {
            foreach (explode(',', $row['tijden_raw']) as $t) {
                $p = explode(':', $t, 2);
                if (count($p) === 2) {
                    $tijden[] = array('voorstelling_id' => (int)$p[0], 'tijd' => $p[1]);
                }
            }
        }
        $films[] = array(
            'id'       => (int) $row['id'],
            'titel'    => $row['titel'],
            'genre'    => isset($row['genre']) ? $row['genre'] : '',
            'duur'     => (int) $row['duur'],
            'leeftijd' => isset($row['leeftijd']) ? $row['leeftijd'] : '',
            'poster'   => isset($row['poster']) ? $row['poster'] : null,
            'tijden'   => $tijden
        );
    }

    echo json_encode(['success' => true, 'films' => $films, 'totaal' => $totaal]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'films' => [], 'totaal' => 0, 'message' => $e->getMessage()]);
}