// frontend/js/pages/home.js
// Homepage JS — importeert storage module, toont lopende reservering
// Rubric: ES Modules (import), JS OOP (FilmKaart class)

import { haalReserveringOp, verwijderReservering, heeftLopendeReservering } from '../storage.js';

// ── JS OOP: FilmKaart class ───────────────────────────
// Rubric: constructor, properties, methodes, encapsulation
class FilmKaart {
    #id;           // private field (encapsulation)
    #titel;
    #poster;
    #element;

    constructor(id, titel, poster = null) {
        this.#id     = id;
        this.#titel  = titel;
        this.#poster = poster;
        this.#element = null;
    }

    // Getter
    getId()    { return this.#id; }
    getTitel() { return this.#titel; }

    // Maak DOM element aan
    render() {
        const li = document.createElement('li');
        li.className = 'film-kaart';
        li.innerHTML = `
            <figure>
                ${this.#poster
                    ? `<img src="${this.#poster}" alt="${this.#titel}" loading="lazy">`
                    : `<span class="poster-placeholder">POSTER</span>`
                }
            </figure>
            <h3>${this.#titel}</h3>
        `;
        li.addEventListener('click', () => this.naarDetail());
        this.#element = li;
        return li;
    }

    // Navigeer naar detailpagina
    naarDetail() {
        window.location.href = `film.php?id=${this.#id}`;
    }

    // Markeer als featured
    markeerFeatured() {
        if (this.#element) {
            this.#element.classList.add('film-kaart--featured');
        }
    }
}

// ── Lopende reservering tonen ─────────────────────────
function toonLopendeReservering() {
    const res = haalReserveringOp();
    if (!res) return;

    const label = document.querySelector('.hero-doorgaan small');
    const checkbox = document.querySelector('.hero-doorgaan input');

    if (label) {
        label.textContent = `${res.aantalStoelen} kaartje(s) · ${res.filmTitel}`;
    }

    if (checkbox) {
        checkbox.addEventListener('change', function () {
            if (this.checked && res.voorstellingId) {
                window.location.href = `checkout.php?voorstelling=${res.voorstellingId}`;
            }
        });
    }
}

// ── Init ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    toonLopendeReservering();
});

export { FilmKaart };