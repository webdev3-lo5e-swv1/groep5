// frontend/js/api.js
// ─────────────────────────────────────────────────────
// Dit is de centrale API module — alle fetch calls gaan
// via dit bestand. Andere JS bestanden importeren dit.
//
// Wat je hier ziet:
//  - async/await  (Les 3: asynchroon JS)
//  - try/catch    (Les 3: errors opvangen)
//  - fetch        (Les 3+4: data ophalen en sturen)
//  - cache        (Uitstekend criterium: niet opnieuw ophalen)
// ─────────────────────────────────────────────────────

// Cache object — sla al opgehaalde data op in geheugen
// Zo hoef je niet elke keer opnieuw te fetchen (Uitstekend)
const cache = {};

// ── Stap 1: Basis fetch functie ───────────────────────
// async = deze functie werkt asynchroon (wacht op de browser)
async function apiGet(url) {

    // Check: zit het al in de cache?
    if (cache[url]) {
        console.log(`[cache] ${url}`);
        return cache[url]; // Geef direct terug, geen nieuwe fetch
    }

    // try: probeer de fetch
    try {
        // await = wacht tot de browser klaar is met het request
        const response = await fetch(url);

        // Check status: 200-299 = succes, anders fout
        if (!response.ok) {
            throw new Error(`HTTP fout: ${response.status}`);
        }

        // Zet de JSON string om naar een JS object
        const data = await response.json();

        // Sla op in cache zodat we het niet opnieuw ophalen
        cache[url] = data;

        return data;

    // catch: vang de error op als er iets fout gaat
    } catch (error) {
        console.error(`[api] GET mislukt voor ${url}:`, error);
        return null;
    }
}

// ── Stap 2: POST functie (data versturen) ─────────────
async function apiPost(url, data) {
    try {
        const response = await fetch(url, {
            method: 'POST',                          // POST = nieuwe data sturen
            headers: {
                'Content-Type': 'application/json'  // Vertel PHP: dit is JSON
            },
            body: JSON.stringify(data)              // Zet JS object om naar JSON string
        });

        if (!response.ok) {
            throw new Error(`HTTP fout: ${response.status}`);
        }

        const result = await response.json();

        // Cache legen voor deze resource (data is veranderd)
        Object.keys(cache).forEach(key => {
            if (key.includes(url.split('?')[0])) delete cache[key];
        });

        return result;

    } catch (error) {
        console.error(`[api] POST mislukt voor ${url}:`, error);
        return null;
    }
}

// ── Exporteer zodat andere bestanden het kunnen gebruiken ──
// (ES Modules — Les 2: modules met import/export)
export { apiGet, apiPost };