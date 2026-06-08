<?php
// admin-planning.php — Voorstellingen beheren (CRUD)
// Gebruikt: PDO, OOP, admin middleware, XSS

require_once 'backend/config/db.php';
require_once 'backend/classes/Film.php';
require_once 'backend/classes/Voorstelling.php';
require_once 'backend/middleware/admin_check.php';

$melding     = '';
$meldingType = '';
$db = Database::getInstance()->getConnection();

// ── DELETE voorstelling ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM voorstellingen WHERE id = ?");
        $ok   = $stmt->execute([(int) $_POST['delete_id']]);
        $melding     = $ok ? 'Voorstelling verwijderd.' : 'Verwijderen mislukt.';
        $meldingType = $ok ? 'succes' : 'fout';
    } catch (PDOException $e) {
        $melding     = 'Kan niet verwijderen: er zijn reserveringen aan gekoppeld.';
        $meldingType = 'fout';
    }
}

// ── CREATE voorstelling ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opslaan'])) {
    try {
        $stmt = $db->prepare("
            INSERT INTO voorstellingen (film_id, zaal_id, datum, starttijd, eindtijd, prijs)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            (int)   $_POST['film_id'],
            (int)   $_POST['zaal_id'],
            htmlspecialchars($_POST['datum']),
            htmlspecialchars($_POST['starttijd']),
            htmlspecialchars($_POST['eindtijd']),
            (float) $_POST['prijs']
        ]);
        $melding     = $ok ? 'Voorstelling toegevoegd.' : 'Toevoegen mislukt.';
        $meldingType = $ok ? 'succes' : 'fout';
    } catch (PDOException $e) {
        $melding     = 'Er is een fout opgetreden.';
        $meldingType = 'fout';
    }
}

// Alle films en zalen ophalen voor dropdown
$films = Film::alle();
$zalen = $db->query("
    SELECT z.id, z.naam, z.type, b.naam as bioscoop, b.stad
    FROM zalen z JOIN bioscopen b ON z.bioscoop_id = b.id
    ORDER BY b.stad, z.naam
")->fetchAll();

// Komende voorstellingen ophalen
$voorstellingen = $db->query("
    SELECT v.*, f.titel, z.naam as zaal, b.naam as bioscoop, b.stad
    FROM voorstellingen v
    JOIN films f     ON v.film_id = f.id
    JOIN zalen z     ON v.zaal_id = z.id
    JOIN bioscopen b ON z.bioscoop_id = b.id
    WHERE v.datum >= CURDATE()
    ORDER BY v.datum ASC, v.starttijd ASC
")->fetchAll();

require_once 'header.php';
?>

<main class="admin-wrapper">

    <header class="pagina-header">
        <h1>⚙ Planning beheren</h1>
        <nav class="admin-nav">
            <a href="admin-films.php" class="btn-ghost small">Films</a>
            <a href="admin-planning.php" class="btn-primary small">Planning</a>
            <a href="admin-reserveringen.php" class="btn-ghost small">Reserveringen</a>
        </nav>
    </header>

    <?php if ($melding): ?>
        <p class="melding melding--<?= $meldingType ?>"><?= htmlspecialchars($melding) ?></p>
    <?php endif; ?>

    <!-- Formulier toevoegen -->
    <section class="admin-form-sectie">
        <h2>Voorstelling toevoegen</h2>

        <form method="POST" class="admin-form">
            <div class="admin-form-grid">
                <div class="form-veld">
                    <label>Film *</label>
                    <select name="film_id" required>
                        <option value="">— Kies film —</option>
                        <?php foreach ($films as $film): ?>
                            <option value="<?= $film->getId() ?>">
                                <?= htmlspecialchars($film->getTitel()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-veld">
                    <label>Zaal *</label>
                    <select name="zaal_id" required>
                        <option value="">— Kies zaal —</option>
                        <?php foreach ($zalen as $zaal): ?>
                            <option value="<?= $zaal['id'] ?>">
                                <?= htmlspecialchars($zaal['bioscoop']) ?> — <?= htmlspecialchars($zaal['naam']) ?> (<?= $zaal['type'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-veld">
                    <label>Datum *</label>
                    <input type="date" name="datum" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-veld">
                    <label>Starttijd *</label>
                    <input type="time" name="starttijd" required>
                </div>
                <div class="form-veld">
                    <label>Eindtijd *</label>
                    <input type="time" name="eindtijd" required>
                </div>
                <div class="form-veld">
                    <label>Prijs (€) *</label>
                    <input type="number" name="prijs" step="0.50" min="0" required placeholder="12.50">
                </div>
            </div>
            <button type="submit" name="opslaan" class="btn-primary" style="margin-top:12px;">
                Voorstelling toevoegen
            </button>
        </form>
    </section>

    <!-- Voorstellingen tabel -->
    <section class="admin-tabel-sectie">
        <h2>Komende voorstellingen (<?= count($voorstellingen) ?>)</h2>

        <table class="admin-tabel">
            <thead>
                <tr>
                    <th>Film</th>
                    <th>Bioscoop</th>
                    <th>Zaal</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Prijs</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voorstellingen as $v): ?>
                    <tr>
                        <td><?= htmlspecialchars($v['titel']) ?></td>
                        <td><?= htmlspecialchars($v['bioscoop']) ?>, <?= htmlspecialchars($v['stad']) ?></td>
                        <td><?= htmlspecialchars($v['zaal']) ?></td>
                        <td><?= date('d M Y', strtotime($v['datum'])) ?></td>
                        <td><?= substr($v['starttijd'], 0, 5) ?> – <?= substr($v['eindtijd'], 0, 5) ?></td>
                        <td>€<?= number_format($v['prijs'], 2, ',', '.') ?></td>
                        <td>
                            <form method="POST" style="display:inline"
                                  onsubmit="return confirm('Voorstelling verwijderen?')">
                                <input type="hidden" name="delete_id" value="<?= $v['id'] ?>">
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