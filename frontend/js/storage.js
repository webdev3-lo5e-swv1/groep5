

const STORAGE_KEY = 'mbo_lopende_reservering';

// Sla lopende reservering op
function slaReserveringOp(data) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (e) {
        console.warn('[storage] opslaan mislukt:', e);
    }
}

// Haal lopende reservering op
function haalReserveringOp() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        return null;
    }
}

// Verwijder lopende reservering
function verwijderReservering() {
    localStorage.removeItem(STORAGE_KEY);
}

// Controleer of er een lopende reservering is
function heeftLopendeReservering() {
    return haalReserveringOp() !== null;
}

export { slaReserveringOp, haalReserveringOp, verwijderReservering, heeftLopendeReservering };