<?php
require_once 'backend/config/db.php';
require_once 'backend/classes/Film.php';
require_once 'backend/classes/Voorstelling.php';
require_once 'backend/classes/Zaal.php';
require_once 'backend/classes/Reservering.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$voorstellingId = isset($_GET['voorstelling']) ? (int) $_GET['voorstelling'] : 0;
$voorstelling   = Voorstelling::findById($voorstellingId);

if (!$voorstelling) {
    header('Location: index.php');
    exit;
}

$film    = Film::findById($voorstelling->getFilmId());
$zaal    = Zaal::findById($voorstelling->getZaalId());
$stoelen = Zaal::getStoelen($voorstelling->getZaalId());
$bezet   = Voorstelling::getBezetteStoelen($voorstellingId);
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $geselecteerd = $_POST['stoelen'] ?? [];

    if (empty($geselecteerd)) {
        $error = 'Selecteer minimaal één stoel.';
    } else {
        $geldige      = array_map('intval', $geselecteerd);
        $zaalStoelIds = array_column($stoelen, 'id');
        $geldige      = array_filter($geldige, fn($id) => in_array($id, $zaalStoelIds));
        $conflict     = array_intersect($geldige, $bezet);

        if (!empty($conflict)) {
            $error = 'Één of meer gekozen stoelen zijn al bezet.';
        } else {
            $totaal = count($geldige) * $voorstelling->getPrijs();
            $code   = Reservering::create((int) $_SESSION['user_id'], $voorstellingId, $totaal, $geldige);

            if ($code) {
                header('Location: bevestiging.php?code=' . urlencode($code));
                exit;
            } else {
                $error = 'Er is iets misgegaan. Probeer het opnieuw.';
            }
        }
    }
}

require_once 'header.php';
?>

<main class="checkout-wrapper">

    <header class="pagina-header">
        <a href="film.php?id=<?= $film->getId() ?>" class="btn-ghost small">← Terug</a>
        <h1>Kies je stoelen</h1>
    </header>

    <?php if ($error): ?>
        <p class="melding melding--fout"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <section class="checkout-grid">

        <article class="stoelenplan-sectie">
            <p class="scherm-label">SCHERM</p>
            <div class="scherm-balk"></div>

            <form method="POST" id="checkout-form">
                <ul class="stoelenplan" id="stoelenplan">
                    <?php
                    $perRij = [];
                    foreach ($stoelen as $stoel) { $perRij[$stoel['rij']][] = $stoel; }
                    ?>
                    <?php foreach ($perRij as $rij => $rijStoelen): ?>
                        <li class="stoel-rij">
                            <span class="rij-label"><?= htmlspecialchars($rij) ?></span>
                            <ul class="rij-stoelen">
                                <?php foreach ($rijStoelen as $stoel): ?>
                                    <?php $isBezet = in_array($stoel['id'], $bezet); ?>
                                    <li>
                                        <label class="stoel stoel--<?= htmlspecialchars($stoel['type']) ?> <?= $isBezet ? 'stoel--bezet' : '' ?>">
                                            <input type="checkbox" name="stoelen[]" value="<?= $stoel['id'] ?>"
                                                   class="stoel-checkbox" data-prijs="<?= $voorstelling->getPrijs() ?>"
                                                   <?= $isBezet ? 'disabled' : '' ?>>
                                            <span><?= $stoel['nummer'] ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <ul class="stoel-legenda">
                    <li><span class="legenda-stoel legenda-stoel--vrij"></span> Vrij</li>
                    <li><span class="legenda-stoel legenda-stoel--gekozen"></span> Gekozen</li>
                    <li><span class="legenda-stoel legenda-stoel--bezet"></span> Bezet</li>
                    <li><span class="legenda-stoel legenda-stoel--deluxe"></span> Deluxe</li>
                </ul>
            </form>
        </article>

        <aside class="checkout-samenvatting">
            <?php if ($film->getPoster()): ?>
                <figure class="samenvatting-poster">
                    <img src="<?= htmlspecialchars($film->getPoster()) ?>" alt="<?= htmlspecialchars($film->getTitel()) ?>">
                </figure>
            <?php endif; ?>

            <h2><?= htmlspecialchars($film->getTitel()) ?></h2>

            <ul class="samenvatting-details">
                <li><span> Datum</span><strong><?= $voorstelling->getDatumFormatted() ?></strong></li>
                <li><span> Tijd</span><strong><?= $voorstelling->getTijdFormatted() ?></strong></li>
                <li><span> Zaal</span><strong><?= htmlspecialchars($zaal->getNaam()) ?></strong></li>
                <li><span> Stoelen</span><strong id="samenvatting-stoelen">—</strong></li>
                <li><span> Prijs p/s</span><strong>€<?= number_format($voorstelling->getPrijs(), 2, ',', '.') ?></strong></li>
            </ul>

            <hr class="samenvatting-lijn">

            <p class="samenvatting-totaal">
                Totaal: <strong id="samenvatting-totaal">€0,00</strong>
            </p>

            <button type="submit" form="checkout-form" class="btn-submit" id="bestel-btn" disabled>
                Reserveer nu
            </button>

            <p class="checkout-info">Ingelogd als <strong><?= htmlspecialchars($_SESSION['voornaam']) ?></strong></p>
        </aside>

    </section>

</main>

<script>const prijsPerStoel = <?= $voorstelling->getPrijs() ?>;</script>
<script src="frontend/js/pages/stoelen.js"></script>
<script type="module" src="frontend/js/pages/checkout.js"></script>
<?php require_once 'footer.php'; ?>