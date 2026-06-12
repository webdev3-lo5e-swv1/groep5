<?php


require_once 'backend/config/db.php';
require_once 'backend/classes/Reservering.php';
require_once 'backend/middleware/auth_check.php';

// Reserveringen ophalen
$reserveringen = Reservering::vanGebruiker($_SESSION['user_id']);

// Splits in aankomend en verleden
$aankomend = array_filter($reserveringen, fn($r) => $r['datum'] >= date('Y-m-d') && $r['status'] !== 'geannuleerd');
$verleden  = array_filter($reserveringen, fn($r) => $r['datum'] < date('Y-m-d') || $r['status'] === 'geannuleerd');

require_once 'header.php';
?>

<main class="dashboard-wrapper">

    <!-- Welkom header -->
    <header class="dashboard-header">
        <div>
            <h1>Welkom, <?= htmlspecialchars($_SESSION['voornaam']) ?>!</h1>
            <p class="pagina-subtitel"><?= htmlspecialchars($_SESSION['email']) ?></p>
        </div>
        <?php if ($_SESSION['rol'] === 'medewerker'): ?>
            <a href="admin-films.php" class="btn-primary">⚙ Admin panel</a>
        <?php endif; ?>
    </header>

    <!-- Statistieken -->
    <ul class="dashboard-stats">
        <li class="stat-kaart">
            <span class="stat-kaart__getal"><?= count($reserveringen) ?></span>
            <span class="stat-kaart__label">Totaal reserveringen</span>
        </li>
        <li class="stat-kaart">
            <span class="stat-kaart__getal"><?= count($aankomend) ?></span>
            <span class="stat-kaart__label">Aankomende films</span>
        </li>
        <li class="stat-kaart">
            <span class="stat-kaart__getal">
                €<?= number_format(array_sum(array_column($reserveringen, 'totaal')), 2, ',', '.') ?>
            </span>
            <span class="stat-kaart__label">Totaal uitgegeven</span>
        </li>
    </ul>

    <!-- Aankomende reserveringen -->
    <section class="dashboard-sectie">
        <header class="sectie-header">
            <h2>Aankomende films</h2>
            <a href="reserveringen.php" class="link-alle">Alle reserveringen →</a>
        </header>

        <?php if (empty($aankomend)): ?>
            <div class="leeg-staat">
                <p class="leeg-staat__icoon"></p>
                <p>Geen aankomende reserveringen.</p>
                <a href="films.php" class="btn-primary">Reserveer een film</a>
            </div>
        <?php else: ?>
            <ul class="dashboard-reserveringen">
                <?php foreach (array_slice($aankomend, 0, 3) as $res): ?>
                    <li class="dashboard-res-kaart">
                        <figure class="dashboard-res-kaart__poster">
                            <?php if ($res['poster']): ?>
                                <img src="<?= htmlspecialchars($res['poster']) ?>"
                                     alt="<?= htmlspecialchars($res['titel']) ?>">
                            <?php else: ?>
                                <span class="poster-placeholder">POSTER</span>
                            <?php endif; ?>
                        </figure>
                        <article>
                            <h3><?= htmlspecialchars($res['titel']) ?></h3>
                            <p> <?= date('d M Y', strtotime($res['datum'])) ?> om <?= substr($res['starttijd'], 0, 5) ?></p>
                            <p> <?= htmlspecialchars($res['bioscoop']) ?></p>
                            <p><span class="reservering-code"><?= htmlspecialchars($res['code']) ?></span></p>
                        </article>
                        <span class="reservering-kaart__status status--<?= htmlspecialchars($res['status']) ?>">
                            <?= match($res['status']) {
                                'in_behandeling' => 'In behandeling',
                                'betaald'        => 'Betaald',
                                default          => htmlspecialchars($res['status'])
                            } ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Snelle links -->
    <section class="dashboard-sectie">
        <h2>Snel navigeren</h2>
        <ul class="dashboard-links">
            <li><a href="films.php" class="dashboard-link-kaart">🎬<span>Films bekijken</span></a></li>
            <li><a href="reserveringen.php" class="dashboard-link-kaart">🎫<span>Mijn reserveringen</span></a></li>
            <li><a href="login.php" class="dashboard-link-kaart" onclick="<?php session_destroy(); ?>">🚪<span>Uitloggen</span></a></li>
        </ul>
    </section>

</main>

<?php require_once 'footer.php'; ?>