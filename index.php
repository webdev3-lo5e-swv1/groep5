<?php
require_once 'backend/config/db.php';

$db = Database::getInstance()->getConnection();

$stmt = $db->query("
    SELECT DISTINCT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum >= CURDATE()
    ORDER BY v.datum ASC
    LIMIT 4
");
$films_nu = $stmt->fetchAll();

$stmt2 = $db->query("
    SELECT f.id, f.titel, f.poster, MIN(v.datum) as eerste_datum
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum > CURDATE()
    GROUP BY f.id
    ORDER BY eerste_datum ASC
    LIMIT 4
");
$films_binnenkort = $stmt2->fetchAll();

$stmt3 = $db->query("
    SELECT f.*, MIN(v.datum) as eerste_datum
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum >= CURDATE()
    GROUP BY f.id
    LIMIT 1
");
$featured = $stmt3->fetch();

require_once 'header.php';
?>

<?php if ($featured): ?>
<section class="hero">
    <figure class="hero-video">
        <figcaption>FEATURED FILM — TRAILER #1</figcaption>
    </figure>
    <article class="hero-info">
        <small>UITGELICHT</small>
        <h1>"<?= htmlspecialchars($featured['titel']) ?>"</h1>
        <p class="tags">
            <?php foreach (explode(',', $featured['genre']) as $g): ?>
                <span class="tag"><?= trim(htmlspecialchars($g)) ?></span>
            <?php endforeach; ?>
            <span class="tag"><?= htmlspecialchars($featured['leeftijd']) ?>+</span>
        </p>
        <p class="hero-buttons">
            <a href="film.php?id=<?= $featured['id'] ?>" class="btn-primary">Kaartjes →</a>
            <a href="#" class="btn-ghost">Trailer</a>
        </p>
        <label class="hero-doorgaan">
            <input type="checkbox"> Doorgaan met reservering
            <small>LocalStorage — 2 kaartjes · Dune Part</small>
        </label>
    </article>
</section>
<?php endif; ?>

<section class="zoek-sectie">
    <small>3 filters · basisresultaten &nbsp;|&nbsp; genre · locatie · datum</small>
    <form class="zoek-balk" method="GET" action="films.php">
        <input type="text" name="zoek" placeholder="Titel of genre...">
        <input type="text" name="locatie" placeholder="Locatie ▾">
        <input type="text" name="datum" placeholder="Datum ▾">
        <input type="text" name="soort" placeholder="Soort ▾">
        <button type="submit" class="btn-primary">Filter</button>
    </form>
</section>

<section class="films-sectie">
    <header>
        <h2>Nu in de zaal</h2>
        <a href="films.php" class="link-alle">13 FILMS · ALLE VESTIGINGEN</a>
    </header>
    <?php if (empty($films_nu)): ?>
        <p class="geen-films">Geen films beschikbaar.</p>
    <?php else: ?>
    <ul class="films-grid">
        <?php foreach ($films_nu as $film): ?>
        <li class="film-kaart">
            <figure>
                <?php if ($film['poster']): ?>
                    <img src="<?= htmlspecialchars($film['poster']) ?>" alt="<?= htmlspecialchars($film['titel']) ?>">
                <?php else: ?>
                    <span class="poster-placeholder">POSTER</span>
                <?php endif; ?>
            </figure>
            <h3><?= htmlspecialchars($film['titel']) ?></h3>
            <p class="film-meta"><?= htmlspecialchars($film['genre']) ?> · <?= $film['duur'] ?> MIN</p>
            <p class="film-acties">
                <span class="film-tijd">vanaf --:--</span>
                <a href="film.php?id=<?= $film['id'] ?>" class="btn-primary small">Reserveer</a>
            </p>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</section>

<section class="films-sectie binnenkort">
    <header>
        <h2>Binnenkort</h2>
        <a href="films.php" class="link-alle">+ ZIE ALLES</a>
    </header>
    <ul class="films-grid binnenkort-grid">
        <?php foreach ($films_binnenkort as $film): ?>
        <li class="film-kaart small">
            <figure>
                <?php if ($film['poster']): ?>
                    <img src="<?= htmlspecialchars($film['poster']) ?>" alt="<?= htmlspecialchars($film['titel']) ?>">
                <?php else: ?>
                    <span class="poster-placeholder">POSTER</span>
                <?php endif; ?>
            </figure>
            <h3><?= htmlspecialchars($film['titel']) ?></h3>
            <p class="film-datum"><?= date('d M Y', strtotime($film['eerste_datum'])) ?></p>
        </li>
        <?php endforeach; ?>
    </ul>
</section>

<?php require_once 'footer.php'; ?>