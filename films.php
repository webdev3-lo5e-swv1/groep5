<?php
// films.php — Films overzichtspagina

require_once 'backend/config/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'header.php';
?>

<div class="films-breadcrumb">
    <a href="index.php">MBO</a> <span>›</span>
    <a href="films.php">Films overzicht</a> <span>›</span>
    <span>Alle films</span>
</div>

<div class="films-page">
    <div class="films-content">

        <aside class="films-sidebar">

            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Genre</h3>
                <ul class="films-filter-groep__lijst">
                    <?php
                    $genres = ['Actie', 'Thriller', 'Drama', 'Komedie', 'Animatie', 'Horror', 'Sci-Fi', 'Avontuur', 'Misdaad', 'Fantasy'];
                    foreach ($genres as $g):
                    ?>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="<?= htmlspecialchars($g) ?>">
                            <?= htmlspecialchars($g) ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Locatie</h3>
                <ul class="films-filter-groep__lijst">
                    <?php foreach (['Amsterdam', 'Rotterdam', 'Den Haag'] as $stad): ?>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="locaties" data-waarde="<?= htmlspecialchars($stad) ?>">
                            <?= htmlspecialchars($stad) ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Datum</h3>
                <div class="films-datum-grid">
                    <?php
                    $dagen = ['Ma','Di','Wo','Do','Vr','Za','Zo'];
                    for ($i = 0; $i < 7; $i++):
                        $datum = date('Y-m-d', strtotime("+$i days"));
                        $label = $i === 0 ? 'Vandaag' : ($i === 1 ? 'Morgen' : $dagen[date('N', strtotime("+$i days")) - 1]);
                    ?>
                        <button class="films-datum-btn" data-datum="<?= $datum ?>"><?= $label ?></button>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Tijd</h3>
                <div class="films-tijd-rij">
                    <button class="films-tijd-btn" data-tijd="ochtend">Ochtend</button>
                    <button class="films-tijd-btn" data-tijd="middag">Middag</button>
                    <button class="films-tijd-btn" data-tijd="avond">Avond</button>
                </div>
            </div>

            <button class="btn-ghost" onclick="films_wisAlleFilters()" style="width:100%;margin-top:12px;">Wis filters</button>

        </aside>

        <main class="films-main">

            <div class="films-titel-rij">
                <h1 class="films-titel">Alle films</h1>
                <div class="films-sort-rij">
                    <span class="films-sort-label">Sort ·</span>
                    <button class="films-sort-btn films-sort-btn--actief" data-sort="datum">Datum</button>
                    <button class="films-sort-btn" data-sort="titel">A–Z</button>
                    <button class="films-sort-btn" data-sort="rating">Rating</button>
                </div>
            </div>

            <p class="films-subtitel">
                <span id="films-teller">...</span>
            </p>

            <div class="films-actieve-filters" id="films-actieve-tags">
                <button class="films-tag films-tag--filter-btn">+ Filter</button>
                <span class="films-wis-alle" onclick="films_wisAlleFilters()">− wis alle</span>
            </div>

            <p id="films-lader" style="display:none;color:var(--tekst-grijs);padding:24px;">Films laden...</p>

            <div class="films-grid" id="films-grid"></div>

            <div style="text-align:center;margin-top:32px;">
                <button class="films-meer-btn" id="films-meer-btn" style="display:none;">Laad meer films ↓</button>
            </div>

        </main>
    </div>
</div>

<script src="frontend/js/pages/films.js"></script>
<?php require_once 'footer.php'; ?>