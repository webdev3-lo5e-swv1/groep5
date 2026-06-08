<?php
// admin-films.php — Films beheren (CRUD)
// Gebruikt: PDO, OOP (Film class), admin middleware, XSS

require_once 'backend/config/db.php';
require_once 'backend/classes/Film.php';
require_once 'backend/middleware/admin_check.php';

$melding     = '';
$meldingType = '';

// ── DELETE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int) $_POST['delete_id'];
    if (Film::delete($id)) {
        $melding = 'Film verwijderd.';
        $meldingType = 'succes';
    } else {
        $melding = 'Verwijderen mislukt.';
        $meldingType = 'fout';
    }
}

// ── CREATE / UPDATE ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opslaan'])) {
    $data = [
        'titel'       => $_POST['titel'] ?? '',
        'beschrijving'=> $_POST['beschrijving'] ?? '',
        'genre'       => $_POST['genre'] ?? '',
        'duur'        => $_POST['duur'] ?? 0,
        'leeftijd'    => $_POST['leeftijd'] ?? '',
        'taal'        => $_POST['taal'] ?? '',
        'regisseur'   => $_POST['regisseur'] ?? '',
        'cast'        => $_POST['cast'] ?? '',
        'poster'      => $_POST['poster'] ?? null,
        'trailer_url' => $_POST['trailer_url'] ?? null,
    ];

    $editId = (int) ($_POST['edit_id'] ?? 0);

    if ($editId > 0) {
        $ok = Film::update($editId, $data);
        $melding = $ok ? 'Film bijgewerkt.' : 'Bijwerken mislukt.';
    } else {
        $ok = Film::create($data);
        $melding = $ok ? 'Film toegevoegd.' : 'Toevoegen mislukt.';
    }
    $meldingType = $ok ? 'succes' : 'fout';
}

// Bewerk-modus: film ophalen
$bewerkFilm = null;
if (isset($_GET['bewerk'])) {
    $bewerkFilm = Film::findById((int) $_GET['bewerk']);
}

// Alle films ophalen
$films = Film::alle();

require_once 'header.php';
?>

<main class="admin-wrapper">

    <header class="pagina-header">
        <h1>⚙ Films beheren</h1>
        <nav class="admin-nav">
            <a href="admin-films.php" class="btn-primary small">Films</a>
            <a href="admin-planning.php" class="btn-ghost small">Planning</a>
            <a href="admin-reserveringen.php" class="btn-ghost small">Reserveringen</a>
        </nav>
    </header>

    <?php if ($melding): ?>
        <p class="melding melding--<?= $meldingType ?>"><?= htmlspecialchars($melding) ?></p>
    <?php endif; ?>

    <!-- Formulier toevoegen/bewerken -->
    <section class="admin-form-sectie">
        <h2><?= $bewerkFilm ? 'Film bewerken' : 'Film toevoegen' ?></h2>

        <form method="POST" class="admin-form">
            <?php if ($bewerkFilm): ?>
                <input type="hidden" name="edit_id" value="<?= $bewerkFilm->getId() ?>">
            <?php endif; ?>

            <div class="admin-form-grid">
                <div class="form-veld">
                    <label>Titel *</label>
                    <input type="text" name="titel" required
                           value="<?= htmlspecialchars($bewerkFilm?->getTitel() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Genre</label>
                    <input type="text" name="genre" placeholder="Actie, Drama"
                           value="<?= htmlspecialchars($bewerkFilm?->getGenre() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Duur (minuten) *</label>
                    <input type="number" name="duur" required min="1"
                           value="<?= $bewerkFilm?->getDuur() ?? '' ?>">
                </div>
                <div class="form-veld">
                    <label>Leeftijd</label>
                    <input type="text" name="leeftijd" placeholder="12"
                           value="<?= htmlspecialchars($bewerkFilm?->getLeeftijd() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Taal</label>
                    <input type="text" name="taal" placeholder="Engels"
                           value="<?= htmlspecialchars($bewerkFilm?->getTaal() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Regisseur</label>
                    <input type="text" name="regisseur"
                           value="<?= htmlspecialchars($bewerkFilm?->getRegisseur() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Cast</label>
                    <input type="text" name="cast" placeholder="Acteur 1, Acteur 2"
                           value="<?= htmlspecialchars($bewerkFilm?->getCast() ?? '') ?>">
                </div>
                <div class="form-veld">
                    <label>Poster URL</label>
                    <input type="url" name="poster"
                           value="<?= htmlspecialchars($bewerkFilm?->getPoster() ?? '') ?>">
                </div>
                <div class="form-veld" style="grid-column: 1/-1;">
                    <label>Beschrijving</label>
                    <textarea name="beschrijving" rows="3"><?= htmlspecialchars($bewerkFilm?->getBeschrijving() ?? '') ?></textarea>
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:12px;">
                <button type="submit" name="opslaan" class="btn-primary">
                    <?= $bewerkFilm ? 'Opslaan' : 'Film toevoegen' ?>
                </button>
                <?php if ($bewerkFilm): ?>
                    <a href="admin-films.php" class="btn-ghost">Annuleren</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <!-- Films tabel -->
    <section class="admin-tabel-sectie">
        <h2>Alle films (<?= count($films) ?>)</h2>

        <table class="admin-tabel">
            <thead>
                <tr>
                    <th>Poster</th>
                    <th>Titel</th>
                    <th>Genre</th>
                    <th>Duur</th>
                    <th>Leeftijd</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($films as $film): ?>
                    <tr>
                        <td>
                            <?php if ($film->getPoster()): ?>
                                <img src="<?= htmlspecialchars($film->getPoster()) ?>"
                                     alt="" style="width:40px;height:60px;object-fit:cover;border-radius:4px;">
                            <?php else: ?>
                                <span class="poster-placeholder" style="width:40px;height:60px;font-size:9px;">POSTER</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($film->getTitel()) ?></td>
                        <td><?= htmlspecialchars($film->getGenre()) ?></td>
                        <td><?= $film->getDuurFormatted() ?></td>
                        <td><?= htmlspecialchars($film->getLeeftijd()) ?>+</td>
                        <td class="admin-acties">
                            <a href="admin-films.php?bewerk=<?= $film->getId() ?>" class="btn-ghost small">Bewerk</a>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Film verwijderen?')">
                                <input type="hidden" name="delete_id" value="<?= $film->getId() ?>">
                                <button type="submit" class="btn-annuleer">Verwijder</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</main>

<?php require_once 'footer.php'; ?>