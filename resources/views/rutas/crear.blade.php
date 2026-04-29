@extends('layouts.plantillaHome')

@section('title', 'Crear ruta &middot; Moveet')

@section('content')
@php
    $oldGeojson = old('ruta_geojson');
@endphp

<div style="max-width: 1100px; margin: 0 auto; padding: 24px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: #1E2A28;">Crear ruta</h1>
            <p style="margin: 6px 0 0; color: #516260; font-weight: 600;">Dibuja la ruta haciendo clic en el mapa. La app genera el GeoJSON automaticamente.</p>
        </div>
        <a href="{{ route('rutas.index') }}" style="background: #8FA8A6; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700;">Volver</a>
    </div>

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #e4b8b8; color: #7b1f1f; padding: 12px 14px; border-radius: 8px; margin-bottom: 14px;">
            <ul style="margin: 0; padding-left: 16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('rutas.guardar') }}" id="ruta-form" style="background: #d0dbd9; border-radius: 16px; padding: 18px;">
        @csrf

        <div style="display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 18px; align-items: start;">
            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 4px;">Titulo</label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" required maxlength="150" placeholder="Ruta al mirador, paseo al parque..." style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 4px;">Descripcion</label>
                    <textarea name="descripcion" maxlength="1000" rows="3" placeholder="Describe el recorrido, puntos de interes, nivel recomendado..." style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">{{ old('descripcion') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Dificultad</label>
                        <select name="dificultad" required style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                            <option value="facil" {{ old('dificultad') === 'facil' ? 'selected' : '' }}>Facil</option>
                            <option value="media" {{ old('dificultad', 'media') === 'media' ? 'selected' : '' }}>Media</option>
                            <option value="dificil" {{ old('dificultad') === 'dificil' ? 'selected' : '' }}>Dificil</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Distancia (metros)</label>
                        <input type="number" name="distancia_metros" value="{{ old('distancia_metros', 1200) }}" required min="200" max="50000" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Puntos de recompensa</label>
                        <input type="number" name="puntos_recompensa" value="{{ old('puntos_recompensa', 75) }}" required min="20" max="500" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Nivel minimo requerido</label>
                        <input type="number" name="min_nivel" value="{{ old('min_nivel', 1) }}" required min="1" max="100" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                    </div>
                </div>

                <label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700;">
                    <input type="checkbox" name="premium_only" value="1" {{ old('premium_only') ? 'checked' : '' }}>
                    Solo premium
                </label>

                <div style="background: #eef4f3; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                    <div style="display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                        <div>
                            <h2 style="margin: 0; font-size: 1rem; color: #1E2A28;">Editor de ruta</h2>
                            <p style="margin: 4px 0 0; color: #5e6f6d; font-size: 13px;">Haz clic para a&ntilde;adir puntos. Puedes moverlos arrastrando.</p>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" id="route-location-btn" style="background: white; color: #1E2A28; border: 1px solid #c8d4d1; border-radius: 10px; padding: 9px 12px; font-weight: 700; cursor: pointer;">Mi ubicaci&oacute;n</button>
                            <button type="button" id="route-undo-btn" style="background: white; color: #1E2A28; border: 1px solid #c8d4d1; border-radius: 10px; padding: 9px 12px; font-weight: 700; cursor: pointer;">Deshacer</button>
                            <button type="button" id="route-clear-btn" style="background: white; color: #b00020; border: 1px solid #efc2c8; border-radius: 10px; padding: 9px 12px; font-weight: 700; cursor: pointer;">Limpiar</button>
                        </div>
                    </div>

                    <div id="route-map" style="height: 420px; border-radius: 14px; overflow: hidden; border: 1px solid #c8d4d1;"></div>
                    <div style="display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-top: 10px; font-size: 13px; color: #516260;">
                        <span id="route-status">A&ntilde;ade al menos 2 puntos para generar la ruta.</span>
                        <span id="route-distance">Distancia aproximada: 0 m</span>
                    </div>

                    <input type="hidden" name="ruta_geojson" id="ruta_geojson" value="{{ e($oldGeojson ?? '') }}">
                    <textarea id="ruta_geojson_preview" readonly rows="5" style="width: 100%; margin-top: 12px; border: 1px solid #c8d4d1; border-radius: 10px; padding: 10px; font-family: monospace; background: #f8fbfb; font-size: 12px;">{{ $oldGeojson ?: '{"type":"LineString","coordinates":[]}' }}</textarea>
                    <small style="display: block; margin-top: 6px; color: #5e6f6d;">Formato tecnico listo para guardar, sin editar a mano.</small>
                </div>
            </div>

            <div style="display: grid; gap: 12px; position: sticky; top: 18px;">
                <div style="background: #eef4f3; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                    <h2 style="margin: 0 0 10px; font-size: 1rem; color: #1E2A28;">Como funciona</h2>
                    <ul style="margin: 0; padding-left: 18px; color: #4f6461; font-size: 13px; line-height: 1.5;">
                        <li>Pulsa sobre el mapa para crear el recorrido.</li>
                        <li>Arrastra los puntos si quieres afinar la forma.</li>
                        <li>El GeoJSON se genera solo.</li>
                        <li>Se guarda como LineString, compatible con el motor de rutas.</li>
                    </ul>
                </div>

                <div style="background: #ffffff; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                    <h3 style="margin: 0 0 10px; font-size: 0.98rem; color: #1E2A28;">Sugerencia rapida</h3>
                    <p style="margin: 0; color: #5e6f6d; font-size: 13px; line-height: 1.5;">Haz rutas cortas y claras al principio. Una ruta bien explicada y con puntos visibles convierte mejor que una muy larga pero confusa.</p>
                </div>

                <button type="submit" id="route-submit" style="background: #8FA8A6; color: white; border: none; border-radius: 10px; padding: 13px 16px; font-weight: 800; cursor: pointer; font-size: 14px; opacity: 0.7;" disabled>Publicar ruta</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('route-map');
        const geojsonInput = document.getElementById('ruta_geojson');
        const preview = document.getElementById('ruta_geojson_preview');
        const status = document.getElementById('route-status');
        const distanceLabel = document.getElementById('route-distance');
        const submitBtn = document.getElementById('route-submit');
        const undoBtn = document.getElementById('route-undo-btn');
        const clearBtn = document.getElementById('route-clear-btn');
        const locationBtn = document.getElementById('route-location-btn');

        if (!mapEl || typeof L === 'undefined') {
            return;
        }

        const defaultCenter = [41.3874, 2.1686];
        const map = L.map('route-map').setView(defaultCenter, 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = [];
        const line = L.polyline([], { color: '#5d8f8d', weight: 5 }).addTo(map);

        const toGeoJSON = () => ({
            type: 'LineString',
            coordinates: markers.map((marker) => {
                const latlng = marker.getLatLng();
                return [Number(latlng.lng.toFixed(6)), Number(latlng.lat.toFixed(6))];
            }),
        });

        const updateUI = () => {
            const geojson = toGeoJSON();
            const hasEnoughPoints = markers.length >= 2;
            const distance = Math.round(line.getLatLngs().slice(1).reduce((sum, point, index) => {
                const prev = line.getLatLngs()[index];
                return sum + map.distance(prev, point);
            }, 0));

            const value = JSON.stringify({
                type: 'Feature',
                geometry: geojson,
                properties: {
                    generated_at: new Date().toISOString(),
                    points: markers.length,
                    distance_meters: distance,
                },
            }, null, 2);

            geojsonInput.value = value;
            preview.value = value;
            status.textContent = hasEnoughPoints
                ? `Ruta lista con ${markers.length} puntos.`
                : 'Añade al menos 2 puntos para generar la ruta.';
            distanceLabel.textContent = `Distancia aproximada: ${distance} m`;
            submitBtn.disabled = !hasEnoughPoints;
            submitBtn.style.opacity = hasEnoughPoints ? '1' : '0.7';
        };

        const rebuildLine = () => {
            line.setLatLngs(markers.map((marker) => marker.getLatLng()));
            updateUI();
        };

        const addMarker = (latlng) => {
            const marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', rebuildLine);
            marker.on('click', () => {
                status.textContent = 'Arrastra o usa Deshacer para corregir la ruta.';
            });
            markers.push(marker);
            rebuildLine();
        };

        const clearMarkers = () => {
            markers.splice(0).forEach((marker) => map.removeLayer(marker));
            line.setLatLngs([]);
            updateUI();
        };

        const loadExisting = () => {
            const raw = geojsonInput.value || preview.value || '';
            if (!raw) {
                updateUI();
                return;
            }

            try {
                const parsed = JSON.parse(raw);
                const coords = parsed?.geometry?.coordinates || parsed?.coordinates || [];

                if (!Array.isArray(coords) || coords.length === 0) {
                    updateUI();
                    return;
                }

                coords.forEach(([lng, lat]) => addMarker([lat, lng]));
                if (markers.length > 0) {
                    map.setView(markers[0].getLatLng(), 14);
                }
            } catch (error) {
                updateUI();
            }
        };

        map.on('click', (event) => addMarker(event.latlng));

        undoBtn.addEventListener('click', () => {
            const marker = markers.pop();
            if (marker) {
                map.removeLayer(marker);
            }
            rebuildLine();
        });

        clearBtn.addEventListener('click', clearMarkers);

        locationBtn.addEventListener('click', () => {
            if (!navigator.geolocation) {
                status.textContent = 'Tu navegador no soporta geolocalizacion.';
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                const latlng = [position.coords.latitude, position.coords.longitude];
                map.setView(latlng, 16);
                addMarker(latlng);
                status.textContent = 'Ubicacion añadida como primer punto de la ruta.';
            }, () => {
                status.textContent = 'No se pudo obtener tu ubicacion.';
            }, { enableHighAccuracy: true, timeout: 8000 });
        });

        loadExisting();
        updateUI();
    });
</script>
@endsection
