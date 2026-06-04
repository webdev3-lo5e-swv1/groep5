<?php


require_once 'backend/config/db.php';
require_once 'backend/classes/User.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$activeTab        = 'inloggen';
$error_login      = '';
$error_register   = '';
$success_register = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //INLOGGEN 
    if (isset($_POST['login'])) {
        $activeTab = 'inloggen';
        $email     = htmlspecialchars(trim($_POST['email'] ?? ''));
        $password  = $_POST['wachtwoord'] ?? '';

        if (empty($email) || empty($password)) {
            $error_login = 'Vul alle velden in.';
        } else {
            $user = User::findByEmail($email);
            if ($user && password_verify($password, $user->getWachtwoord())) {
                $_SESSION['user_id']  = $user->getId();
                $_SESSION['voornaam'] = $user->getVoornaam();
                $_SESSION['email']    = $user->getEmail();
                $_SESSION['rol']      = $user->getRol();
                header('Location: dashboard.php');
                exit;
            } else {
                $error_login = 'Ongeldig e-mailadres of wachtwoord.';
            }
        }

    //REGISTREREN
    } elseif (isset($_POST['register'])) {
        $activeTab        = 'registreren';
        $voornaam         = htmlspecialchars(trim($_POST['voornaam'] ?? ''));
        $achternaam       = htmlspecialchars(trim($_POST['achternaam'] ?? ''));
        $email            = htmlspecialchars(trim($_POST['email'] ?? ''));
        $password         = $_POST['wachtwoord'] ?? '';
        $password_confirm = $_POST['wachtwoord_confirm'] ?? '';

        if (empty($voornaam) || empty($achternaam) || empty($email) || empty($password) || empty($password_confirm)) {
            $error_register = 'Vul alle velden in.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_register = 'Voer een geldig e-mailadres in.';
        } elseif (strlen($password) < 8) {
            $error_register = 'Wachtwoord moet minimaal 8 tekens zijn.';
        } elseif ($password !== $password_confirm) {
            $error_register = 'Wachtwoorden komen niet overeen.';
        } elseif (User::findByEmail($email)) {
            $error_register = 'Dit e-mailadres is al in gebruik.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            if (User::create($voornaam, $achternaam, $email, $hashed)) {
                $success_register = 'Account aangemaakt! Je kunt nu inloggen.';
                $activeTab = 'inloggen';
            } else {
                $error_register = 'Er is iets misgegaan. Probeer opnieuw.';
            }
        }
    }
}

require_once 'header.php';
?>

<link rel="stylesheet" href="style/login.css">

<main class="login-wrapper">

    <aside class="login-visual">
        <span class="logo"><span class="logo-dot"></span> MBO Cinemas</span>
        <h2>De beste films,<br>altijd voor jou.</h2>
        <p>Reserveer je stoelen in seconden.</p>
    </aside>

    <section class="login-panel">

        <nav class="login-tabs">
            <button id="tab-btn-inloggen"
                    class="login-tab <?= $activeTab === 'inloggen' ? 'login-tab--actief' : '' ?>"
                    onclick="switchTab('inloggen')" type="button">Inloggen</button>
            <button id="tab-btn-registreren"
                    class="login-tab <?= $activeTab === 'registreren' ? 'login-tab--actief' : '' ?>"
                    onclick="switchTab('registreren')" type="button">Registreren</button>
        </nav>

      
        <form id="form-inloggen" class="login-form" method="POST"
              style="display:<?= $activeTab === 'inloggen' ? 'flex' : 'none' ?>;">

            <h3>Welkom terug</h3>

            <?php if ($error_login): ?>
                <p class="melding melding--fout"><?= htmlspecialchars($error_login) ?></p>
            <?php endif; ?>
            <?php if ($success_register): ?>
                <p class="melding melding--succes"><?= htmlspecialchars($success_register) ?></p>
            <?php endif; ?>

            <label for="in-email">E-mailadres</label>
            <input type="email" id="in-email" name="email" placeholder="jouw@email.nl" required>

            <label for="in-wachtwoord">Wachtwoord</label>
            <span class="input-wachtwoord">
                <input type="password" id="in-wachtwoord" name="wachtwoord" placeholder="••••••••" required>
                <button type="button" class="toon-btn" onclick="toggleWachtwoord('in-wachtwoord',this)">👁</button>
            </span>

            <button type="submit" name="login" class="btn-submit">Inloggen</button>
            <p class="wissel">Geen account? <a href="#" onclick="switchTab('registreren');return false;">Registreren</a></p>
        </form>

    
        <form id="form-registreren" class="login-form" method="POST"
              style="display:<?= $activeTab === 'registreren' ? 'flex' : 'none' ?>;">

            <h3>Account aanmaken</h3>

            <?php if ($error_register): ?>
                <p class="melding melding--fout"><?= htmlspecialchars($error_register) ?></p>
            <?php endif; ?>

            <label for="reg-voornaam">Voornaam</label>
            <input type="text" id="reg-voornaam" name="voornaam" placeholder="Jan" required>

            <label for="reg-achternaam">Achternaam</label>
            <input type="text" id="reg-achternaam" name="achternaam" placeholder="de Vries" required>

            <label for="reg-email">E-mailadres</label>
            <input type="email" id="reg-email" name="email" placeholder="jouw@email.nl" required>

            <label for="reg-wachtwoord">Wachtwoord <span class="hint">(min. 8 tekens)</span></label>
            <span class="input-wachtwoord">
                <input type="password" id="reg-wachtwoord" name="wachtwoord"
                       placeholder="••••••••" required minlength="8">
                <button type="button" class="toon-btn" onclick="toggleWachtwoord('reg-wachtwoord',this)">👁</button>
            </span>

            <label for="reg-bevestig">Wachtwoord herhalen</label>
            <span class="input-wachtwoord">
                <input type="password" id="reg-bevestig" name="wachtwoord_confirm"
                       placeholder="••••••••" required minlength="8">
                <button type="button" class="toon-btn" onclick="toggleWachtwoord('reg-bevestig',this)">👁</button>
            </span>

            <span class="sterkte-wrapper" id="sterkte-wrapper" style="display:none;">
                <span class="sterkte-balk"><span class="sterkte-vulling" id="sterkte-vulling"></span></span>
                <small id="sterkte-label"></small>
            </span>

            <button type="submit" name="register" class="btn-submit">Account aanmaken</button>
            <p class="wissel">Al een account? <a href="#" onclick="switchTab('inloggen');return false;">Inloggen</a></p>
        </form>

    </section>

</main>

<script src="frontend/js/pages/login.js"></script>
<?php require_once 'footer.php'; ?>