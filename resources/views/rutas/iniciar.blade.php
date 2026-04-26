@extends('layouts.plantillaHome')

@section('title', 'Iniciar ruta · Moveet')

@push('styles')
<style>
    /* ── Contenedor principal mobile-first ── */
    .ruta-play-wrapper {
        width: 100%;
        min-height: calc(100vh - 15vh);
        display: flex;
        flex-direction: column;
        background: #f4f7f6;
    }

    /* ── Mapa (ocupa toda la pantalla en móvil) ── */
    .ruta-map-container {
        position: relative;
        width: 100%;
        height: 55vw;
        min-height: 280px;
        max-height: 420px;
        background: #d0dbd9;
        flex-shrink: 0;
    }

    #route-play-map {
        width: 100%;
        height: 100%;
    }

    /* ── Panel de información debajo del mapa ── */
    .ruta-info-panel {
        flex: 1;
        overflow-y: auto;
        padding: 0 16px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ── Tarjeta de estado (HUD) ── */
    .ruta-hud {
        background: #1E2A28;
        color: white;
        border-radius: 14px;
        padding: 14px 16px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        text-align: center;
        margin-top: -20px;
        position: relative;
        z-index: 10;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }

    .ruta-hud-item label {
        display: block;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #8FA8A6;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .ruta-hud-item .value {
        font-size: 17px;
        font-weight: 900;
        color: white;
        line-height: 1;
    }

    .ruta-hud-item .value.warn {
        color: #f59e0b;
    }

    .ruta-hud-item .value.danger {
        color: #ef4444;
    }

    .ruta-hud-item .value.ok {
        color: #4ade80;
    }

    /* ── Barra de progreso ── */
    .ruta-progress-card {
        background: white;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid #d8e3e0;
    }

    .ruta-progress-bar-bg {
        height: 10px;
        background: #e4ecea;
        border-radius: 999px;
        overflow: hidden;
        margin: 8px 0;
    }

    .ruta-progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #8FA8A6, #5d7e7c);
        border-radius: 999px;
        transition: width 0.5s ease;
    }

    /* ── Tarjeta de estado/alerta ── */
    .ruta-status-card {
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .ruta-status-card.normal { background: #eef4f3; border-color: #d2dedc; color: #1E2A28; }
    .ruta-status-card.warning { background: #fff4db; border-color: #f59e0b; color: #9a6700; }
    .ruta-status-card.danger { background: #fee2e2; border-color: #ef4444; color: #991b1b; }
    .ruta-status-card.success { background: #d1fae5; border-color: #4ade80; color: #065f46; }

    /* ── Lista de checkpoints ── */
    .cp-list {
        display: grid;
        gap: 8px;
    }

    .cp-item {
        background: white;
        border: 1px solid #d8e1df;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .cp-item.done { background: #e9f6ee; border-color: #86efac; }
    .cp-item.current { background: #fff4db; border-color: #f59e0b; }

    .cp-badge {
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
    }

    .cp-badge.done { background: #166534; color: white; }
    .cp-badge.current { background: #9a6700; color: white; }
    .cp-badge.pending { background: #d1d5db; color: #374151; }

    /* ── Anti-cheat banner ── */
    .anticheat-banner {
        background: #fee2e2;
        border: 2px solid #ef4444;
        border-radius: 12px;
        padding: 12px 16px;
        color: #991b1b;
        font-weight: 700;
        font-size: 13px;
        display: none;
        align-items: center;
        gap: 10px;
    }

    .anticheat-banner.visible {
        display: flex;
    }

    /* ── Botón volver ── */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef4f3;
        color: #1E2A28;
        border: 1px solid #d2dedc;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        margin: 12px 16px 0;
        width: fit-content;
    }

    /* ── Desktop overrides ── */
    @media (min-width: 900px) {
        .ruta-play-wrapper {
            display: grid;
            grid-template-columns: 1fr 380px;
            grid-template-rows: 1fr;
            padding: 24px clamp(16px, 2vw, 40px) 32px;
            gap: 20px;
            align-items: start;
            background: #f4f7f6;
        }

        .ruta-map-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ruta-map-container {
            height: min(72vh, 680px);
            max-height: unset;
            border-radius: 16px;
            overflow: hidden;
        }

        .ruta-info-panel {
            padding: 0;
        }

        .ruta-hud {
            margin-top: 0;
            order: -1;
        }

        .btn-back {
            margin: 0 0 12px;
        }

        .ruta-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }
    }

    @media (max-width: 899px) {
        .ruta-map-section {
            width: 100%;
        }

        .ruta-header {
            padding: 12px 16px 0;
        }

        .ruta-header h1 {
            font-size: 1.4rem;
        }
    }
</style>
@endpush

@section('content')
<div class="ruta-play-wrapper">

    {{-- ── Sección del mapa ── --}}
    <div class="ruta-map-section">

        {{-- Header (solo desktop, en móvil va dentro del panel) --}}
        <div class="ruta-header hidden md:flex justify-between items-start gap-3">
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; color: #1E2A28; margin: 0;">{{ $ruta->titulo }}</h1>
                <p style="color: #516260; font-size: 13px; margin: 4px 0 0;">Sigue el mapa. Llegarás a cada punto y se verificará automáticamente.</p>
            </div>
            <a href="{{ route('rutas.index') }}" class="btn-back">← Volver</a>
        </div>

        {{-- Mapa --}}
        <div class="ruta-map-container">
            <div id="route-play-map" style="width:100%; height:100%;"></div>
        </div>

        {{-- HUD con métricas en tiempo real --}}
        <div class="ruta-hud">
            <div class="ruta-hud-item">
                <label>Velocidad</label>
                <div class="value" id="hud-speed">— km/h</div>
            </div>
            <div class="ruta-hud-item">
                <label>Distancia al punto</label>
                <div class="value" id="hud-distance">— m</div>
            </div>
            <div class="ruta-hud-item">
                <label>Progreso</label>
                <div class="value" id="hud-progress">{{ $attempt->current_checkpoint_index }}/{{ $attempt->checkpoint_total }}</div>
            </div>
        </div>

    </div>{{-- fin ruta-map-section --}}

    {{-- ── Panel lateral / inferior ── --}}
    <div class="ruta-info-panel">

        {{-- Botón volver (móvil) --}}
        <div class="md:hidden">
            <a href="{{ route('rutas.index') }}" class="btn-back">← Volver a rutas</a>
        </div>

        {{-- Título (móvil) --}}
        <div class="md:hidden" style="padding: 0 0 4px;">
            <h1 style="font-size: 1.3rem; font-weight: 800; color: #1E2A28; margin: 0;">{{ $ruta->titulo }}</h1>
            <p style="color: #516260; font-size: 12px; margin: 4px 0 0;">Sigue el mapa y verifica cada punto.</p>
        </div>

        @if(session('status'))
            <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 14px; border-radius: 10px;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Banner anti-cheat --}}
        <div class="anticheat-banner" id="anticheat-banner">
            <span style="font-size: 20px;">🚨</span>
            <div>
                <div>Velocidad anómala detectada</div>
                <div id="anticheat-msg" style="font-size: 12px; font-weight: 500; margin-top: 3px;"></div>
            </div>
        </div>

        {{-- Estado actual --}}
        <div class="ruta-status-card normal" id="status-card">
            <span id="status-icon">📍</span>
            <span id="route-status-text">Obteniendo ubicación...</span>
        </div>

        {{-- Barra de progreso --}}
        <div class="ruta-progress-card">
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #516260; font-weight: 600;">
                <span>Progreso de la ruta</span>
                <span id="route-progress-text">{{ $attempt->current_checkpoint_index }} / {{ $attempt->checkpoint_total }}</span>
            </div>
            <div class="ruta-progress-bar-bg">
                <div class="ruta-progress-bar-fill" id="route-progress-bar"
                    style="width: {{ $attempt->checkpoint_total > 0 ? round(($attempt->current_checkpoint_index / $attempt->checkpoint_total) * 100) : 0 }}%;">
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #7a9190;">
                <span>🏁 Inicio</span>
                <span>+{{ $ruta->puntos_recompensa }} puntos al completar</span>
            </div>
        </div>

        {{-- Info de la ruta --}}
        <div class="ruta-progress-card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #1E2A28;">
            <div>
                <div style="color: #7a9190; font-size: 11px; font-weight: 700; margin-bottom: 3px;">DIFICULTAD</div>
                <div style="font-weight: 700;">{{ ucfirst($ruta->dificultad) }}</div>
            </div>
            <div>
                <div style="color: #7a9190; font-size: 11px; font-weight: 700; margin-bottom: 3px;">DISTANCIA</div>
                <div style="font-weight: 700;">{{ number_format($ruta->distancia_metros / 1000, 1) }} km</div>
            </div>
            <div>
                <div style="color: #7a9190; font-size: 11px; font-weight: 700; margin-bottom: 3px;">RADIO DE VALIDACIÓN</div>
                <div style="font-weight: 700;">{{ $attempt->verification_threshold_meters }} m</div>
            </div>
            <div>
                <div style="color: #7a9190; font-size: 11px; font-weight: 700; margin-bottom: 3px;">PREMIUM</div>
                <div style="font-weight: 700;">{{ $ruta->premium_only ? 'Sí' : 'No' }}</div>
            </div>
        </div>

        {{-- Lista checkpoints --}}
        <div class="ruta-progress-card">
            <h3 style="margin: 0 0 10px; color: #1E2A28; font-size: 14px; font-weight: 800;">Puntos de la ruta</h3>
            <div class="cp-list" id="checkpoint-list"></div>
        </div>

        {{-- Nota anti-cheat --}}
        <div style="background: #f0f2f1; border-radius: 10px; padding: 12px 14px; font-size: 12px; color: #516260;">
            <strong>🛡️ Sistema anti-trampas activo:</strong> La velocidad y ubicación GPS se validan en tiempo real. Si se detecta una velocidad superior a 50 km/h, la verificación será bloqueada automáticamente.
        </div>

    </div>{{-- fin ruta-info-panel --}}

</div>{{-- fin ruta-play-wrapper --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const MAX_WALKING_SPEED_KMH = 50;
    const MAX_GPS_ACCURACY_METERS = 80;

    const token = @json($attempt->verification_token);
    const checkpoints = @json($checkpoints);
    const currentIndex = {{ (int) $attempt->current_checkpoint_index }};
    const mapEl = document.getElementById('route-play-map');

    // HUD elements
    const hudSpeed = document.getElementById('hud-speed');
    const hudDistance = document.getElementById('hud-distance');
    const hudProgress = document.getElementById('hud-progress');
    const progressText = document.getElementById('route-progress-text');
    const statusText = document.getElementById('route-status-text');
    const statusCard = document.getElementById('status-card');
    const progressBar = document.getElementById('route-progress-bar');
    const checkpointList = document.getElementById('checkpoint-list');
    const anticheatBanner = document.getElementById('anticheat-banner');
    const anticheatMsg = document.getElementById('anticheat-msg');
    const statusIcon = document.getElementById('status-icon');

    if (typeof L === 'undefined' || !mapEl) {
        return;
    }

    // ── Mapa ──
    const map = L.map('route-play-map');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const latlngs = checkpoints.map(p => [p.lat, p.lng]);

    if (latlngs.length > 0) {
        const line = L.polyline(latlngs, { color: '#8FA8A6', weight: 5, opacity: 0.8 }).addTo(map);
        map.fitBounds(line.getBounds().pad(0.25));
    }

    // Icono de usuario
    const userIcon = L.divIcon({
        className: '',
        html: `<div style="width:20px;height:20px;background:#3b82f6;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    });

    let userMarker = null;
    let userCircle = null;

    // Markers de checkpoints
    const cpMarkers = checkpoints.map((point, index) => {
        const color = index < currentIndex ? '#22c55e' : (index === currentIndex ? '#f59e0b' : '#8FA8A6');
        const marker = L.circleMarker([point.lat, point.lng], {
            radius: 10,
            color,
            fillColor: color,
            fillOpacity: 0.9,
            weight: 3,
        }).addTo(map).bindPopup(`Punto ${index + 1}`);
        return marker;
    });

    // ── Render checkpoints list ──
    let lastCompletedIndex = currentIndex;

    const renderCheckpointList = (activeIdx) => {
        checkpointList.innerHTML = checkpoints.map((point, index) => {
            const done = index < activeIdx;
            const current = index === activeIdx;
            const cls = done ? 'done' : (current ? 'current' : '');
            const badgeCls = done ? 'done' : (current ? 'current' : 'pending');
            const label = done ? '✓ Completado' : (current ? '⭐ Siguiente' : 'Pendiente');

            return `<div class="cp-item ${cls}">
                <div>
                    <div style="font-weight:700;font-size:14px;color:#1E2A28;">Punto ${index + 1}</div>
                    <div style="font-size:11px;color:#7a9190;margin-top:2px;">${point.lat.toFixed(5)}, ${point.lng.toFixed(5)}</div>
                </div>
                <span class="cp-badge ${badgeCls}">${label}</span>
            </div>`;
        }).join('');
    };

    // ── Update progress ──
    const updateProgress = (completedIdx) => {
        lastCompletedIndex = completedIdx;
        const total = checkpoints.length;
        const done = Math.min(completedIdx, total);

        progressText.textContent = `${done} / ${total}`;
        hudProgress.textContent = `${done}/${total}`;
        const pct = total > 0 ? Math.round((done / total) * 100) : 0;
        progressBar.style.width = pct + '%';

        if (done >= total) {
            setStatus('success', '🎉', '¡Ruta completada! ¡Puntos otorgados!');
        } else {
            setStatus('normal', '📍', `Punto ${done + 1} en curso`);
        }

        // Actualizar colores de markers
        cpMarkers.forEach((marker, index) => {
            const color = index < done ? '#22c55e' : (index === done ? '#f59e0b' : '#8FA8A6');
            marker.setStyle({ color, fillColor: color });
        });

        renderCheckpointList(done);
    };

    const setStatus = (type, icon, msg) => {
        statusCard.className = `ruta-status-card ${type}`;
        statusIcon.textContent = icon;
        statusText.textContent = msg;
    };

    // ── Formatear distancia ──
    const formatDistance = (meters) => {
        if (meters >= 1000) return (meters / 1000).toFixed(1) + ' km';
        return Math.round(meters) + ' m';
    };

    // ── Anti-cheat: validar velocidad ──
    const checkAntiCheat = (positionCoords) => {
        const speedMs = positionCoords.speed; // metros por segundo
        const speedKmh = speedMs != null ? speedMs * 3.6 : null;

        if (speedKmh !== null) {
            const rounded = speedKmh.toFixed(1);
            hudSpeed.textContent = rounded + ' km/h';

            if (speedKmh > MAX_WALKING_SPEED_KMH) {
                hudSpeed.className = 'value danger';
                anticheatBanner.classList.add('visible');
                anticheatMsg.textContent = `Velocidad actual: ${rounded} km/h. Máximo permitido: ${MAX_WALKING_SPEED_KMH} km/h.`;
                return false;
            } else if (speedKmh > 15) {
                hudSpeed.className = 'value warn';
            } else {
                hudSpeed.className = 'value ok';
            }
        } else {
            hudSpeed.textContent = '— km/h';
            hudSpeed.className = 'value';
        }

        anticheatBanner.classList.remove('visible');
        return true;
    };

    // ── Enviar verificación de checkpoint ──
    const sendCheckpoint = async (index, coords) => {
        const response = await fetch(@json(route('rutas.verificar', $ruta)), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                token,
                checkpoint_index: index,
                latitude: coords.latitude,
                longitude: coords.longitude,
                speed_kmh: coords.speed != null ? coords.speed * 3.6 : null,
                accuracy_meters: coords.accuracy,
            }),
        });

        if (response.ok) {
            const data = await response.json().catch(() => ({}));
            const completed = data.completed || (index + 1 >= checkpoints.length);
            updateProgress(index + 1);
            if (completed) {
                setStatus('success', '🎉', '¡Ruta completada! Puntos otorgados.');
            }
            return;
        }

        const payload = await response.json().catch(() => ({}));
        throw new Error(payload.message || 'No se pudo verificar el punto.');
    };

    // ── Watch position ──
    const startTracking = () => {
        if (!navigator.geolocation) {
            setStatus('warning', '⚠️', 'Tu navegador no soporta geolocalización.');
            return;
        }

        let activeIndex = {{ (int) $attempt->current_checkpoint_index }};
        let sending = false;

        navigator.geolocation.watchPosition(async (position) => {
            const { latitude, longitude, accuracy } = position.coords;

            // Actualizar marcador de usuario
            if (!userMarker) {
                userMarker = L.marker([latitude, longitude], { icon: userIcon }).addTo(map);
            } else {
                userMarker.setLatLng([latitude, longitude]);
            }

            // Círculo de precisión
            if (userCircle) {
                map.removeLayer(userCircle);
            }
            if (accuracy < 200) {
                userCircle = L.circle([latitude, longitude], {
                    radius: accuracy,
                    color: '#3b82f6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.08,
                    weight: 1,
                }).addTo(map);
            }

            if (activeIndex >= checkpoints.length) {
                hudDistance.textContent = '—';
                return;
            }

            const target = checkpoints[activeIndex];
            const distance = map.distance([latitude, longitude], [target.lat, target.lng]);

            // Actualizar HUD de distancia
            hudDistance.textContent = formatDistance(distance);

            if (sending) return;

            // Validar anti-cheat
            const speedOk = checkAntiCheat(position.coords);

            if (!speedOk) {
                setStatus('danger', '🚨', 'Velocidad demasiado alta. Camina para poder verificar.');
                return;
            }

            const threshold = {{ (float) $attempt->verification_threshold_meters }};

            if (distance <= threshold) {
                sending = true;
                setStatus('warning', '⏳', `Verificando punto ${activeIndex + 1}...`);

                try {
                    await sendCheckpoint(activeIndex, position.coords);
                    activeIndex += 1;
                } catch (error) {
                    setStatus('warning', '⚠️', error.message);
                } finally {
                    sending = false;
                }
            } else {
                setStatus('normal', '📍', `Acércate al punto ${activeIndex + 1} (a ${formatDistance(distance)})`);
            }

        }, (error) => {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    setStatus('danger', '🔒', 'Permiso de ubicación denegado. Actívalo en tu navegador.');
                    break;
                case error.POSITION_UNAVAILABLE:
                    setStatus('warning', '📡', 'Ubicación no disponible. Comprueba tu GPS.');
                    break;
                default:
                    setStatus('warning', '⚠️', 'No se pudo obtener tu ubicación.');
            }
        }, {
            enableHighAccuracy: true,
            maximumAge: 3000,
            timeout: 15000,
        });
    };

    // ── Init ──
    renderCheckpointList(currentIndex);
    updateProgress(currentIndex);
    startTracking();
});
</script>
@endsection
