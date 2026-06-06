// frontend/js/pages/stoelen.js
// Stoelenplan interactie — selecteren, totaal berekenen, samenvatting updaten

// ── State ─────────────────────────────────────────────
const geselecteerdeStoelen = new Set(); // Set = unieke waarden, geen duplicaten

// ── Init ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.stoel-checkbox');
    const bestelBtn  = document.getElementById('bestel-btn');

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            const stoelId = this.value;
            const label   = this.closest('.stoel');

            if (this.checked) {
                geselecteerdeStoelen.add(stoelId);
                label.classList.add('stoel--gekozen');
            } else {
                geselecteerdeStoelen.delete(stoelId);
                label.classList.remove('stoel--gekozen');
            }

            updateSamenvatting();

            // Knop activeren zodra er minimaal 1 stoel gekozen is
            bestelBtn.disabled = geselecteerdeStoelen.size === 0;
        });
    });
});

// ── Samenvatting updaten ──────────────────────────────
function updateSamenvatting() {
    const aantalStoelen = geselecteerdeStoelen.size;
    const totaal        = aantalStoelen * prijsPerStoel; // prijsPerStoel komt uit checkout.php

    // Stoellabels ophalen (rij + nummer) uit de labels
    const labels = [];
    geselecteerdeStoelen.forEach(function (stoelId) {
        const checkbox = document.querySelector(`.stoel-checkbox[value="${stoelId}"]`);
        if (checkbox) {
            // Rij staat in het rij-label, nummer in de span van de stoel
            const rijEl    = checkbox.closest('.stoel-rij')?.querySelector('.rij-label');
            const nummerEl = checkbox.closest('.stoel')?.querySelector('span');
            if (rijEl && nummerEl) {
                labels.push(rijEl.textContent + nummerEl.textContent);
            }
        }
    });

    // Samenvatting updaten
    document.getElementById('samenvatting-stoelen').textContent =
        labels.length > 0 ? labels.join(', ') : '—';

    document.getElementById('samenvatting-totaal').textContent =
        '€' + totaal.toFixed(2).replace('.', ',');
}