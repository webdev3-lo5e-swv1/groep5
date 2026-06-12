const geselecteerdeStoelen = new Set();

document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.stoel-checkbox');
    const bestelBtn  = document.getElementById('bestel-btn');
    if (!bestelBtn) return;

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
            bestelBtn.disabled = geselecteerdeStoelen.size === 0;
        });
    });
});

function updateSamenvatting() {
    const totaal = geselecteerdeStoelen.size * (typeof prijsPerStoel !== 'undefined' ? prijsPerStoel : 0);
    const labels = [];

    geselecteerdeStoelen.forEach(function (stoelId) {
        const checkbox = document.querySelector(`.stoel-checkbox[value="${stoelId}"]`);
        if (checkbox) {
            const rijEl    = checkbox.closest('.stoel-rij')?.querySelector('.rij-label');
            const nummerEl = checkbox.closest('.stoel')?.querySelector('span');
            if (rijEl && nummerEl) labels.push(rijEl.textContent.trim() + nummerEl.textContent.trim());
        }
    });

    const stoelEl  = document.getElementById('samenvatting-stoelen');
    const totaalEl = document.getElementById('samenvatting-totaal');
    if (stoelEl)  stoelEl.textContent  = labels.length > 0 ? labels.join(', ') : '—';
    if (totaalEl) totaalEl.textContent = '€' + totaal.toFixed(2).replace('.', ',');
}