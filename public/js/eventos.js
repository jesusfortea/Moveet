'use strict';

const EVENTO = window.eventoData ?? null;
const MISIONES = window.misionesData || [];
const EVENTO_FIN = window.fechaFinEvento ? new Date(window.fechaFinEvento) : null;

const elLoading = document.getElementById('map-loading');
const elMissions = document.getElementById('missions-list');
const elCountdown = document.getElementById('timer-countdown');
const elLocationBtn = document.getElementById('location-btn');

let mapa = null;
let marcadorUsuario = null;
let marcadoresMisiones = [];   // markers + círculos
let rutasCapas = {};   // { [misionId]: [L.Layer, ...] }
let userCoords = null;
let recorridoTotal = 0;
let lastPosition = null;
let lastSpeedKmh = 0;
let lastAccuracyMeters = null;

/* ── Control de completado ──────────────────────────────────────── */
const misionesProximidadCompletadas = new Set();
const RADIO_PROXIMIDAD_M = 50;

/* ── CSRF ───────────────────────────────────────────────────────── */
function getCsrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.content : '';
}

/* ── Completar misión en servidor ───────────────────────────────── */
function completarMisionEnServidor(mision) {
    const payload = {
        distance_meters: Number.isFinite(recorridoTotal) ? Math.round(recorridoTotal) : 0,
        speed_kmh: Number.isFinite(lastSpeedKmh) ? Number(lastSpeedKmh.toFixed(2)) : 0,
        accuracy_meters: Number.isFinite(lastAccuracyMeters) ? Number(lastAccuracyMeters.toFixed(2)) : null,
        latitude: userCoords ? userCoords[0] : null,
        longitude: userCoords ? userCoords[1] : null,
    };

    return fetch(`/misiones/${mision.id}/completar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify(payload),
    }).then(res => {
        if (!res.ok) return res.json().then(b => { throw new Error(b.message || 'Error'); });
        return res.json();
    });
}

/* ── Haversine ──────────────────────────────────────────────────── */
function calcularDistanciaMetros([lat1, lng1], [lat2, lng2]) {
    const toRad = d => d * Math.PI / 180;
    const R = 6371000;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a = Math.sin(dLat / 2) ** 2
        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/* ══════════════════════════════════════════════════════════════════
   ROUTING (OSRM público — walking)
   ══════════════════════════════════════════════════════════════════ */

/**
 * Llama a OSRM y devuelve la geometría de la ruta como array de [lat, lng].
 * Usa el perfil "foot" (a pie).
 */
async function obtenerRutaOSRM(userLat, userLng, misionLat, misionLng) {
    const url = `https://router.project-osrm.org/route/v1/foot/`
        + `${userLng},${userLat};${misionLng},${misionLat}`
        + `?geometries=geojson&overview=full`;
    const res = await fetch(url);
    const data = await res.json();
    if (!data.routes || !data.routes[0]) return null;

    // GeoJSON coordinates son [lng, lat] → invertir a [lat, lng] para Leaflet
    return data.routes[0].geometry.coordinates.map(([lng, lat]) => [lat, lng]);
}

/**
 * Dibuja (o reemplaza) la ruta para una misión concreta.
 */
async function trazarRutaMision(mision, userLat, userLng) {
    if (!mapa) return;
    if (mision.completada) return;
    if (mision.ejeX == null || mision.ejeY == null) return;

    // Eliminar ruta anterior de esta misión si existe
    eliminarRutaMision(mision.id);

    const mLat = parseFloat(mision.ejeX);
    const mLng = parseFloat(mision.ejeY);

    let puntos;
    try {
        puntos = await obtenerRutaOSRM(userLat, userLng, mLat, mLng);
    } catch {
        // Si falla OSRM, dibujar línea recta como fallback
        puntos = [[userLat, userLng], [mLat, mLng]];
    }

    if (!puntos || !mapa) return;

    const distancia = calcularDistanciaMetros([userLat, userLng], [mLat, mLng]);
    const distLabel = distancia < 1000
        ? `${Math.round(distancia)} m`
        : `${(distancia / 1000).toFixed(1)} km`;

    // Sombra blanca para legibilidad
    const sombra = L.polyline(puntos, {
        color: '#fff',
        weight: 7,
        opacity: 0.6,
    }).addTo(mapa);

    // Línea principal animada
    const linea = L.polyline(puntos, {
        color: '#4F46E5',
        weight: 4,
        opacity: 0.9,
        dashArray: '10 6',
        lineCap: 'round',
        lineJoin: 'round',
    }).addTo(mapa);

    // Etiqueta de distancia en el punto medio de la ruta
    const mid = puntos[Math.floor(puntos.length / 2)];
    const etiqueta = L.marker(mid, {
        icon: L.divIcon({
            className: '',
            html: `<div class="route-distance-label">${distLabel}</div>`,
            iconAnchor: [30, 12],
        }),
        interactive: false,
        zIndexOffset: -100,
    }).addTo(mapa);

    rutasCapas[mision.id] = [sombra, linea, etiqueta];
}

/**
 * Elimina las capas de ruta de una misión del mapa.
 */
function eliminarRutaMision(misionId) {
    if (rutasCapas[misionId]) {
        rutasCapas[misionId].forEach(layer => mapa && mapa.removeLayer(layer));
        delete rutasCapas[misionId];
    }
}

/**
 * (Re)traza rutas para todas las misiones con coordenadas no completadas.
 */
async function trazarTodasLasRutas(userLat, userLng) {
    for (const m of MISIONES) {
        if (!m.completada && m.ejeX != null && m.ejeY != null) {
            await trazarRutaMision(m, userLat, userLng);
        }
    }
}

/* ══════════════════════════════════════════════════════════════════
   PROXIMIDAD — completar misión al acercarse
   ══════════════════════════════════════════════════════════════════ */
function verificarProximidadMisiones(userLat, userLng) {
    MISIONES
        .filter(m => !m.completada && m.ejeX != null && m.ejeY != null)
        .forEach(m => {
            if (misionesProximidadCompletadas.has(m.id)) return;
            const dist = calcularDistanciaMetros(
                [userLat, userLng],
                [parseFloat(m.ejeX), parseFloat(m.ejeY)]
            );
            if (dist <= RADIO_PROXIMIDAD_M) {
                misionesProximidadCompletadas.add(m.id);
                console.log(`✓ Proximidad: ${dist.toFixed(1)} m — '${m.nombre}'`);

                completarMisionEnServidor(m)
                    .then(data => {
                        m.completada = true;
                        eliminarRutaMision(m.id);   // quitar la ruta del mapa
                        marcarMisionCompletadaEnDOM(m.id);
                        if (mapa && userCoords) renderMapaMisiones(userCoords[0], userCoords[1]);
                        mostrarToastMision(m.nombre, data.puntos_ganados ?? m.puntos);
                    })
                    .catch(err => console.error('Error completando misión:', err));
            }
        });
}

/* ── Toast ──────────────────────────────────────────────────────── */
function mostrarToastMision(nombre, puntos) {
    const toast = document.createElement('div');
    toast.className = 'mission-toast';
    toast.innerHTML = `
        <div class="mission-toast__icon">✓</div>
        <div class="mission-toast__text">
            <span class="mission-toast__title">¡Misión completada!</span>
            <span class="mission-toast__name">${nombre}</span>
        </div>
        <div class="mission-toast__pts">+${puntos} ptos</div>
    `;
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('visible'));
    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

/* ── Formatear ms ───────────────────────────────────────────────── */
function formatMs(ms) {
    if (ms <= 0) return '00:00:00';
    const s = Math.floor(ms / 1000);
    return [Math.floor(s / 3600), Math.floor((s % 3600) / 60), s % 60]
        .map(n => String(n).padStart(2, '0')).join(':');
}

/* ── DOM Update ─────────────────────────────────────────────── */
function marcarMisionCompletadaEnDOM(id) {
    const cards = document.querySelectorAll(`.mission-card[data-id="${id}"]`);
    cards.forEach(card => {
        card.classList.add('completed');
        const check = card.querySelector('.mission-card__check');
        if (check) check.textContent = '✓';
        const points = card.querySelector('.points-label');
        if (points && !points.textContent.startsWith('+')) {
            points.textContent = '+' + points.textContent;
        }
        const dir = card.querySelector('.direccion-sub');
        if (dir) dir.remove();
        const hint = card.querySelector('.route-hint');
        if (hint) hint.remove();
    });
}

/* ══════════════════════════════════════════════════════════════════
   MAPA (Leaflet)
   ══════════════════════════════════════════════════════════════════ */
function initMap(lat, lng) {
    mapa = L.map('map', { center: [lat, lng], zoom: 15, zoomControl: true });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(mapa);

    marcadorUsuario = L.marker([lat, lng], {
        icon: L.divIcon({
            className: '',
            html: '<div style="width:18px;height:18px;background:#8FA8A6;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>',
            iconSize: [18, 18], iconAnchor: [9, 9],
        }),
        zIndexOffset: 1000,
    }).addTo(mapa).bindTooltip('Tu posición', { direction: 'top' });

    renderMapaMisiones(lat, lng);

    // Trazar rutas OSRM para las misiones con coordenadas
    trazarTodasLasRutas(lat, lng);
}

function misionIcon(completada) {
    const bg = completada ? '#56C470' : '#4F46E5';
    return L.divIcon({
        className: '',
        html: `<div style="width:22px;height:22px;background:${bg};border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>`,
        iconSize: [22, 22], iconAnchor: [11, 11],
    });
}

function renderMapaMisiones(userLat, userLng) {
    if (!mapa) return;

    marcadoresMisiones.forEach(l => mapa.removeLayer(l));
    marcadoresMisiones = [];

    MISIONES.forEach(m => {
        if (m.ejeX == null || m.ejeY == null) return;

        const lat = parseFloat(m.ejeX);
        const lng = parseFloat(m.ejeY);

        // Círculo de radio 50 m
        const circulo = L.circle([lat, lng], {
            radius: RADIO_PROXIMIDAD_M,
            color: m.completada ? '#56C470' : '#4F46E5',
            fillColor: m.completada ? '#56C470' : '#4F46E5',
            fillOpacity: 0.12,
            weight: 2,
        }).addTo(mapa);
        marcadoresMisiones.push(circulo);

        const popup = L.popup({ closeButton: false, maxWidth: 240 }).setContent(`
            <div class="mission-popup">
                <div class="mission-popup__title">${m.nombre}</div>
                <div class="mission-popup__pts">+${m.puntos} ptos</div>
                ${m.completada
                ? '<div class="mission-popup__completed">✓ Completada</div>'
                : `<div style="font-size:.72rem;color:#7A9190;margin-top:4px">🗺️ Ruta activa · acércate a ${RADIO_PROXIMIDAD_M} m</div>`}
                ${m.direccion ? `<div style="font-size:.75rem;color:#64748b;margin-top:6px">${m.direccion}</div>` : ''}
            </div>
        `.trim());

        const marker = L.marker([lat, lng], { icon: misionIcon(m.completada) })
            .addTo(mapa).bindPopup(popup);
        marcadoresMisiones.push(marker);
    });
}

/* ── Geolocalización ────────────────────────────────────────────── */
// Actualizar rutas como máximo cada 60 s para no saturar OSRM
let lastRouteUpdate = 0;
const ROUTE_UPDATE_INTERVAL = 60_000;

function esconderLoading() {
    if (elLoading) elLoading.classList.add('hidden');
}

function usarFallback() {
    const lat = 41.3851, lng = 2.1734;  // Barcelona
    userCoords = [lat, lng];
    initMap(lat, lng);
    esconderLoading();
}

function iniciarGeolocalizacion() {
    if (!navigator.geolocation) { usarFallback(); return; }

    navigator.geolocation.watchPosition(
        pos => {
            const coords = [pos.coords.latitude, pos.coords.longitude];
            userCoords = coords;
            lastAccuracyMeters = pos.coords.accuracy;
            lastSpeedKmh = pos.coords.speed != null ? pos.coords.speed * 3.6 : 0;

            if (!lastPosition) {
                lastPosition = coords;
            } else {
                recorridoTotal += calcularDistanciaMetros(lastPosition, coords);
                lastPosition = coords;
            }

            // Verificar proximidad
            verificarProximidadMisiones(coords[0], coords[1]);

            if (!mapa) {
                initMap(coords[0], coords[1]);
                esconderLoading();
                return;
            }

            if (marcadorUsuario) marcadorUsuario.setLatLng(coords);
            mapa.panTo(coords, { animate: true });

            // Actualizar rutas si han pasado ≥ 60 s
            const ahora = Date.now();
            if (ahora - lastRouteUpdate >= ROUTE_UPDATE_INTERVAL) {
                lastRouteUpdate = ahora;
                trazarTodasLasRutas(coords[0], coords[1]);
            }
        },
        () => usarFallback(),
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function centrarEnUbicacion() {
    if (!mapa || !userCoords) return;
    mapa.setView(userCoords, 15, { animate: true });
}

/* ── Timer ──────────────────────────────────────────────────────── */
function tickTimer() {
    if (!EVENTO || !EVENTO_FIN) {
        if (elCountdown) elCountdown.textContent = 'Evento no disponible';
        return;
    }
    const restante = EVENTO_FIN.getTime() - Date.now();
    if (elCountdown) elCountdown.textContent = restante > 0 ? formatMs(restante) : 'Evento finalizado';
}

/* ── Click en tarjeta → centrar en misión ───────────────────────── */
function abrirTarjetaEnMapa(event) {
    const card = event.target.closest('.mission-card');
    if (!card || !mapa) return;
    const id = Number(card.dataset.id);
    const mision = MISIONES.find(item => item.id === id);
    if (!mision || mision.ejeX == null || mision.ejeY == null) return;

    mapa.setView([parseFloat(mision.ejeX), parseFloat(mision.ejeY)], 16, { animate: true });

    marcadoresMisiones.forEach(layer => {
        if (!layer.getLatLng) return;
        const ll = layer.getLatLng();
        if (
            Math.abs(ll.lat - parseFloat(mision.ejeX)) < 0.0001 &&
            Math.abs(ll.lng - parseFloat(mision.ejeY)) < 0.0001
        ) layer.openPopup();
    });
}

/* ── Arranque ───────────────────────────────────────────────────── */
function inicializarEvento() {
    tickTimer();
    setInterval(tickTimer, 1000);
    iniciarGeolocalizacion();
    if (elLocationBtn) elLocationBtn.onclick = centrarEnUbicacion;
    document.onclick = abrirTarjetaEnMapa;
}

window.onload = inicializarEvento;
