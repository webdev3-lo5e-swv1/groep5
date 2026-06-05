// frontend/js/pages/films.js
// ─────────────────────────────────────────────────────
// Gebruikt: async/await, fetch, try/catch, cache (Les 3+4)
// ─────────────────────────────────────────────────────

// ─── STATE & CACHE ───────────────────────────────────
const _cache = {}; // Opgeslagen resultaten — niet opnieuw ophalen (Uitstekend)

const filmsState = {
    pagina:    1,
    perPagina: 9,
    sort:      'datum',
    isLaden:   false,
    filters: {
        genres:   [],
        locaties: [],
        datums:   [],
        tijden:   []
    }
};

// ─── DATA OPHALEN ────────────────────────────────────
async function films_laadFilms(reset = false) {
    if (filmsState.isLaden) return;
    filmsState.isLaden = true;

    if (reset) {
        filmsState.pagina = 1;
        document.getElementById('films-grid').innerHTML = '';
    }

    films_toonSkeletons();

    try {
        const params = new URLSearchParams({
            pagina:    filmsState.pagina,
            per_pagina: filmsState.perPagina,
            sort:      filmsState.sort,
        });

        if (filmsState.filters.genres.length)   params.append('genre',   filmsState.filters.genres.join(','));
        if (filmsState.filters.locaties.length) params.append('locatie', filmsState.filters.locaties.join(','));
        if (filmsState.filters.datums.length)   params.append('datum',   filmsState.filters.datums.join(','));
        if (filmsState.filters.tijden.length)   params.append('tijd',    filmsState.filters.tijden.join(','));

        // Correcte URL naar jouw structuur: backend/api/films.php
        const url = `backend/api/films.php?${params}`;

        // Cache check — zit het al in geheugen? Dan geen nieuwe fetch
        let data;
        if (_cache[url]) {
            console.log('[cache] gebruikt:', url);
            data = _cache[url];
        } else {
            // await = wacht tot browser klaar is met het request (Les 3)
            const response = await fetch(url);

            // Check status: 200-299 = succes, anders fout gooien
            if (!response.ok) {
                throw new Error(`HTTP fout: ${response.status}`);
            }

            // Zet JSON string om naar JS object
            data = await response.json();

            // Sla op in cache zodat we het niet opnieuw ophalen
            _cache[url] = data;
        }

        films_verwijderSkeletons();
        films_renderFilms(data.films);
        films_updateTeller(data.totaal);

        const totaalGeladen = filmsState.pagina * filmsState.perPagina;
        document.getElementById('films-meer-btn').style.display =
            totaalGeladen >= data.totaal ? 'none' : 'block';

    } catch (err) {
        // catch = vang de error op als fetch mislukt (Les 3)
        films_verwijderSkeletons();
        films_renderDemoFilms();
        console.warn('API niet bereikbaar, demo modus actief:', err);
    }

    filmsState.isLaden = false;
}

// ─── RENDER ──────────────────────────────────────────
function films_renderFilms(filmLijst) {
    const grid = document.getElementById('films-grid');

    if (!filmLijst || filmLijst.length === 0) {
        grid.innerHTML = `
            <div class="films-leeg">
                <div class="films-leeg__icoon">🎬</div>
                <p>Geen films gevonden voor deze filters.</p>
            </div>`;
        return;
    }

    filmLijst.forEach(film => {
        const kaart = films_maakKaart(film);
        grid.appendChild(kaart);
    });
}

function films_maakKaart(film) {
    const kaart = document.createElement('div');
    kaart.className = 'film-kaart';
    kaart.dataset.id = film.id;

    const poster = film.poster
        ? `<img class="film-kaart__poster" src="${film.poster}" alt="${film.titel}" loading="lazy">`
        : `<div class="film-kaart__poster--placeholder">Poster</div>`;

    const tijdenHTML = (film.tijden || []).map(t =>
        `<button class="film-kaart__tijd" onclick="films_selecteerTijd(event, ${film.id}, '${t}')">${t}</button>`
    ).join('');

    kaart.innerHTML = `
        ${poster}
        <div class="film-kaart__info">
            <div class="film-kaart__titel-rij">
                <h3 class="film-kaart__titel">${film.titel}</h3>
                <span class="film-kaart__leeftijd">${film.leeftijd || ''}</span>
            </div>
            <p class="film-kaart__meta">${(film.genre || '').toUpperCase()} · ${film.jaar || ''} · ${film.duur || ''}</p>
            <div class="film-kaart__balk">
                <div class="film-kaart__balk-vulling" style="width:${film.beoordeling || 0}%"></div>
            </div>
            <p class="film-kaart__ster">⭐ ${((film.beoordeling || 0) / 10).toFixed(1)} / 10</p>
            <div class="film-kaart__tijden">${tijdenHTML}</div>
        </div>`;

    kaart.addEventListener('click', (e) => {
        if (!e.target.classList.contains('film-kaart__tijd')) {
            films_naarDetail(film.id);
        }
    });

    return kaart;
}

// Demo kaarten als API niet beschikbaar is
function films_renderDemoFilms() {
    const demoFilms = Array.from({ length: 9 }, (_, i) => ({
        id:          i + 1,
        titel:       `Film titel ${i + 1}`,
        genre:       'Thriller',
        duur:        `2u ${Math.floor(Math.random() * 40 + 60)}m`,
        leeftijd:    ['6+', '9+', '12+', '16+'][i % 4],
        poster:      null,
        beoordeling: Math.floor(Math.random() * 60 + 30),
        tijden:      ['14:30', '17:45', '20:15', '22:50'],
    }));
    films_renderFilms(demoFilms);
    films_updateTeller(24);
}

// ─── SKELETON LOADERS ────────────────────────────────
function films_toonSkeletons() {
    const grid = document.getElementById('films-grid');
    for (let i = 0; i < 6; i++) {
        const sk = document.createElement('div');
        sk.className = 'films-skeleton films-skeleton-item';
        sk.innerHTML = `
            <div class="films-skeleton__poster"></div>
            <div class="films-skeleton__lijn"></div>
            <div class="films-skeleton__lijn films-skeleton__lijn--kort"></div>`;
        grid.appendChild(sk);
    }
}

function films_verwijderSkeletons() {
    document.querySelectorAll('.films-skeleton-item').forEach(el => el.remove());
}

// ─── FILTERS ─────────────────────────────────────────
function films_initFilters() {
    document.querySelectorAll('.films-filter-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const type   = cb.dataset.type;
            const waarde = cb.dataset.waarde;
            const lijst  = filmsState.filters[type];

            if (cb.checked) {
                if (!lijst.includes(waarde)) lijst.push(waarde);
            } else {
                const idx = lijst.indexOf(waarde);
                if (idx > -1) lijst.splice(idx, 1);
            }

            films_updateActieveTags();
            films_laadFilms(true);
        });
    });

    document.querySelectorAll('.films-datum-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('films-datum-btn--actief');
            const waarde = btn.dataset.datum;
            const idx = filmsState.filters.datums.indexOf(waarde);
            idx > -1
                ? filmsState.filters.datums.splice(idx, 1)
                : filmsState.filters.datums.push(waarde);
            films_laadFilms(true);
        });
    });

    document.querySelectorAll('.films-tijd-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('films-tijd-btn--actief');
            const waarde = btn.dataset.tijd;
            const idx = filmsState.filters.tijden.indexOf(waarde);
            idx > -1
                ? filmsState.filters.tijden.splice(idx, 1)
                : filmsState.filters.tijden.push(waarde);
            films_laadFilms(true);
        });
    });

    document.querySelectorAll('.films-sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.films-sort-btn').forEach(b => b.classList.remove('films-sort-btn--actief'));
            btn.classList.add('films-sort-btn--actief');
            filmsState.sort = btn.dataset.sort;
            films_laadFilms(true);
        });
    });

    const meerBtn = document.getElementById('films-meer-btn');
    if (meerBtn) {
        meerBtn.addEventListener('click', () => {
            filmsState.pagina++;
            films_laadFilms();
        });
    }
}

function films_updateActieveTags() {
    const container = document.getElementById('films-actieve-tags');
    if (!container) return;

    const vaste = container.querySelectorAll('.films-tag--filter-btn, .films-wis-alle');
    container.querySelectorAll('.films-tag:not(.films-tag--filter-btn)').forEach(el => el.remove());

    const alleFilters = [
        ...filmsState.filters.genres.map(v   => ({ type: 'genres',   waarde: v })),
        ...filmsState.filters.locaties.map(v  => ({ type: 'locaties', waarde: v })),
    ];

    alleFilters.forEach(({ type, waarde }) => {
        const tag = document.createElement('span');
        tag.className = 'films-tag';
        tag.innerHTML = `${waarde} <span class="films-tag__x">×</span>`;
        tag.addEventListener('click', () => films_verwijderFilter(type, waarde));
        container.insertBefore(tag, vaste[0]);
    });
}

function films_verwijderFilter(type, waarde) {
    const lijst = filmsState.filters[type];
    const idx = lijst.indexOf(waarde);
    if (idx > -1) lijst.splice(idx, 1);

    const cb = document.querySelector(`.films-filter-checkbox[data-type="${type}"][data-waarde="${waarde}"]`);
    if (cb) cb.checked = false;

    films_updateActieveTags();
    films_laadFilms(true);
}

function films_wisAlleFilters() {
    filmsState.filters = { genres: [], locaties: [], datums: [], tijden: [] };
    document.querySelectorAll('.films-filter-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.films-datum-btn, .films-tijd-btn').forEach(btn => {
        btn.classList.remove('films-datum-btn--actief', 'films-tijd-btn--actief');
    });
    films_updateActieveTags();
    films_laadFilms(true);
}

// ─── NAVIGATIE ───────────────────────────────────────
function films_naarDetail(id) {
    window.location.href = `film.php?id=${id}`;
}

function films_selecteerTijd(event, filmId, tijd) {
    event.stopPropagation();
    window.location.href = `checkout.php?film=${filmId}&tijd=${encodeURIComponent(tijd)}`;
}

function films_updateTeller(totaal) {
    const el = document.getElementById('films-teller');
    if (el) el.textContent = `${totaal} resultaten`;
}

// ─── START ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    films_initFilters();
    films_laadFilms();
});
