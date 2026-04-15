'use strict';

const EVENTO = window.eventoData ?? null;
const MISIONES = window.misionesData ?? [];
const EVENTO_FIN = window.fechaFinEvento ? new Date(window.fechaFinEvento) : null;

const elLoading = document.getElementById('map-loading');
const elMissions = document.getElementById('missions-list');
const elCountdown = document.getElementById('timer-countdown');
const elLocationBtn = document.getElementById('location-btn');

let mapa = null;
let marcadorUsuario = null;
let marcadoresMisiones = [];
let userCoords = null;
let lastPosition = null;

function formatMs(ms) {
    if (ms <= 0) {
        return '00:00:00';
    }
    const totalSecs = Math.floor(ms / 1000);
    const h = Math.floor(totalSecs / 3600);
    const m = Math.floor((totalSecs % 3600) / 60);
    const s = totalSecs % 60;
    return [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
}

function tarjetaMision(m) {
    const premiumBadge = m.premium ? '<span class="mission-card__premium">⭐ Premium</span>' : '';
    const direccion = m.direccion ? `<div class="mission-card__sub">📍 ${m.direccion}</div>` : '';

    return `
        <div class="mission-card ${m.completada ? 'completed' : ''}" data-id="${m.id}" role="listitem">
            <div class="mission-card__check">${m.completada ? '✓' : ''}</div>
            <div class="mission-card__body">
                <div class="mission-card__name">${m.nombre}</div>
                ${premiumBadge}
                ${direccion}
            </div>
            <div class="mission-card__points">+${m.puntos} ptos</div>
        </div>
    `.trim();
}

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

    elMissions.innerHTML = MISIONES.map(tarjetaMision).join('');
}

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

function misionIcon() {
    return L.divIcon({
        className: '',
        html: '<div style="width:22px;height:22px;background:#4F46E5;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,.3)"></div>',
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });
}

function renderMapaMisiones(userLat, userLng) {
    if (!mapa) return;

    marcadoresMisiones.forEach(marker => mapa.removeLayer(marker));
    marcadoresMisiones = [];

    MISIONES.forEach(m => {
        if (m.ejeX == null || m.ejeY == null) {
            return;
        }

        const lat = parseFloat(m.ejeX);
        const lng = parseFloat(m.ejeY);
        const popup = L.popup({ closeButton: false, maxWidth: 220 }).setContent(`
            <div class="mission-popup">
                <div class="mission-popup__title">${m.nombre}</div>
                <div class="mission-popup__pts">+${m.puntos} ptos</div>
                ${m.direccion ? `<div style="font-size:.75rem;color:#64748b;margin-top:6px">${m.direccion}</div>` : ''}
            </div>
        `.trim());

        const marker = L.marker([lat, lng], { icon: misionIcon() })
            .addTo(mapa)
            .bindPopup(popup);

        marcadoresMisiones.push(marker);
    });
}

function esconderLoading() {
    if (elLoading) {
        elLoading.classList.add('hidden');
    }
}

function usarFallback() {
    const lat = 40.4168;
    const lng = -3.7038;
    userCoords = [lat, lng];
    initMap(lat, lng);
    esconderLoading();
}

function iniciarGeolocalizacion() {
    if (!navigator.geolocation) {
        usarFallback();
        return;
    }

    navigator.geolocation.watchPosition(
        pos => {
            const coords = [pos.coords.latitude, pos.coords.longitude];
            userCoords = coords;

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
        () => {
            usarFallback();
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

function centrarEnUbicacion() {
    if (!mapa || !userCoords) return;
    mapa.setView(userCoords, 15, { animate: true });
}

function tickTimer() {
    if (!EVENTO || !EVENTO_FIN) {
        if (elCountdown) {
            elCountdown.textContent = 'Evento no disponible';
        }
        return;
    }

    const restante = EVENTO_FIN.getTime() - Date.now();
    if (elCountdown) {
        elCountdown.textContent = restante > 0 ? formatMs(restante) : 'Evento finalizado';
    }
}

function aplicarCoordsFake() {
    if (!userCoords || MISIONES.length === 0) return;
    MISIONES.forEach((m, index) => {
        if (m.ejeX == null || m.ejeY == null) {
            const offsets = [
                [0.0025, 0.0030],
                [-0.0028, 0.0022],
                [0.0034, -0.0029],
                [-0.0032, -0.0038],
                [0.0041, 0.0012],
            ];
            const [dy, dx] = offsets[index % offsets.length];
            m.ejeX = userCoords[0] + dy;
            m.ejeY = userCoords[1] + dx;
        }
    });
}

function abrirTarjetaEnMapa(event) {
    const card = event.target.closest('.mission-card');
    if (!card || !mapa) return;
    const id = Number(card.dataset.id);
    const mision = MISIONES.find(item => item.id === id);
    if (!mision || mision.ejeX == null || mision.ejeY == null) return;

    mapa.setView([parseFloat(mision.ejeX), parseFloat(mision.ejeY)], 16, { animate: true });
    marcadoresMisiones.forEach(marker => {
        const latlng = marker.getLatLng();
        if (Math.abs(latlng.lat - parseFloat(mision.ejeX)) < 0.0001 && Math.abs(latlng.lng - parseFloat(mision.ejeY)) < 0.0001) {
            marker.openPopup();
        }
    });
}

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
