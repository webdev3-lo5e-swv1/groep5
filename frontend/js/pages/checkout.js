// frontend/js/pages/checkout.js
// Slaat lopende reservering op in localStorage via storage module
// Rubric: ES Modules (import/export)

import { slaReserveringOp, verwijderReservering } from '../storage.js';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');
    if (!form) return;

    // Sla reservering op zodra gebruiker stoelen selecteert
    document.querySelectorAll('.stoel-checkbox').forEach(cb => {
        cb.addEventListener('change', slaLopendeReserveringOp);
    });

    // Bij submit: verwijder de lopende reservering uit storage
    form.addEventListener('submit', function () {
        verwijderReservering();
    });
});

function slaLopendeReserveringOp() {
    const geselecteerd = document.querySelectorAll('.stoel-checkbox:checked');
    const filmTitel    = document.querySelector('.checkout-samenvatting h2')?.textContent ?? '';
    const params       = new URLSearchParams(window.location.search);
    const voorstellingId = params.get('voorstelling');

    slaReserveringOp({
        voorstellingId: voorstellingId,
        filmTitel:      filmTitel,
        aantalStoelen:  geselecteerd.length,
        tijdstip:       Date.now()
    });
}