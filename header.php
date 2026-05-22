<?php
$huidigePagina = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MBO Cinemas</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<nav class="navbar">
    <span class="logo"><span class="logo-dot"></span> MBO Cinemas</span>
    <ul>
        <li><a href="index.php" class="<?= $huidigePagina === 'index.php' ? 'actief' : '' ?>">Films</a></li>
        <li><a href="films.php" class="<?= $huidigePagina === 'films.php' ? 'actief' : '' ?>">Bioscopen</a></li>
        <li><a href="#">Aanbiedingen</a></li>
        <li><a href="#">Over ons</a></li>
    </ul>
    <p>
        <a href="login.php" class="btn-ghost">Inloggen</a>
        <a href="films.php" class="btn-primary">Reserveer</a>
    </p>
</nav>