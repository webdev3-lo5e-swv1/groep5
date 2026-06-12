<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$huidigePagina = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Sanae & Mateusz">
    <meta name="keywords" content="bioscopen, films, popcorn, cinema, reserveren, kijken">
    <title>MBO Cinemas</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo"><span class="logo-dot"></span> MBO Cinemas</a>
    <ul>
        <li><a href="index.php" class="<?= $huidigePagina === 'index.php' ? 'actief' : '' ?>">Films</a></li>
        <li><a href="films.php" class="<?= $huidigePagina === 'films.php' ? 'actief' : '' ?>">Bioscopen</a></li>
        <li><a href="#">Aanbiedingen</a></li>
        <li><a href="#">Over ons</a></li>
    </ul>
    <p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn-ghost">👤 <?= htmlspecialchars($_SESSION['voornaam']) ?></a>
            <a href="uitloggen.php" class="btn-ghost">Uitloggen</a>
        <?php else: ?>
            <a href="login.php" class="btn-ghost <?= $huidigePagina === 'login.php' ? 'actief' : '' ?>">Inloggen</a>
            <a href="films.php" class="btn-primary">Reserveer</a>
        <?php endif; ?>
    </p>
</nav>