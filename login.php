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

        <div id="login-form-inloggen" class="login-form">
            <h3>Inloggen</h3>
            <label for="in-email">E-mail</label>
            <input type="email" id="in-email" name="email" placeholder="jouw@email.nl" />
            <label for="in-wachtwoord">Wachtwoord</label>
            <input type="password" id="in-wachtwoord" name="wachtwoord" placeholder="••••••••" />
            <button class="login-form__btn" type="submit">Inloggen</button>
            <p class="login-form__switch">Geen account? <a href="#" onclick="loginSwitchTab('registreren'); return false;">Registreren</a></p>
        </div>

        <div id="login-form-registreren" class="login-form" style="display: none;">
            <h3>Account aanmaken</h3>
            <label for="reg-naam">Naam</label>
            <input type="text" id="reg-naam" name="naam" placeholder="Jouw naam" />
            <label for="reg-email">E-mail</label>
            <input type="email" id="reg-email" name="email" placeholder="jouw@email.nl" />
            <label for="reg-wachtwoord">Wachtwoord</label>
            <input type="password" id="reg-wachtwoord" name="wachtwoord" placeholder="••••••••" />
            <button class="login-form__btn" type="submit">Registreren</button>
            <p class="login-form__switch">Al een account? <a href="#" onclick="loginSwitchTab('inloggen'); return false;">Inloggen</a></p>
        </div>

    </div>
</div>

<script>
function loginSwitchTab(tab) {
    const isInloggen = tab === 'inloggen';

    document.getElementById('login-form-inloggen').style.display    = isInloggen ? 'flex' : 'none';
    document.getElementById('login-form-registreren').style.display  = isInloggen ? 'none' : 'flex';

    document.getElementById('login-tab-inloggen').className    = 'login-tab' + (isInloggen ? ' login-tab--actief' : '');
    document.getElementById('login-tab-registreren').className = 'login-tab' + (!isInloggen ? ' login-tab--actief' : '');
}
</script>

<?php include 'includes/footer.php'; ?>