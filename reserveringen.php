<?php


require_once 'backend/config/db.php';
require_once 'backend/classes/Reservering.php';

session_start();

// Niet ingelogd? Doorsturen naar login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// ── Annulering verwerken ──────────────────────────────
$melding     = '';
$meldingType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annuleer_id'])) {
    $annuleerID = (int) $_POST['annuleer_id'];

    if (Reservering::annuleer($annuleerID, $userId)) {
        $melding     = 'Reservering succesvol geannuleerd.';
        $meldingType = 'succes';
    } else {
        $melding     = 'Annulering mislukt. Mogelijk is de reservering al betaald of geannuleerd.';
        $meldingType = 'fout';
    }
}

// ── Reserveringen ophalen ─────────────────────────────
$reserveringen = Reservering::vanGebruiker($userId);

require_once 'header.php';
?>

<main class="pagina-wrapper">

    <header class="pagina-header">
        <h1>Mijn reserveringen</h1>
        <p class="pagina-subtitel">Overzicht van al jouw geboekte films</p>
    </header>

    <?php if ($melding): ?>
        <p class="melding melding--<?= $meldingType ?>">
            <?= htmlspecialchars($melding) ?>
        </p>
    <?php endif; ?>

    <?php if (empty($reserveringen)): ?>
        <section class="leeg-staat">
            <p class="leeg-staat__icoon"></p>
            <h2>Nog geen reserveringen</h2>
            <p>Je hebt nog geen films gereserveerd.</p>
            <a href="films.php" class="btn-primary">Bekijk films</a>
        </section>

    <?php else: ?>
        <ul class="reserveringen-lijst">
            <?php foreach ($reserveringen as $res): ?>

                <?php
                    $stoelen     = Reservering::getStoelen($res['id']);
                    $stoelLabels = array_map(fn($s) => $s['rij'] . $s['nummer'], $stoelen);
                ?>

                <li class="reservering-kaart reservering-kaart--<?= htmlspecialchars($res['status']) ?>">

                    <figure class="reservering-kaart__poster">
                        <?php if ($res['poster']): ?>
                            <img src="<?= htmlspecialchars($res['poster']) ?>"
                                 alt="<?= htmlspecialchars($res['titel']) ?>">
                        <?php else: ?>
                            <span class="poster-placeholder">POSTER</span>
                        <?php endif; ?>
                    </figure>

                    <article class="reservering-kaart__info">
                        <header>
                            <h2><?= htmlspecialchars($res['titel']) ?></h2>
                            <span class="reservering-kaart__status status--<?= htmlspecialchars($res['status']) ?>">
                                <?= match($res['status']) {
                                    'in_behandeling' => 'In behandeling',
                                    'betaald'        => 'Betaald',
                                    'geannuleerd'    => 'Geannuleerd',
                                    default          => htmlspecialchars($res['status'])
                                } ?>
                            </span>
                        </header>

                        <ul class="reservering-kaart__details">
                            <li>
                                <span> Datum</span>
                                <strong><?= date('d M Y', strtotime($res['datum'])) ?></strong>
                            </li>
                            <li>
                                <span> Tijd</span>
                                <strong><?= substr($res['starttijd'], 0, 5) ?></strong>
                            </li>
                            <li>
                                <span> Locatie</span>
                                <strong><?= htmlspecialchars($res['bioscoop']) ?> — <?= htmlspecialchars($res['zaal']) ?></strong>
                            </li>
                            <li>
                                <span>💺 Stoelen</span>
                                <strong><?= !empty($stoelLabels) ? htmlspecialchars(implode(', ', $stoelLabels)) : '—' ?></strong>
                            </li>
                            <li>
                                <span> Totaal</span>
                                <strong>€<?= number_format($res['totaal'], 2, ',', '.') ?></strong>
                            </li>
                            <li>
                                <span> Code</span>
                                <strong class="reservering-code"><?= htmlspecialchars($res['code']) ?></strong>
                            </li>
                        </ul>

                        <?php if ($res['status'] === 'in_behandeling'): ?>
                            <form method="POST" class="annuleer-form"
                                  onsubmit="return confirm('Weet je zeker dat je deze reservering wilt annuleren?')">
                                <input type="hidden" name="annuleer_id" value="<?= $res['id'] ?>">
                                <button type="submit" class="btn-annuleer">Annuleren</button>
                            </form>
                        <?php endif; ?>
                    </article>

                </li>

            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</main>

<?php require_once 'footer.php'; ?>