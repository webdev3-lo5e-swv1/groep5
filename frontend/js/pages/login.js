// ── Tab wisselen ─────────────────────────────────
function switchTab(tab) {
    const isInloggen = tab === 'inloggen';

    // Formulieren tonen/verbergen
    document.getElementById('form-inloggen').style.display     = isInloggen ? 'flex' : 'none';
    document.getElementById('form-registreren').style.display  = isInloggen ? 'none'  : 'flex';

    // Tab knoppen stylen
    document.getElementById('tab-btn-inloggen').classList.toggle('login-tab--actief', isInloggen);
    document.getElementById('tab-btn-registreren').classList.toggle('login-tab--actief', !isInloggen);
}

// ── Wachtwoord tonen / verbergen ─────────────────
function toggleWachtwoord(inputId, knop) {
    const input = document.getElementById(inputId);
    const verborgen = input.type === 'password';

    input.type    = verborgen ? 'text' : 'password';
    knop.textContent = verborgen ? '🙈' : '👁';
    knop.title    = verborgen ? 'Verberg wachtwoord' : 'Toon wachtwoord';
}

// ── Wachtwoord sterkte meter ──────────────────────
function berekenSterkte(wachtwoord) {
    let score = 0;
    if (wachtwoord.length >= 8)  score++;
    if (wachtwoord.length >= 12) score++;
    if (/[A-Z]/.test(wachtwoord)) score++;
    if (/[0-9]/.test(wachtwoord)) score++;
    if (/[^A-Za-z0-9]/.test(wachtwoord)) score++;
    return score; // 0–5
}

function updateSterkteUI(score) {
    const vulling = document.getElementById('sterkte-vulling');
    const label   = document.getElementById('sterkte-label');
    const wrapper = document.getElementById('sterkte-wrapper');

    const stappen = [
        { label: '',          kleur: 'transparent', breedte: '0%'   },
        { label: 'Zwak',      kleur: '#ef4444',      breedte: '20%'  },
        { label: 'Matig',     kleur: '#f97316',      breedte: '40%'  },
        { label: 'Redelijk',  kleur: '#eab308',      breedte: '60%'  },
        { label: 'Goed',      kleur: '#22c55e',      breedte: '80%'  },
        { label: 'Sterk',     kleur: '#16a34a',      breedte: '100%' },
    ];

    const stap = stappen[score] || stappen[0];
    vulling.style.width      = stap.breedte;
    vulling.style.background = stap.kleur;
    label.textContent        = stap.label;
    wrapper.style.display    = score > 0 ? 'flex' : 'none';
}

// ── Init na laden DOM ─────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Juiste tab tonen op basis van URL param (?tab=registreren)
    const params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'registreren') {
        switchTab('registreren');
    }

    // Wachtwoord sterkte koppelen aan het registreer-veld
    const wachtwoordVeld = document.getElementById('reg-wachtwoord');
    if (wachtwoordVeld) {
        wachtwoordVeld.addEventListener('input', function () {
            const score = berekenSterkte(this.value);
            updateSterkteUI(score);
        });
    }

    // Bevestig-veld: toon rode rand als wachtwoorden niet overeenkomen
    const bevestigVeld = document.getElementById('reg-bevestig');
    if (bevestigVeld && wachtwoordVeld) {
        bevestigVeld.addEventListener('input', function () {
            const overeenkomen = this.value === wachtwoordVeld.value;
            this.style.borderColor = this.value.length > 0
                ? (overeenkomen ? 'var(--oranje)' : '#ef4444')
                : '';
        });
    }
});