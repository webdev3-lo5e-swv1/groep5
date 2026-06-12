// frontend/js/pages/films.js

const _cache = {};
const filmsState = {
    pagina: 1, perPagina: 9, sort: 'datum', isLaden: false,
    filters: { genres: [], locaties: [], datums: [], tijden: [] }
};

async function films_laadFilms(reset = false) {
    if (filmsState.isLaden) return;
    filmsState.isLaden = true;
    if (reset) { filmsState.pagina = 1; document.getElementById('films-grid').innerHTML = ''; }
    films_toonSkeletons();

    const params = new URLSearchParams({ pagina: filmsState.pagina, per_pagina: filmsState.perPagina, sort: filmsState.sort });
    if (filmsState.filters.genres.length)   params.append('genre',   filmsState.filters.genres.join(','));
    if (filmsState.filters.locaties.length) params.append('locatie', filmsState.filters.locaties.join(','));
    if (filmsState.filters.datums.length)   params.append('datum',   filmsState.filters.datums.join(','));
    if (filmsState.filters.tijden.length)   params.append('tijd',    filmsState.filters.tijden.join(','));

    const url = `backend/api/films.php?${params}`;
    try {
        let data;
        if (_cache[url]) {
            data = _cache[url];
        } else {
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            data = await res.json();
            if (!data.success) throw new Error(data.message || 'Fout');
            _cache[url] = data;
        }
        films_verwijderSkeletons();
        films_renderFilms(data.films);
        films_updateTeller(data.totaal);
        const btn = document.getElementById('films-meer-btn');
        if (btn) btn.style.display = (filmsState.pagina * filmsState.perPagina) >= data.totaal ? 'none' : 'block';
    } catch (err) {
        console.error('Films laden mislukt:', err);
        films_verwijderSkeletons();
        document.getElementById('films-grid').innerHTML = `<div class="films-leeg"><p>⚠ ${err.message}</p></div>`;
    }
    filmsState.isLaden = false;
}

function films_renderFilms(lijst) {
    const grid = document.getElementById('films-grid');
    if (!lijst || lijst.length === 0) { grid.innerHTML = `<div class="films-leeg"><div class="films-leeg__icoon">🎬</div><p>Geen films gevonden.</p></div>`; return; }
    lijst.forEach(film => grid.appendChild(films_maakKaart(film)));
}

function films_maakKaart(film) {
    const li = document.createElement('li');
    li.className = 'film-kaart';
    const heeftPoster = film.poster && film.poster.startsWith('http');
    const eersteTijd  = film.tijden && film.tijden.length > 0 ? film.tijden[0].tijd : '--:--';
    li.innerHTML = `
        <figure>${heeftPoster ? `<img src="${esc(film.poster)}" alt="${esc(film.titel)}" loading="lazy">` : `<span class="poster-placeholder">POSTER</span>`}</figure>
        <h3>${esc(film.titel)}</h3>
        <p class="film-meta">${esc(film.genre)} · ${film.duur} MIN</p>
        <p class="film-acties"><span class="film-tijd">${eersteTijd}</span><a href="film.php?id=${film.id}" class="btn-primary small">Reserveer</a></p>`;
    li.addEventListener('click', e => { if (!e.target.classList.contains('btn-primary')) window.location.href = `film.php?id=${film.id}`; });
    return li;
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function films_toonSkeletons() {
    const grid = document.getElementById('films-grid');
    if (!grid) return;
    for (let i = 0; i < 8; i++) {
        const sk = document.createElement('li');
        sk.className = 'films-skeleton films-skeleton-item';
        sk.innerHTML = `<div class="films-skeleton__poster"></div><div class="films-skeleton__lijn"></div><div class="films-skeleton__lijn films-skeleton__lijn--kort"></div>`;
        grid.appendChild(sk);
    }
}
function films_verwijderSkeletons() { document.querySelectorAll('.films-skeleton-item').forEach(el => el.remove()); }

function films_initFilters() {
    document.querySelectorAll('.films-filter-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const { type, waarde } = cb.dataset, lijst = filmsState.filters[type];
            cb.checked ? (!lijst.includes(waarde) && lijst.push(waarde)) : lijst.splice(lijst.indexOf(waarde), 1);
            films_updateActieveTags(); films_laadFilms(true);
        });
    });
    document.querySelectorAll('.films-datum-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('films-datum-btn--actief');
            const w = btn.dataset.datum, i = filmsState.filters.datums.indexOf(w);
            i > -1 ? filmsState.filters.datums.splice(i,1) : filmsState.filters.datums.push(w);
            films_laadFilms(true);
        });
    });
    document.querySelectorAll('.films-tijd-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.classList.toggle('films-tijd-btn--actief');
            const w = btn.dataset.tijd, i = filmsState.filters.tijden.indexOf(w);
            i > -1 ? filmsState.filters.tijden.splice(i,1) : filmsState.filters.tijden.push(w);
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
    document.getElementById('films-meer-btn')?.addEventListener('click', () => { filmsState.pagina++; films_laadFilms(); });
}

function films_updateActieveTags() {
    const container = document.getElementById('films-actieve-tags');
    if (!container) return;
    const vaste = container.querySelectorAll('.films-tag--filter-btn, .films-wis-alle');
    container.querySelectorAll('.films-tag:not(.films-tag--filter-btn)').forEach(el => el.remove());
    [...filmsState.filters.genres.map(v=>({type:'genres',waarde:v})),...filmsState.filters.locaties.map(v=>({type:'locaties',waarde:v}))].forEach(({type,waarde}) => {
        const tag = document.createElement('span'); tag.className='films-tag';
        tag.innerHTML=`${waarde} <span class="films-tag__x">×</span>`;
        tag.addEventListener('click', () => films_verwijderFilter(type, waarde));
        container.insertBefore(tag, vaste[0]);
    });
}
function films_verwijderFilter(type, waarde) {
    const lijst=filmsState.filters[type], idx=lijst.indexOf(waarde);
    if(idx>-1) lijst.splice(idx,1);
    const cb=document.querySelector(`.films-filter-checkbox[data-type="${type}"][data-waarde="${waarde}"]`);
    if(cb) cb.checked=false;
    films_updateActieveTags(); films_laadFilms(true);
}
function films_wisAlleFilters() {
    filmsState.filters={genres:[],locaties:[],datums:[],tijden:[]};
    document.querySelectorAll('.films-filter-checkbox').forEach(cb=>cb.checked=false);
    document.querySelectorAll('.films-datum-btn,.films-tijd-btn').forEach(btn=>btn.classList.remove('films-datum-btn--actief','films-tijd-btn--actief'));
    films_updateActieveTags(); films_laadFilms(true);
}
function films_updateTeller(totaal) { const el=document.getElementById('films-teller'); if(el) el.textContent=`${totaal} film${totaal!==1?'s':''} gevonden`; }

document.addEventListener('DOMContentLoaded', () => { films_initFilters(); films_laadFilms(); });
