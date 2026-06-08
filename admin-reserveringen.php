<?php
// admin-reserveringen.php — Reserveringen beheren
// Gebruikt: PDO, admin middleware, XSS

require_once 'backend/config/db.php';
require_once 'backend/middleware/admin_check.php';

$melding     = '';
$meldingType = '';
$db = Database::getInstance()->getConnection();

// ── Status aanpassen ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $stmt = $db->prepare("UPDATE reserveringen SET status = ? WHERE id = ?");
    $ok   = $stmt->execute([
        htmlspecialchars($_POST['status']),
        (int) $_POST['update_id']
    ]);
    $melding     = $ok ? 'Status bijgewerkt.' : 'Bijwerken mislukt.';
    $meldingType = $ok ? 'succes' : 'fout';
}

// Filter op status
$filterStatus = $_GET['status'] ?? 'alle';
$sql = "
    SELECT r.*, f.titel, v.datum, v.starttijd,
           CONCAT(u.voornaam, ' ', u.achternaam) as gebruiker,
           u.email
    FROM reserveringen r
    JOIN voorstellingen v ON r.voorstelling_id = v.id
    JOIN films f          ON v.film_id = f.id
    LEFT JOIN users u     ON r.user_id = u.id
";

if ($filterStatus !== 'alle') {
    $sql .= " WHERE r.status = " . $db->quote($filterStatus);
}
$sql .= " ORDER BY r.aangemaakt_op DESC";

$reserveringen = $db->query($sql)->fetchAll();

require_once 'header.php';
?>

<main class="admin-wrapper">

    <header class="pagina-header">
        <h1>⚙ Reserveringen beheren</h1>
        <nav class="admin-nav">
            <a href="admin-films.php" class="btn-ghost small">Films</a>
            <a href="admin-planning.php" class="btn-ghost small">Planning</a>
            <a href="admin-reserveringen.php" class="btn-primary small">Reserveringen</a>
        </nav>
    </header>

    <?php if ($melding): ?>
        <p class="melding melding--<?= $meldingType ?>"><?= htmlspecialchars($melding) ?></p>
    <?php endif; ?>

    <!-- Filter tabs -->
    <nav class="admin-filter-tabs">
        <?php foreach (['alle', 'in_behandeling', 'betaald', 'geannuleerd'] as $status): ?>
            <a href="?status=<?= $status ?>"
               class="admin-filter-tab <?= $filterStatus === $status ? 'admin-filter-tab--actief' : '' ?>">
                <?= match($status) {
                    'alle'           => 'Alle',
                    'in_behandeling' => 'In behandeling',
                    'betaald'        => 'Betaald',
                    'geannuleerd'    => 'Geannuleerd'
                } ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Reserveringen tabel -->
    <section class="admin-tabel-sectie">
        <h2>Reserveringen (<?= count($reserveringen) ?>)</h2>

        <?php if (empty($reserveringen)): ?>
            <p class="geen-films">Geen reserveringen gevonden.</p>
        <?php else: ?>
        <table class="admin-tabel">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Gebruiker</th>
                    <th>Film</th>
                    <th>Datum</th>
                    <th>Totaal</th>
                    <th>Status</th>
                    <th>Aangemaakt</th>
                    <th>Actie</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reserveringen as $res): ?>
                    <tr>
                        <td><span class="reservering-code"><?= htmlspecialchars($res['code']) ?></span></td>
                        <td>
                            <?= htmlspecialchars($res['gebruiker'] ?? 'Gast') ?>
                            <?php if ($res['email']): ?>
                                <small style="display:block;color:var(--tekst-grijs)"><?= htmlspecialchars($res['email']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($res['titel']) ?></td>
                        <td><?= date('d M Y', strtotime($res['datum'])) ?> <?= substr($res['starttijd'], 0, 5) ?></td>
                        <td>€<?= number_format($res['totaal'], 2, ',', '.') ?></td>
                        <td>
                            <span class="reservering-kaart__status status--<?= htmlspecialchars($res['status']) ?>">
                                <?= match($res['status']) {
                                    'in_behandeling' => 'In behandeling',
                                    'betaald'        => 'Betaald',
                                    'geannuleerd'    => 'Geannuleerd',
                                    default          => htmlspecialchars($res['status'])
                                } ?>
                            </span>
                        </td>
                        <td><?= date('d M Y H:i', strtotime($res['aangemaakt_op'])) ?></td>
                        <td>
                            <form method="POST" style="display:flex;gap:4px;align-items:center;">
                                <input type="hidden" name="update_id" value="<?= $res['id'] ?>">
                                <select name="status" class="admin-select">
                                    <option value="in_behandeling" <?= $res['status'] === 'in_behandeling' ? 'selected' : '' ?>>In behandeling</option>
                                    <option value="betaald"        <?= $res['status'] === 'betaald'        ? 'selected' : '' ?>>Betaald</option>
                                    <option value="geannuleerd"    <?= $res['status'] === 'geannuleerd'    ? 'selected' : '' ?>>Geannuleerd</option>
                                </select>
                                <button type="submit" class="btn-primary small">Sla op</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </section>

</main>

<?php require_once 'footer.php'; ?>