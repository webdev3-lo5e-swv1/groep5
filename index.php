<?php
require_once 'backend/config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$db = Database::getInstance()->getConnection();

$films_nu = $db->query("
    SELECT DISTINCT f.id, f.titel, f.genre, f.duur, f.leeftijd, f.poster,
           MIN(v.starttijd) as eerste_tijd
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum = CURDATE()
    GROUP BY f.id
    ORDER BY eerste_tijd ASC
    LIMIT 4
")->fetchAll();

$films_binnenkort = $db->query("
    SELECT f.id, f.titel, f.poster, MIN(v.datum) as eerste_datum
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum > CURDATE()
    GROUP BY f.id
    ORDER BY eerste_datum ASC
    LIMIT 4
")->fetchAll();

$featured = $db->query("
    SELECT f.*, MIN(v.datum) as eerste_datum, MIN(v.starttijd) as eerste_tijd, MIN(v.id) as eerste_voorstelling
    FROM films f
    JOIN voorstellingen v ON f.id = v.film_id
    WHERE v.datum >= CURDATE()
    GROUP BY f.id
    ORDER BY eerste_datum ASC, eerste_tijd ASC
    LIMIT 1
")->fetch();

require_once 'header.php';
?>

<?php if ($featured): ?>
<section class="hero">
    <figure class="hero-video">
        <?php if ($featured['poster']): ?>
            <img src="<?= htmlspecialchars($featured['poster']) ?>"
                 alt="<?= htmlspecialchars($featured['titel']) ?>"
                 style="width:100%;height:100%;object-fit:cover;border-radius:6px;">
        <?php endif; ?>
        <figcaption>FEATURED FILM — NU IN DE ZAAL</figcaption>
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
            <?php if ($featured['trailer_url']): ?>
                <a href="<?= htmlspecialchars($featured['trailer_url']) ?>" target="_blank" class="btn-ghost">Trailer</a>
            <?php else: ?>
                <a href="#" class="btn-ghost">Trailer</a>
            <?php endif; ?>
        </p>
        <label class="hero-doorgaan" id="hero-doorgaan" style="display:none;">
            <input type="checkbox"> Doorgaan met reservering
            <small id="hero-doorgaan-info">—</small>
        </label>
    </article>
</section>
<?php endif; ?>

<section class="zoek-sectie">
    <small>Zoek op titel, genre of locatie</small>
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
        <a href="films.php" class="link-alle">ALLE FILMS · ALLE VESTIGINGEN</a>
    </header>
    <?php if (empty($films_nu)): ?>
        <p class="geen-films">Geen films beschikbaar.</p>
    <?php else: ?>
    <ul class="films-grid">
        <?php foreach ($films_nu as $film): ?>
        <li class="film-kaart">
            <figure>
                <?php if ($film['poster']): ?>
                    <img src="<?= htmlspecialchars($film['poster']) ?>" alt="<?= htmlspecialchars($film['titel']) ?>" loading="lazy">
                <?php else: ?>
                    <span class="poster-placeholder">POSTER</span>
                <?php endif; ?>
            </figure>
            <h3><?= htmlspecialchars($film['titel']) ?></h3>
            <p class="film-meta"><?= htmlspecialchars($film['genre']) ?> · <?= $film['duur'] ?> MIN</p>
            <p class="film-acties">
                <span class="film-tijd"><?= $film['eerste_tijd'] ? substr($film['eerste_tijd'], 0, 5) : '--:--' ?></span>
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
                    <img src="<?= htmlspecialchars($film['poster']) ?>" alt="<?= htmlspecialchars($film['titel']) ?>" loading="lazy">
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

<script type="module" src="frontend/js/pages/home.js"></script>
<?php require_once 'footer.php'; ?>