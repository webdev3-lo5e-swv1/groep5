
class AdminTabel {
    #tabelId;
    #rijen;
    #zoekterm;

    constructor(tabelId) {
        this.#tabelId  = tabelId;
        this.#rijen    = [];
        this.#zoekterm = '';
        this.#init();
    }

    #init() {
        const tabel = document.getElementById(this.#tabelId);
        if (!tabel) return;
        this.#rijen = Array.from(tabel.querySelectorAll('tbody tr'));
    }

    zoek(term) {
        this.#zoekterm = term.toLowerCase();
        this.#rijen.forEach(rij => {
            const tekst = rij.textContent.toLowerCase();
            rij.style.display = tekst.includes(this.#zoekterm) ? '' : 'none';
        });
    }

    getAantalZichtbaar() {
        return this.#rijen.filter(r => r.style.display !== 'none').length;
    }
}

// ── Init ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // Initialiseer tabel zoekfunctie als die aanwezig is
    const zoekInput = document.getElementById('admin-zoek');
    if (zoekInput) {
        const tabel = new AdminTabel('admin-tabel-body');
        zoekInput.addEventListener('input', function () {
            tabel.zoek(this.value);
        });
    }

    // Bevestig dialoog voor verwijder knoppen
    document.querySelectorAll('.verwijder-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Weet je zeker dat je dit wilt verwijderen?')) {
                e.preventDefault();
            }
        });
    });
});

export { AdminTabel };