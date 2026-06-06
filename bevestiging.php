<?php
// bevestiging.php — Bevestigingspagina na reservering
// Gebruikt: PDO, OOP (Reservering class), session, XSS

require_once 'backend/config/db.php';
require_once 'backend/classes/Reservering.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Code ophalen uit URL
$code = isset($_GET['code']) ? htmlspecialchars(trim($_GET['code'])) : '';

if (empty($code)) {
    header('Location: index.php');
    exit;
}

// Reservering ophalen op code
$reservering = Reservering::opCode($code);

if (!$reservering) {
    header('Location: index.php');
    exit;
}

// Stoelen ophalen
$stoelen     = Reservering::getStoelen($reservering['id']);
$stoelLabels = array_map(fn($s) => $s['rij'] . $s['nummer'], $stoelen);

require_once 'header.php';
?>

<main class="bevestiging-wrapper">

    <section class="bevestiging-kaart">

        <span class="bevestiging-icoon"></span>
        <h1>Reservering bevestigd!</h1>
        <p class="bevestiging-subtitel">
            Bedankt <?= htmlspecialchars($_SESSION['voornaam']) ?>! Je reservering is aangemaakt.
        </p>

        <article class="bevestiging-code-blok">
            <small>Jouw reserveringscode</small>
            <strong class="bevestiging-code"><?= htmlspecialchars($reservering['code']) ?></strong>
            <small>Bewaar deze code — je hebt hem nodig bij de kassa</small>
        </article>

        <ul class="bevestiging-details">
            <li>
                <span>🎬 Film</span>
                <strong><?= htmlspecialchars($reservering['titel']) ?></strong>
            </li>
            <li>
                <span>Datum</span>
                <strong><?= date('l d F Y', strtotime($reservering['datum'])) ?></strong>
            </li>
            <li>
                <span>Tijd</span>
                <strong><?= substr($reservering['starttijd'], 0, 5) ?></strong>
            </li>
            <li>
                <span>Locatie</span>
                <strong><?= htmlspecialchars($reservering['bioscoop']) ?> — <?= htmlspecialchars($reservering['zaal']) ?></strong>
            </li>
            <li>
                <span> Stoelen</span>
                <strong><?= !empty($stoelLabels) ? htmlspecialchars(implode(', ', $stoelLabels)) : '—' ?></strong>
            </li>
            <li>
                <span>Totaal</span>
                <strong>€<?= number_format($reservering['totaal'], 2, ',', '.') ?></strong>
            </li>
        </ul>

        <nav class="bevestiging-acties">
            <a href="reserveringen.php" class="btn-primary">Mijn reserveringen</a>
            <a href="index.php" class="btn-ghost">Terug naar home</a>
        </nav>

    </section>

</main>

<?php require_once 'footer.php'; ?>