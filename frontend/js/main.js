// frontend/js/main.js
// Globale JS die op elke pagina geladen wordt

document.addEventListener('DOMContentLoaded', function () {
    // Actieve nav link markeren
    const huidig = window.location.pathname.split('/').pop();
    document.querySelectorAll('.navbar ul a').forEach(link => {
        if (link.getAttribute('href') === huidig) {
            link.classList.add('actief');
        }
    });
});