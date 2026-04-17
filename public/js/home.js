/* ═══════════════════════════════════════════════════════════════════
   home.js — Moveet · Página principal
   ═══════════════════════════════════════════════════════════════════ */

'use strict';

/* ── Datos de misiones ─────────────────────────────────────────────
   En producción Laravel inyectará los datos reales mediante Blade:
   window.misionesData  = @json($misiones);
   window.misionesReset = @json($fechaLimite);   // ISO string
   ─────────────────────────────────────────────────────────────────── */
const MISIONES = window.misionesData ?? [
    /* Diarias */
    {
        id: 1, nombre: 'Caminar 500 metros.',
        puntos: 30, semanal: false, completada: false,
        premium: false, metros_requeridos: 500, ejeX: null, ejeY: null,
        direccion: null
    },
    {
        id: 2, nombre: 'Chatear con un amigo.',
        puntos: 10, semanal: false, completada: true,
        premium: false, metros_requeridos: null, ejeX: null, ejeY: null,
        direccion: null
    },
    {
        id: 3, nombre: 'Visitar la tienda Nike más cercana.',
        puntos: 50, semanal: false, completada: false,
        premium: true, ejeX: null, ejeY: null,      // se rellenan con geolocalización
        direccion: 'Nike Store, tu ciudad'
    },
    /* Semanales */
    {
        id: 4, nombre: 'Correr 5 km sin parar.',
        puntos: 100, semanal: true, completada: false,
        premium: false, ejeX: null, ejeY: null,
        direccion: null
    },
    {
        id: 5, nombre: 'Asistir a un evento local.',
        puntos: 200, semanal: true, completada: false,
        premium: true, ejeX: null, ejeY: null,
        direccion: null
    },
    {
        id: 6, nombre: 'Hacer 3 amigos nuevos en la app.',
        puntos: 80, semanal: true, completada: true,
        premium: false, ejeX: null, ejeY: null,
        direccion: null
    },
];

const EVENTO = window.eventoData ?? null;

/* Fecha límite del ciclo: diarias / semanales / evento.
   En producción: window.misionesReset = { diarias, semanales } */
const RESET_DATES = window.misionesReset || {
    diarias: (() => {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        d.setHours(0, 0, 0, 0);
        return d.toISOString();
    })(),
    semanales: (() => {
        const d = new Date();
        d.setDate(d.getDate() + 7);
        d.setHours(0, 0, 0, 0);
        return d.toISOString();
    })(),
    evento: window.fechaFinEvento || null,
};

/* ── Estado de la UI ────────────────────────────────────────────── */
let tabActiva = 'diarias';   // 'diarias' | 'semanales'
let resetDate = new Date(RESET_DATES[tabActiva]);
let mapa = null;
let marcadorUsuario = null;
let capasRutas = [];
let marcadoresMisiones = [];
let userCoords = null;
let lastPosition = null;
let recorridoTotal = 0;
const misionesConMeta = new Set();

/* ── DOM refs ───────────────────────────────────────────────────── */
const elLoading = document.getElementById('map-loading');
const elMissions = document.getElementById('missions-list');
const elCountdown = document.getElementById('timer-countdown');
const elBarFill = document.getElementById('timer-bar-fill');
const elChangBtn = document.getElementById('change-missions-btn');

function getCsrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.content : '';
}

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

/* ════════════════════════════════════════════════════════════════
   MAPA (Leaflet)
   ════════════════════════════════════════════════════════════════ */

/**
 * Inicializa el mapa Leaflet centrado en `lat, lng`.
 */
function initMap(lat, lng) {
    mapa = L.map('map', {
        center: [lat, lng],
        zoom: 15,
        zoomControl: true,
    });

    /* Tiles OpenStreetMap */
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(mapa);

    /* Marcador del usuario */
    const userIcon = L.divIcon({
        className: '',
        html: `<div style="
            width:18px; height:18px;
            background:#8FA8A6;
            border:3px solid #fff;
            border-radius:50%;
            box-shadow:0 2px 8px rgba(0,0,0,.3);
        "></div>`,
        iconSize: [18, 18],
        iconAnchor: [9, 9],
    });

    marcadorUsuario = L.marker([lat, lng], { icon: userIcon, zIndexOffset: 1000 })
        .addTo(mapa)
        .bindTooltip('Tu posición', { permanent: false, direction: 'top' });

    /* Dibujar marcadores y rutas de misiones con coordenadas */
    renderMapaMisiones(lat, lng);
}

/**
 * Icono personalizado para marcadores de misión.
 */
function misionIcon(completada) {
    const bg = completada ? '#56C470' : '#E06060';
    return L.divIcon({
        className: '',
        html: `<div style="
            width:22px; height:22px;
            background:${bg};
            border:3px solid #fff;
            border-radius:50%;
            box-shadow:0 2px 8px rgba(0,0,0,.3);
        "></div>`,
        iconSize: [22, 22],
        iconAnchor: [11, 11],
    });
}

/**
 * Elimina capas anteriores y dibuja las misiones del tab activo que
 * tengan coordenadas.
 */
function renderMapaMisiones(userLat, userLng) {
    /* Limpiar capas previas */
    capasRutas.forEach(c => mapa.removeLayer(c));
    marcadoresMisiones.forEach(m => mapa.removeLayer(m));
    capasRutas = [];
    marcadoresMisiones = [];

    let misiones;

    if (EVENTO) {
        misiones = MISIONES;
    } else {
        const esSemanal = tabActiva === 'semanales';
        misiones = MISIONES.filter(m => m.semanal === esSemanal);
    }

    misiones.forEach(m => {
        if (m.ejeX == null || m.ejeY == null) return;

        const lat = parseFloat(m.ejeX);
        const lng = parseFloat(m.ejeY);

        /* Polyline usuario → misión */
        const colorLinea = m.completada ? '#56C470' : '#E06060';
        const linea = L.polyline(
            [[userLat, userLng], [lat, lng]],
            { color: colorLinea, weight: 4, opacity: .85, dashArray: m.completada ? null : '8 6' }
        ).addTo(mapa);
        capasRutas.push(linea);

        /* Marcador misión */
        const popup = L.popup({ closeButton: false, maxWidth: 200 }).setContent(`
            <div class="mission-popup">
                <div class="mission-popup__title">${m.nombre}</div>
                <div class="mission-popup__pts">+${m.puntos} ptos</div>
                ${m.completada ? '<div class="mission-popup__completed">✓ Completada</div>' : ''}
                ${m.direccion ? `<div style="font-size:.72rem;color:#7A9190;margin-top:4px">${m.direccion}</div>` : ''}
            </div>
        `);

        const marcador = L.marker([lat, lng], { icon: misionIcon(m.completada) })
            .addTo(mapa)
            .bindPopup(popup);

        marcadoresMisiones.push(marcador);
    });
}

/* ════════════════════════════════════════════════════════════════
   MISIONES — Renderizado del panel
   ════════════════════════════════════════════════════════════════ */

/**
 * Genera el HTML de una tarjeta de misión.
 */
function tarjetaMision(m) {
    const completada = m.completada ? 'completed' : '';
    const checkIcon = m.completada ? '✓' : '';
    const puntosLabel = m.completada ? `+${m.puntos} ptos` : `${m.puntos} ptos`;
    const premiumBadge = m.premium
        ? `<span class="mission-card__premium">⭐ Premium</span>`
        : '';

    return `
        <div class="mission-card ${completada}" data-id="${m.id}" role="listitem">
            <div class="mission-card__check">${checkIcon}</div>
            <div class="mission-card__body">
                <div class="mission-card__name">${m.nombre}</div>
                ${premiumBadge}
                ${m.direccion && !m.completada
            ? `<div class="mission-card__sub">📍 ${m.direccion}</div>`
            : ''}
            </div>
            <div class="mission-card__points">${puntosLabel}</div>
        </div>
    `.trim();
}

/**
 * Renderiza la lista de misiones según el tab activo o todas si hay evento.
 */
function renderMisiones() {
    let lista;

    if (EVENTO) {
        // En caso de que se esté ejecutando un evento, mostraremos todas sus misiones y omitiremos filtro semanal/diario
        lista = MISIONES;
    } else {
        // Filtrar por tab activo
        const esSemanal = tabActiva === 'semanales';
        lista = MISIONES.filter(m => m.semanal === esSemanal);
    }

    if (lista.length === 0) {
        elMissions.innerHTML = `
            <div class="missions-empty">
                <div class="missions-empty__icon">🎯</div>
                <p>No hay misiones ${EVENTO ? 'en este evento' : (tabActiva === 'semanales' ? 'semanales' : 'diarias')} disponibles.</p>
            </div>
        `;
        return;
    }

    /* Completadas al final */
    const ordenadas = [
        ...lista.filter(m => !m.completada),
        ...lista.filter(m => m.completada),
    ];

    elMissions.innerHTML = ordenadas.map(tarjetaMision).join('');

    /* Recount badge */
    const cercanas = document.querySelector('.map-info-badge strong');
    if (cercanas) {
        const conCoords = lista.filter(m => m.ejeX && !m.completada).length;
        cercanas.textContent = conCoords;
    }
}

/* ════════════════════════════════════════════════════════════════
   TEMPORIZADOR
   ════════════════════════════════════════════════════════════════ */

/**
 * Formatea milisegundos restantes en HH:MM:SS.
 */
function formatMs(ms) {
    if (ms <= 0) return '00:00:00';
    const totalSecs = Math.floor(ms / 1000);
    const h = Math.floor(totalSecs / 3600);
    const m = Math.floor((totalSecs % 3600) / 60);
    const s = totalSecs % 60;
    return [h, m, s].map(n => String(n).padStart(2, '0')).join(':');
}

function tickTimer() {
    const ahora = Date.now();
    let limite;
    let total = 0;

    if (EVENTO && RESET_DATES.evento) {
        limite = new Date(RESET_DATES.evento).getTime();
    } else {
        limite = resetDate.getTime();
        total = tabActiva === 'semanales'
            ? 7 * 24 * 60 * 60 * 1000
            : 24 * 60 * 60 * 1000;   // 7 d o 24 h en ms
    }

    const restante = limite - ahora;
    const pct = total > 0 ? Math.max(0, Math.min(100, ((total - restante) / total) * 100)) : 0;

    elCountdown.textContent = formatMs(restante);
    elBarFill.style.width = pct + '%';

    if (restante <= 0) {
        if (EVENTO) {
            elCountdown.textContent = '¡Evento finalizado!';
        } else if (tabActiva === 'semanales') {
            elCountdown.textContent = '¡Renovando!';
            // Recargar la página para reiniciar el ciclo semanal
            setTimeout(() => location.reload(), 1000);
        } else {
            elCountdown.textContent = '¡Renovando!';
        }
    }
}

function calcularDistanciaMetros([lat1, lng1], [lat2, lng2]) {
    const toRad = deg => deg * Math.PI / 180;
    const R = 6371000; // metros
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
        + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2))
        * Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function verificarMetasRecorrido(distancia) {
    MISIONES.filter(m => !m.completada && Number.isFinite(m.metros_requeridos) && m.metros_requeridos > 0)
        .forEach(m => {
            if (!misionesConMeta.has(m.id) && distancia >= m.metros_requeridos) {
                misionesConMeta.add(m.id);
                console.log(`Metros recorridos: ${distancia.toFixed(1)} (misión '${m.nombre}' alcanzada)`);

                completarMisionEnServidor(m)
                    .then(data => {
                        m.completada = true;
                        renderMisiones();
                        if (mapa && userCoords) {
                            renderMapaMisiones(userCoords[0], userCoords[1]);
                        }
                        // Registro interno para confirmar la asignación del pago y estado
                        console.log(`Misión completada vía recorrido manual: ${m.nombre}. Puntos acumulados reportados: ${data.puntos}`);
                    })
                    .catch(err => {
                        console.error('Error completando misión:', err);
                    });
            }
        });
}

/**
 * Comprueba si el usuario está a ≤ 50 m de las coordenadas de cada misión.
 * Para misiones con ejeX/ejeY pero sin metros_requeridos (o además de ellos).
 */
const RADIO_PROXIMIDAD_M = 50;
const misionesProximidadCompletadas = new Set();

function verificarProximidadMisiones(userLat, userLng) {
    MISIONES
        .filter(m => !m.completada && m.ejeX != null && m.ejeY != null)
        .forEach(m => {
            if (misionesProximidadCompletadas.has(m.id)) return;
            const distancia = calcularDistanciaMetros(
                [userLat, userLng],
                [parseFloat(m.ejeX), parseFloat(m.ejeY)]
            );
            if (distancia <= RADIO_PROXIMIDAD_M) {
                misionesProximidadCompletadas.add(m.id);
                console.log(`Proximidad alcanzada a ${distancia.toFixed(1)} m — misión: '${m.nombre}'`);

                completarMisionEnServidor(m)
                    .then(data => {
                        m.completada = true;
                        renderMisiones();
                        if (mapa && userCoords) {
                            renderMapaMisiones(userCoords[0], userCoords[1]);
                        }
                        mostrarToastMision(m.nombre, data.puntos_ganados ?? m.puntos);
                        // Registro de obtención de puntos al acercarse físicamente al destino
                        console.log(`Misión por geofence '${m.nombre}' completada exitosamente. Nuevo balance de puntos: ${data.puntos}`);
                    })
                    .catch(err => console.error('Error completando misión por proximidad:', err));
            }
        });
}

/**
 * Muestra una notificación flotante cuando se completa una misión por proximidad.
 */
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
    // Animar entrada
    requestAnimationFrame(() => toast.classList.add('visible'));
    setTimeout(() => {
        toast.classList.remove('visible');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

/* ════════════════════════════════════════════════════════════════
   GEOLOCALIZACIÓN
   ════════════════════════════════════════════════════════ */

function iniciarGeolocalizacion() {
    if (!navigator.geolocation) {
        usarFallback();
        return;
    }

    navigator.geolocation.watchPosition(
        pos => {
            const coords = [pos.coords.latitude, pos.coords.longitude];

            if (!lastPosition) {
                lastPosition = coords;
            } else {
                const delta = calcularDistanciaMetros(lastPosition, coords);
                recorridoTotal += delta;
                lastPosition = coords;
                verificarMetasRecorrido(recorridoTotal);
            }

            // Comprobar proximidad a misiones con coordenadas
            verificarProximidadMisiones(coords[0], coords[1]);

            userCoords = coords;

            if (!mapa) {
                initMap(coords[0], coords[1]);
                ocultarLoading();
                return;
            }

            if (marcadorUsuario) {
                marcadorUsuario.setLatLng(coords);
            }

            if (mapa) {
                mapa.panTo(coords, { animate: true });
            }
        },
        _err => {
            usarFallback();
        },
        { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
    );
}

/**
 * Centra el mapa en la ubicación del usuario actual.
 */
function centrarEnUbicacion() {
    if (!userCoords || !mapa) {
        console.warn('Ubicación del usuario no disponible aún');
        return;
    }

    mapa.setView(userCoords, 15, { animate: true, duration: 0.8 });
}



/**
 * Fallback: Barcelona como coordenadas por defecto.
 */
function usarFallback() {
    const lat = 41.3851, lng = 2.1734;
    userCoords = [lat, lng];
    initMap(lat, lng);
    ocultarLoading();
}

function ocultarLoading() {
    if (elLoading) elLoading.classList.add('hidden');
}

/* ════════════════════════════════════════════════════════════════
   EVENTOS
   ════════════════════════════════════════════════════════════════ */

/**
 * Tabs: Diarias / Semanales
 */
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        tabActiva = btn.dataset.tab;
        resetDate = new Date(RESET_DATES[tabActiva]);
        tickTimer();
        renderMisiones();

        if (mapa && userCoords) {
            renderMapaMisiones(userCoords[0], userCoords[1]);
        }
    };
});

/**
 * Botón "Cambiar misiones"
 */
if (elChangBtn) {
    elChangBtn.onclick = () => {
        if (EVENTO) {
            alert('Estas misiones pertenecen al evento activo y no pueden cambiarse desde esta pantalla.');
            return;
        }

        /* Lógica para procesar un pago real si el usuario decide cambiar las misiones antes de tiempo.
           TODO: Reemplazar con redirección al controlador de la pasarela de pagos. */
        const confirmado = confirm('¿Cambiar misiones por 0,99 €?');
        if (confirmado) {
            alert('En desarrollo: Redirigiendo al proceso de pago…');
        }
    };
}

/**
 * Click en tarjeta de misión → centrar mapa en esa misión.
 */
elMissions.onclick = e => {
    const card = e.target.closest('.mission-card');
    if (!card || !mapa) return;

    const id = parseInt(card.dataset.id, 10);
    const m = MISIONES.find(x => x.id === id);
    if (!m || m.ejeX == null) return;

    mapa.setView([parseFloat(m.ejeX), parseFloat(m.ejeY)], 16, { animate: true });

    /* Abrir popup del marcador correspondiente */
    marcadoresMisiones.forEach(mk => {
        const ll = mk.getLatLng();
        if (
            Math.abs(ll.lat - parseFloat(m.ejeX)) < 0.0001 &&
            Math.abs(ll.lng - parseFloat(m.ejeY)) < 0.0001
        ) {
            mk.openPopup();
        }
    });
};

/* ════════════════════════════════════════════════════════════════
   ARRANQUE
   ════════════════════════════════════════════════════════════════ */

window.onload = () => {
    /* 1. Renderizar lista de misiones */
    renderMisiones();

    /* 2. Timer */
    if (EVENTO && RESET_DATES.evento) {
        resetDate = new Date(RESET_DATES.evento);
    }
    tickTimer();
    setInterval(tickTimer, 1000);

    /* 3. Mapa con geolocalización */
    iniciarGeolocalizacion();

    /* 4. Botón de ubicación */
    const locationBtn = document.getElementById('location-btn');
    if (locationBtn) {
        locationBtn.onclick = centrarEnUbicacion;
    }
};