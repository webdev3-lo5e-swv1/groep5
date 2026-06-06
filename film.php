<?php
// film.php — Detailpagina van een film
// Gebruikt: PDO, OOP (Film + Voorstelling classes), XSS-beveiliging

require_once 'backend/config/db.php';
require_once 'backend/classes/Film.php';
require_once 'backend/classes/Voorstelling.php';

session_start();

// ID ophalen en valideren uit URL (?id=5)
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Film ophalen via OOP
$film = Film::findById($id);

// Film niet gevonden? Terug naar homepage
if (!$film) {
    header('Location: index.php');
    exit;
}

// Voorstellingen ophalen — gegroepeerd per datum
$voorstellingen = Voorstelling::vanFilm($film->getId());

// Groepeer per datum voor overzichtelijke weergave
$perDatum = [];
foreach ($voorstellingen as $v) {
    $perDatum[$v['datum']][] = $v;
}

require_once 'header.php';
?>

<main class="film-detail-wrapper">

    <!-- ── Hero sectie ── -->
    <section class="film-hero">
        <figure class="film-hero__poster">
            <?php if ($film->getPoster()): ?>
                <img src="<?= htmlspecialchars($film->getPoster()) ?>"
                     alt="<?= htmlspecialchars($film->getTitel()) ?>">
            <?php else: ?>
                <span class="poster-placeholder">POSTER</span>
            <?php endif; ?>
        </figure>

        <article class="film-hero__info">
            <small class="film-hero__label">NU IN DE ZAAL</small>
            <h1><?= htmlspecialchars($film->getTitel()) ?></h1>

            <ul class="film-tags">
                <?php foreach (explode(',', $film->getGenre()) as $g): ?>
                    <li class="tag"><?= trim(htmlspecialchars($g)) ?></li>
                <?php endforeach; ?>
                <li class="tag"><?= htmlspecialchars($film->getLeeftijd()) ?>+</li>
                <li class="tag"><?= $film->getDuurFormatted() ?></li>
                <li class="tag"><?= htmlspecialchars($film->getTaal()) ?></li>
            </ul>

            <p class="film-beschrijving"><?= htmlspecialchars($film->getBeschrijving()) ?></p>

            <ul class="film-meta-lijst">
                <li><span>Regisseur</span><strong><?= htmlspecialchars($film->getRegisseur()) ?></strong></li>
                <li><span>Cast</span><strong><?= htmlspecialchars($film->getCast()) ?></strong></li>
            </ul>

            <?php if ($film->getTrailerUrl()): ?>
                <a href="<?= htmlspecialchars($film->getTrailerUrl()) ?>"
                   target="_blank" class="btn-ghost">▶ Bekijk trailer</a>
            <?php endif; ?>
        </article>
    </section>

    <!-- ── Voorstellingen ── -->
    <section class="voorstellingen-sectie">
        <h2>Voorstellingen</h2>

        <?php if (empty($perDatum)): ?>
            <p class="geen-films">Geen voorstellingen beschikbaar.</p>

        <?php else: ?>
            <?php foreach ($perDatum as $datum => $tijden): ?>
                <article class="voorstelling-dag">
                    <h3 class="voorstelling-dag__datum">
                        <?= date('l d F Y', strtotime($datum)) ?>
                    </h3>
                    <ul class="voorstelling-tijden">
                        <?php foreach ($tijden as $v): ?>
                            <li>
                                <a href="checkout.php?voorstelling=<?= $v['id'] ?>"
                                   class="voorstelling-tijd-kaart">
                                    <strong><?= substr($v['starttijd'], 0, 5) ?></strong>
                                    <span><?= htmlspecialchars($v['zaal_naam']) ?> · <?= htmlspecialchars($v['zaal_type']) ?></span>
                                    <span><?= htmlspecialchars($v['bioscoop_naam']) ?>, <?= htmlspecialchars($v['stad']) ?></span>
                                    <span class="voorstelling-prijs">€<?= number_format($v['prijs'], 2, ',', '.') ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'footer.php'; ?>