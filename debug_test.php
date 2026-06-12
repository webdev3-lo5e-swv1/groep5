<?php
// Zet error handling aan
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "PHP Error [$errno]: $errstr in $errfile on line $errline\n";
    return true;
});

set_exception_handler(function($e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
});

echo "=== Direct API Test ===\n\n";

try {
    // Load config
    require_once __DIR__ . '/backend/config/db.php';
    echo "✓ Config loaded\n";
    
    // Get DB connection
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connected\n";
    
    // Simulate GET request
    $_GET['pagina'] = 1;
    $_GET['per_pagina'] = 9;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    // Execute query from films.php directly
    $pagina = max(1, (int) (isset($_GET['pagina']) ? $_GET['pagina'] : 1));
    $perPagina = min(24, (int) (isset($_GET['per_pagina']) ? $_GET['per_pagina'] : 9));
    $offset = ($pagina - 1) * $perPagina;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'datum';
    
    echo "✓ Parameters: pagina=$pagina, perPagina=$perPagina, offset=$offset\n";
    
    $where = array("v.datum >= CURDATE()");
    $params = array();
    
    $whereSQL = 'WHERE ' . implode(' AND ', $where);
    
    if ($sort === 'titel') {
        $orderSQL = 'f.titel ASC';
    } else {
        $orderSQL = 'eerste_datum ASC, f.titel ASC';
    }
    
    echo "✓ Query building...\n";
    
    // Count query
    $c = $db->prepare("SELECT COUNT(DISTINCT f.id) FROM films f JOIN voorstellingen v ON f.id = v.film_id $whereSQL");
    echo "✓ Prepared count query\n";
    $c->execute($params);
    echo "✓ Executed count query\n";
    $totaal = (int) $c->fetchColumn();
    echo "✓ Total films: $totaal\n";
    
    // Films query
    $stmt = $db->prepare("
        SELECT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
               MIN(v.datum) as eerste_datum,
               GROUP_CONCAT(
                   DISTINCT CONCAT(v.id, '|', TIME_FORMAT(v.starttijd, '%H:%i'))
                   ORDER BY v.starttijd ASC
               ) as tijden_raw
        FROM films f
        JOIN voorstellingen v ON f.id = v.film_id
        $whereSQL
        GROUP BY f.id
        ORDER BY $orderSQL
        LIMIT $perPagina OFFSET $offset
    ");
    echo "✓ Prepared films query\n";
    $stmt->execute($params);
    echo "✓ Executed films query\n";
    $films = $stmt->fetchAll();
    echo "✓ Fetched " . count($films) . " films\n";
    
    echo "\n✓ SUCCESS - JSON ready\n";
    echo json_encode(array('success' => true, 'films' => $films, 'totaal' => $totaal));
    
} catch (Exception $e) {
    echo "\n✗ EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>


