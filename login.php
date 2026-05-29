<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="css/login.css" />

<div class="login-wrapper">

    <div class="login-foto">
        <img src="images/jouw-foto.jpg" alt="Sportschoenen" />
        <div class="login-foto__tekst">
            <h2>Welkom bij SportStep</h2>
            <p>De beste schoenen voor jou</p>
        </div>
    </div>

    <div class="login-panel">

        <div class="login-tabs">
            <button id="login-tab-inloggen" class="login-tab login-tab--actief" onclick="loginSwitchTab('inloggen')">Inloggen</button>
            <button id="login-tab-registreren" class="login-tab" onclick="loginSwitchTab('registreren')">Registreren</button>
        </div>

        <form id="login-form-inloggen" class="login-form" method="POST">
            <h3>Inloggen</h3>
            <?php if (isset($error_login)): ?>
                <div class="login-error"><?php echo htmlspecialchars($error_login); ?></div>
            <?php endif; ?>
            <label for="in-email">E-mail</label>
            <input type="email" id="in-email" name="email" placeholder="jouw@email.nl" required />
            <label for="in-wachtwoord">Wachtwoord</label>
            <input type="password" id="in-wachtwoord" name="wachtwoord" placeholder="••••••••" required />
            <button class="login-form__btn" type="submit" name="login" value="1">Inloggen</button>
            <p class="login-form__switch">Geen account? <a href="#" onclick="loginSwitchTab('registreren'); return false;">Registreren</a></p>
        </form>

        <form id="login-form-registreren" class="login-form" style="display: none;" method="POST">
            <h3>Account aanmaken</h3>
            <?php if (isset($error_register)): ?>
                <div class="login-error"><?php echo htmlspecialchars($error_register); ?></div>
            <?php endif; ?>
            <?php if (isset($success_register)): ?>
                <div class="login-success"><?php echo htmlspecialchars($success_register); ?></div>
            <?php endif; ?>
            <label for="reg-naam">Naam</label>
            <input type="text" id="reg-naam" name="naam" placeholder="Jouw naam" required />
            <label for="reg-email">E-mail</label>
            <input type="email" id="reg-email" name="email" placeholder="jouw@email.nl" required />
            <label for="reg-wachtwoord">Wachtwoord</label>
            <input type="password" id="reg-wachtwoord" name="wachtwoord" placeholder="••••••••" required />
            <label for="reg-wachtwoord-confirm">Wachtwoord herhalen</label>
            <input type="password" id="reg-wachtwoord-confirm" name="wachtwoord_confirm" placeholder="••••••••" required />
            <button class="login-form__btn" type="submit" name="register" value="1">Registreren</button>
            <p class="login-form__switch">Al een account? <a href="#" onclick="loginSwitchTab('inloggen'); return false;">Inloggen</a></p>
        </form>

    </div>
</div>


<?php
require_once 'backend/config/db.php';
session_start();

// LOGIN VERWERKING
if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['wachtwoord'];

    if (empty($email) || empty($password)) {
        $error_login = "Vul alle velden in!";
    } else {
        $stmt = $conn->prepare("SELECT id, wachtwoord FROM gebruikers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['wachtwoord'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                header("Location: dashboard.php");
                exit;
            } else {
                $error_login = "Onjuist wachtwoord!";
            }
        } else {
            $error_login = "E-mailadres niet gevonden!";
        }
        $stmt->close();
    }
}

// REGISTRATIE VERWERKING
if (isset($_POST['register'])) {
    $naam = htmlspecialchars($_POST['naam']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['wachtwoord'];
    $password_confirm = $_POST['wachtwoord_confirm'];

    if (empty($naam) || empty($email) || empty($password) || empty($password_confirm)) {
        $error_register = "Vul alle velden in!";
    } elseif ($password !== $password_confirm) {
        $error_register = "Wachtwoorden komen niet overeen!";
    } else {
        // Check of email al bestaat
        $check_stmt = $conn->prepare("SELECT id FROM gebruikers WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_register = "Dit e-mailadres bestaat al!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO gebruikers (naam, email, wachtwoord) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $naam, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_register = "Account aangemaakt! Je kunt nu inloggen.";
            } else {
                $error_register = "Er is een fout opgetreden. Probeer later opnieuw.";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<script>
function loginSwitchTab(tab) {
    const isInloggen = tab === 'inloggen';

    document.getElementById('login-form-inloggen').style.display    = isInloggen ? 'flex' : 'none';
    document.getElementById('login-form-registreren').style.display  = isInloggen ? 'none' : 'flex';

    document.getElementById('login-tab-inloggen').className    = 'login-tab' + (isInloggen ? ' login-tab--actief' : '');
    document.getElementById('login-tab-registreren').className = 'login-tab' + (!isInloggen ? ' login-tab--actief' : '');
}

// Toon registratie tab automatisch na succesvolle registratie
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('registered') === 'true') {
        loginSwitchTab('inloggen');
    }
});
</script>

<?php include 'includes/footer.php'; ?>