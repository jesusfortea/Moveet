@extends('layouts.plantillaHome')

@section('title', 'Iniciar ruta · Moveet')

@push('styles')
<style>
    .route-play-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
        gap: 18px;
        align-items: start;
        width: 100%;
    }

    .route-play-map {
        height: min(72vh, 760px);
        min-height: 540px;
        border-radius: 14px;
        overflow: hidden;
    }

    @media (max-width: 980px) {
        .route-play-layout {
            grid-template-columns: 1fr;
        }

        .route-play-map {
            height: 50vh;
            min-height: 300px;
        }
    }
</style>
@endpush

@section('content')
<div style="width: 100%; padding: 24px clamp(16px, 2.2vw, 32px) 32px; box-sizing: border-box;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; width: 100%;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: #1E2A28;">Iniciar ruta</h1>
            <p style="margin: 6px 0 0; color: #516260; font-weight: 600;">Sigue el mapa. Cuando llegues a cada punto, la app lo verificara y avanzara al siguiente.</p>
        </div>
        <a href="{{ route('rutas.index') }}" style="background: #8FA8A6; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700;">Volver</a>
    </div>

    @if(session('status'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 14px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    <div class="route-play-layout">
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 16px; padding: 14px; min-width: 0;">
            <div id="route-play-map" class="route-play-map"></div>
        </div>

        <div style="display: grid; gap: 12px; align-self: start; min-width: 0;">
            <div style="background: #eef4f3; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                <h2 style="margin: 0 0 8px; color: #1E2A28; font-size: 1.05rem;">{{ $ruta->titulo }}</h2>
                <p style="margin: 0 0 12px; color: #4f6461; font-size: 13px; line-height: 1.5;">{{ $ruta->descripcion }}</p>
                <div style="display: grid; gap: 6px; font-size: 13px; color: #1E2A28;">
                    <div><strong>Puntos:</strong> +{{ $ruta->puntos_recompensa }}</div>
                    <div><strong>Nivel minimo:</strong> {{ $ruta->min_nivel }}</div>
                    <div><strong>Premium:</strong> {{ $ruta->premium_only ? 'Sí' : 'No' }}</div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                <h3 style="margin: 0 0 10px; color: #1E2A28; font-size: 1rem;">Progreso</h3>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: #516260;">
                    <span id="route-progress-text">{{ $attempt->current_checkpoint_index }} / {{ $attempt->checkpoint_total }}</span>
                    <span id="route-status-text">Esperando ubicacion...</span>
                </div>
                <div style="height: 10px; background: #e4ecea; border-radius: 999px; overflow: hidden; margin-bottom: 10px;">
                    <div id="route-progress-bar" style="height: 100%; width: {{ $attempt->checkpoint_total > 0 ? round(($attempt->current_checkpoint_index / $attempt->checkpoint_total) * 100) : 0 }}%; background: #8FA8A6;"></div>
                </div>
                <div style="display: grid; gap: 8px; font-size: 13px; color: #516260;">
                    <div><strong>Intento:</strong> activo</div>
                    <div><strong>Radio de validacion:</strong> {{ $attempt->verification_threshold_meters }} m</div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                <h3 style="margin: 0 0 10px; color: #1E2A28; font-size: 1rem;">Checkpoints</h3>
                <div id="checkpoint-list" style="display: grid; gap: 8px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const routeId = {{ $ruta->id }};
        const token = @json($attempt->verification_token);
        const checkpoints = @json($checkpoints);
        const currentIndex = {{ (int) $attempt->current_checkpoint_index }};
        const mapEl = document.getElementById('route-play-map');
        const progressText = document.getElementById('route-progress-text');
        const statusText = document.getElementById('route-status-text');
        const progressBar = document.getElementById('route-progress-bar');
        const checkpointList = document.getElementById('checkpoint-list');

        if (typeof L === 'undefined' || !mapEl) {
            return;
        }

        const map = L.map('route-play-map');
        const latlngs = checkpoints.map((point) => [point.lat, point.lng]);
        const line = L.polyline(latlngs, { color: '#8FA8A6', weight: 5 }).addTo(map);
        const bounds = line.getBounds();
        map.fitBounds(bounds.pad(0.2));
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const icons = checkpoints.map((point, index) => {
            const color = index < currentIndex ? '#2e7d32' : (index === currentIndex ? '#f59e0b' : '#8FA8A6');
            return L.circleMarker([point.lat, point.lng], {
                radius: 10,
                color,
                fillColor: color,
                fillOpacity: 0.9,
                weight: 3,
            }).addTo(map).bindPopup(`Punto ${index + 1}`);
        });

        const renderCheckpointList = () => {
            checkpointList.innerHTML = checkpoints.map((point, index) => {
                const state = index < currentIndex ? 'Completado' : (index === currentIndex ? 'Siguiente' : 'Pendiente');
                const bg = index < currentIndex ? '#e9f6ee' : (index === currentIndex ? '#fff4db' : '#f4f5f5');
                const color = index < currentIndex ? '#1e613b' : (index === currentIndex ? '#9a6700' : '#5d6f6d');

                return `<div style="background:${bg}; border:1px solid #d8e1df; border-radius:10px; padding:10px 12px; display:flex; justify-content:space-between; gap:10px; align-items:center;">
                    <div>
                        <strong>Punto ${index + 1}</strong>
                        <div style="font-size:12px; color:#5d6f6d; margin-top:4px;">${point.lat.toFixed(5)}, ${point.lng.toFixed(5)}</div>
                    </div>
                    <span style="font-size:12px; font-weight:800; color:${color};">${state}</span>
                </div>`;
            }).join('');
        };

        const updateProgress = (completedIndex) => {
            const total = checkpoints.length;
            const done = Math.min(completedIndex, total);
            progressText.textContent = `${done} / ${total}`;
            progressBar.style.width = total > 0 ? `${Math.round((done / total) * 100)}%` : '0%';
            statusText.textContent = done >= total ? 'Ruta completada' : `Punto ${done + 1} en curso`;
            icons.forEach((icon, index) => {
                const color = index < done ? '#2e7d32' : (index === done ? '#f59e0b' : '#8FA8A6');
                icon.setStyle({ color, fillColor: color });
            });
            renderCheckpointList();
        };

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
                }),
            });

            if (response.ok) {
                const completed = index + 1 >= checkpoints.length;
                updateProgress(index + 1);
                if (completed) {
                    statusText.textContent = 'Ruta completada. Puntos otorgados.';
                }
                return;
            }

            const payload = await response.json().catch(() => ({}));
            throw new Error(payload.message || 'No se pudo verificar el punto.');
        };

        const watchPosition = () => {
            if (!navigator.geolocation) {
                statusText.textContent = 'Tu navegador no soporta geolocalizacion.';
                return;
            }

            let activeIndex = {{ (int) $attempt->current_checkpoint_index }};
            let sending = false;

            navigator.geolocation.watchPosition(async (position) => {
                if (sending || activeIndex >= checkpoints.length) {
                    return;
                }

                const target = checkpoints[activeIndex];
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const distance = map.distance([lat, lng], [target.lat, target.lng]);

                if (distance <= {{ (float) $attempt->verification_threshold_meters }}) {
                    sending = true;
                    statusText.textContent = `Punto ${activeIndex + 1} verificado...`;

                    try {
                        await sendCheckpoint(activeIndex, position.coords);
                        activeIndex += 1;
                    } catch (error) {
                        statusText.textContent = error.message;
                    } finally {
                        sending = false;
                    }
                } else {
                    statusText.textContent = `Acercate al punto ${activeIndex + 1} para verificarlo.`;
                }
            }, (error) => {
                statusText.textContent = 'No se pudo obtener tu ubicacion.';
            }, { enableHighAccuracy: true, maximumAge: 2000, timeout: 10000 });
        };

        renderCheckpointList();
        updateProgress(currentIndex);
        watchPosition();
    });
</script>
@endsection
