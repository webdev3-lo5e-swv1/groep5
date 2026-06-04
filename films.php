<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alle Films – MBO Cinemas</title>

    
    <link rel="stylesheet" href="style/style.css" />
</head>
<body>

<?php include 'includes/header.php'; ?>

 

<!-- ─── BREADCRUMB ─── -->
<div class="films-breadcrumb">
    <a href="index.php">MBO</a>
    <span>›</span>
    <a href="films.php">Films overzicht</a>
    <span>›</span>
    <span>Alle</span>
    <span>›</span>
    <span>Klant</span>
</div>

<!-- ─── HOOFD CONTENT ─── -->
<div class="films-page">
    <div class="films-content">

        <!-- ─── SIDEBAR ─── -->
        <aside class="films-sidebar">

            <!-- Genre filter -->
            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Genre</h3>
                <ul class="films-filter-groep__lijst">
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Actie" />
                            Actie
                        </label>
                        <span class="films-filter-item__count">20</span>
                    </li>
                    <li class="films-filter-item films-filter-item--actief">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Thriller" checked />
                            Thriller
                        </label>
                        <span class="films-filter-item__count">18</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Drama" />
                            Drama
                        </label>
                        <span class="films-filter-item__count">16</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Komedie" />
                            Komedie
                        </label>
                        <span class="films-filter-item__count">14</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Animatie" />
                            Animatie
                        </label>
                        <span class="films-filter-item__count">12</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="genres" data-waarde="Horror" />
                            Horror
                        </label>
                        <span class="films-filter-item__count">10</span>
                    </li>
                </ul>
            </div>

            <!-- Locatie filter -->
            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Locatie</h3>
                <ul class="films-filter-groep__lijst">
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="locaties" data-waarde="Amsterdam" />
                            Amsterdam
                        </label>
                        <span class="films-filter-item__count">14</span>
                    </li>
                    <li class="films-filter-item films-filter-item--actief">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="locaties" data-waarde="Utrecht" checked />
                            Utrecht
                        </label>
                        <span class="films-filter-item__count">12</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="locaties" data-waarde="Rotterdam" />
                            Rotterdam
                        </label>
                        <span class="films-filter-item__count">18</span>
                    </li>
                    <li class="films-filter-item">
                        <label class="films-filter-item__links">
                            <input type="checkbox" class="films-filter-checkbox" data-type="locaties" data-waarde="Eindhoven" />
                            Eindhoven
                        </label>
                        <span class="films-filter-item__count">8</span>
                    </li>
                </ul>
            </div>

            <!-- Datum filter -->
            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Datum</h3>
                <div class="films-datum-grid">
                    <button class="films-datum-btn" data-datum="ma">Ma</button>
                    <button class="films-datum-btn" data-datum="di">Di</button>
                    <button class="films-datum-btn films-datum-btn--actief" data-datum="wo">Wo</button>
                    <button class="films-datum-btn" data-datum="do">Do</button>
                    <button class="films-datum-btn" data-datum="vr">Vr</button>
                    <button class="films-datum-btn" data-datum="za">Za</button>
                    <button class="films-datum-btn" data-datum="zo">Zo</button>
                    <button class="films-datum-btn" data-datum="all">+10</button>
                </div>
            </div>

            <!-- Tijd filter -->
            <div class="films-filter-groep">
                <h3 class="films-filter-groep__titel">Tijd</h3>
                <div class="films-tijd-rij">
                    <button class="films-tijd-btn" data-tijd="ochtend">Ochtend</button>
                    <button class="films-tijd-btn films-tijd-btn--actief" data-tijd="middag">Middag</button>
                    <button class="films-tijd-btn films-tijd-btn--actief" data-tijd="avond">Avond</button>
                </div>
            </div>

        </aside>

        <!-- ─── HOOFD GEDEELTE ─── -->
        <main class="films-main">

            <!-- Titel + sort -->
            <div class="films-titel-rij">
                <h1 class="films-titel">Alle films</h1>
                <div class="films-sort-rij">
                    <span class="films-sort-label">Sort ·</span>
                    <button class="films-sort-btn films-sort-btn--actief" data-sort="populair">Populair</button>
                    <button class="films-sort-btn" data-sort="nieuwste">Nieuwste</button>
                    <button class="films-sort-btn" data-sort="az">A–Z</button>
                </div>
            </div>

            <!-- Subtitel -->
            <p class="films-subtitel">
                Utrecht · deze week ·
                <span id="films-teller">...</span>
            </p>

            <!-- Actieve filter tags -->
            <div class="films-actieve-filters" id="films-actieve-tags">
                <span class="films-tag">
                    Thriller <span class="films-tag__x" onclick="films_verwijderFilter('genres','Thriller')">×</span>
                </span>
                <span class="films-tag">
                    Utrecht <span class="films-tag__x" onclick="films_verwijderFilter('locaties','Utrecht')">×</span>
                </span>
                <span class="films-tag">
                    Deze week <span class="films-tag__x">×</span>
                </span>
                <button class="films-tag films-tag--filter-btn">+ Filter</button>
                <span class="films-wis-alle" onclick="films_wisAlleFilters()">− wis alle</span>
            </div>

            <!-- Film grid -->
            <div class="films-grid" id="films-grid">
                <!-- Films worden hier ingeladen via JavaScript -->
            </div>

            <!-- Meer laden -->
            <button class="films-meer-btn" id="films-meer-btn" style="display:none;">
                Laad meer films ↓
            </button>

        </main>
    </div>
</div>

<!-- ─── FOOTER ─── -->
<?php include 'includes/footer.php'; ?>

<!-- Films pagina JavaScript -->
<script src="js/films.js"></script>

</body>
</html>