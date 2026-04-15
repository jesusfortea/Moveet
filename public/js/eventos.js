'use strict';

const EVENTO = window.eventoData ?? null;
const MISIONES = window.misionesData ?? [];
const EVENTO_FIN = window.fechaFinEvento ? new Date(window.fechaFinEvento) : null;

const elLoading     = document.getElementById('map-loading');
const elMissions    = document.getElementById('missions-list');
const elCountdown   = document.getElementById('timer-countdown');
const elLocationBtn = document.getElementById('location-btn');

let mapa               = null;
let marcadorUsuario    = null;
let marcadoresMisiones = [];
let userCoords         = null;
let lastPosition       = null;

/* ── Sets de control de completado ─────────────────────────────── */
const misionesProximidadCompletadas = new Set();

/* ── CSRF ───────────────────────────────────────────────────────── */
function getCsrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.content : '';
}

/* ── Llamada al servidor para completar misión ──────────────────── */
function completarMisionEnServidor(mision) {
    return fetch(`/misiones/${mision.id}/completar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({}),
    }).then(res => {
        if (!res.ok) {
            return res.json().then(body => {
                throw new Error(body.message || 'Error al completar misión');
            });
        }
        return res.json();
    });
}

/* ── Calcular distancia en metros (Haversine) ───────────────────── */
function calcularDistanciaMetros([lat1, lng1], [lat2, lng2]) {
    const toRad = deg => deg * Math.PI / 180;
    const R = 6371000;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a = Math.sin(dLat / 2) ** 2
            + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/* ── Verificar proximidad (≤ 50 m) a misiones con coordenadas ───── */
const RADIO_PROXIMIDAD_M = 50;

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
                console.log(`Proximidad ≤ ${RADIO_PROXIMIDAD_M} m (${dist.toFixed(1)} m) — '${m.nombre}'`);

                completarMisionEnServidor(m)
                    .then(data => {
                        m.completada = true;
                        renderMisiones();
                        if (mapa && userCoords) {
                            renderMapaMisiones(userCoords[0], userCoords[1]);
                        }
                        mostrarToastMision(m.nombre, data.puntos_ganados ?? m.puntos);
                        console.log(`Misión '${m.nombre}' completada. Puntos: ${data.puntos}`);
                    })
                    .catch(err => console.error('Error completando misión por proximidad:', err));
            }
        });
}

/* ── Toast de misión completada ─────────────────────────────────── */
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

/* ── Formatear ms en HH:MM:SS ───────────────────────────────────── */
function formatMs(ms) {
    if (ms <= 0) return '00:00:00';
    const totalSecs = Math.floor(ms / 1000);
    const h = Math.floor(totalSecs / 3600);
    const m = Math.floor((totalSecs % 3600) / 60);
    const s = totalSecs % 60;
    return [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
}

/* ── Tarjeta de misión ──────────────────────────────────────────── */
function tarjetaMision(m) {
    const premiumBadge = m.premium
        ? '<span class="mission-card__premium">⭐ Premium</span>'
        : '';
    const direccion = m.direccion
        ? `<div class="mission-card__sub">📍 ${m.direccion}</div>`
        : '';
    const radioInfo = (m.ejeX != null && !m.completada)
        ? `<div class="mission-card__sub">🎯 Acércate a ${RADIO_PROXIMIDAD_M} m para completarla</div>`
        : '';

    return `
        <div class="mission-card ${m.completada ? 'completed' : ''}" data-id="${m.id}" role="listitem">
            <div class="mission-card__check">${m.completada ? '✓' : ''}</div>
            <div class="mission-card__body">
                <div class="mission-card__name">${m.nombre}</div>
                ${premiumBadge}
                ${direccion}
                ${radioInfo}
            </div>
            <div class="mission-card__points">+${m.puntos} ptos</div>
        </div>
    `.trim();
}

/* ── Renderizar lista de misiones ───────────────────────────────── */
function renderMisiones() {
    if (!EVENTO) {
        elMissions.innerHTML = `
            <div class="missions-empty">
                <div class="missions-empty__icon">🎯</div>
                <p>No hay misiones de evento disponibles.</p>
            </div>
        `;
        return;
    }

    if (MISIONES.length === 0) {
        elMissions.innerHTML = `
            <div class="missions-empty">
                <div class="missions-empty__icon">🎯</div>
                <p>El evento no tiene misiones disponibles.</p>
            </div>
        `;
        return;
    }

    // Completadas al final
    const ordenadas = [
        ...MISIONES.filter(m => !m.completada),
        ...MISIONES.filter(m =>  m.completada),
    ];
    elMissions.innerHTML = ordenadas.map(tarjetaMision).join('');
}

/* ── Mapa (Leaflet) ─────────────────────────────────────────────── */
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
            iconSize: [18, 18],
            iconAnchor: [9, 9],
        }),
        zIndexOffset: 1000,
    }).addTo(mapa).bindTooltip('Tu posición', { direction: 'top' });

    renderMapaMisiones(lat, lng);
}

function misionIcon(completada) {
    const bg = completada ? '#56C470' : '#4F46E5';
    return L.divIcon({
        className: '',
        html: `<div style="width:22px;height:22px;background:${bg};border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });
}

/* Dibuja un círculo de 50 m alrededor de cada misión con coordenadas */
function renderMapaMisiones(userLat, userLng) {
    if (!mapa) return;

    marcadoresMisiones.forEach(layer => mapa.removeLayer(layer));
    marcadoresMisiones = [];

    MISIONES.forEach(m => {
        if (m.ejeX == null || m.ejeY == null) return;

        const lat = parseFloat(m.ejeX);
        const lng = parseFloat(m.ejeY);

        // Círculo radio 50 m
        const circulo = L.circle([lat, lng], {
            radius: RADIO_PROXIMIDAD_M,
            color: m.completada ? '#56C470' : '#4F46E5',
            fillColor: m.completada ? '#56C470' : '#4F46E5',
            fillOpacity: 0.15,
            weight: 2,
        }).addTo(mapa);
        marcadoresMisiones.push(circulo);

        const popup = L.popup({ closeButton: false, maxWidth: 220 }).setContent(`
            <div class="mission-popup">
                <div class="mission-popup__title">${m.nombre}</div>
                <div class="mission-popup__pts">+${m.puntos} ptos</div>
                ${m.completada ? '<div class="mission-popup__completed">✓ Completada</div>' : `<div style="font-size:.72rem;color:#7A9190;margin-top:4px">Acércate a ${RADIO_PROXIMIDAD_M} m</div>`}
                ${m.direccion ? `<div style="font-size:.75rem;color:#64748b;margin-top:6px">${m.direccion}</div>` : ''}
            </div>
        `.trim());

        const marker = L.marker([lat, lng], { icon: misionIcon(m.completada) })
            .addTo(mapa)
            .bindPopup(popup);

        marcadoresMisiones.push(marker);
    });
}

/* ── Ocultar spinner ────────────────────────────────────────────── */
function esconderLoading() {
    if (elLoading) elLoading.classList.add('hidden');
}

function usarFallback() {
    const lat = 40.4168, lng = -3.7038;
    userCoords = [lat, lng];
    initMap(lat, lng);
    esconderLoading();
}

/* ── Geolocalización ────────────────────────────────────────────── */
function iniciarGeolocalizacion() {
    if (!navigator.geolocation) {
        usarFallback();
        return;
    }

    navigator.geolocation.watchPosition(
        pos => {
            const coords = [pos.coords.latitude, pos.coords.longitude];
            userCoords = coords;

            // Comprobar proximidad a misiones con coordenadas
            verificarProximidadMisiones(coords[0], coords[1]);

            if (!mapa) {
                initMap(coords[0], coords[1]);
                esconderLoading();
                return;
            }

            if (marcadorUsuario) {
                marcadorUsuario.setLatLng(coords);
            }
            mapa.panTo(coords, { animate: true });
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
    if (elCountdown) {
        elCountdown.textContent = restante > 0 ? formatMs(restante) : 'Evento finalizado';
    }
}

/* ── Click en tarjeta → centrar mapa ───────────────────────────── */
function abrirTarjetaEnMapa(event) {
    const card = event.target.closest('.mission-card');
    if (!card || !mapa) return;
    const id = Number(card.dataset.id);
    const mision = MISIONES.find(item => item.id === id);
    if (!mision || mision.ejeX == null || mision.ejeY == null) return;

    mapa.setView([parseFloat(mision.ejeX), parseFloat(mision.ejeY)], 16, { animate: true });
    marcadoresMisiones.forEach(layer => {
        if (layer.getLatLng) {
            const ll = layer.getLatLng();
            if (
                Math.abs(ll.lat - parseFloat(mision.ejeX)) < 0.0001 &&
                Math.abs(ll.lng - parseFloat(mision.ejeY)) < 0.0001
            ) {
                layer.openPopup();
            }
        }
    });
}

/* ── Arranque ───────────────────────────────────────────────────── */
function inicializarEvento() {
    renderMisiones();
    tickTimer();
    setInterval(tickTimer, 1000);

    iniciarGeolocalizacion();
    if (elLocationBtn) {
        elLocationBtn.addEventListener('click', centrarEnUbicacion);
    }
    document.addEventListener('click', abrirTarjetaEnMapa);
}

window.addEventListener('DOMContentLoaded', () => {
    inicializarEvento();
});
