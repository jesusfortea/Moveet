@extends('layouts.plantillaHome')

@section('title', 'Eventos · Moveet')

@section('content')

{{-- Inyectar datos de misiones y evento --}}
<script>
    window.misionesData = @json($misiones);
    window.misionesReset = {
        diarias: @json($fechaLimiteDiarias),
        semanales: @json($fechaLimiteSemanales),
    };
    window.eventoData = @json($evento);
    window.fechaFinEvento = @json($fechaFinEvento);
</script>

<div class="event-page">
    <section class="event-summary p-4 bg-white rounded-lg shadow-sm mb-4">
        @if($evento)
            <h1 class="text-2xl font-bold mb-2">{{ $evento['nombre'] }}</h1>
            <p class="text-base text-slate-700 mb-2">{{ $evento['descripcion'] }}</p>
            <p class="text-sm text-slate-500 mb-1"><strong>Fechas:</strong> {{ $evento['fecha_inicio'] }} — {{ $evento['fecha_fin'] }}</p>
            <p class="text-sm text-slate-500"><strong>Ubicación:</strong> {{ $evento['direccion'] }}</p>
        @else
            <h1 class="text-2xl font-bold mb-2">No hay eventos activos</h1>
            <p class="text-base text-slate-700">En este momento no se ha encontrado ningún evento activo. Vuelve más tarde.</p>
        @endif
    </section>

@if($evento)
    <div class="home-layout">

    {{-- ══════════════════════════════════════════════════════
         PANEL IZQUIERDO — Mapa Leaflet
    ══════════════════════════════════════════════════════ --}}
    <div class="map-panel">

        {{-- Loading overlay --}}
        <div class="map-loading" id="map-loading">
            <div class="spinner"></div>
            <p>Obteniendo tu ubicación...</p>
        </div>

        {{-- Mapa --}}
        <div id="map"></div>

        {{-- Botón de ubicación --}}
        <button class="location-btn" id="location-btn" title="Ir a mi ubicación">
            <span class="location-icon">📍</span>
        </button>

    </div>

    {{-- ══════════════════════════════════════════════════════
         PANEL DERECHO — Misiones
    ══════════════════════════════════════════════════════ --}}
    <div class="missions-panel">

        {{-- Cabecera --}}
        <div class="missions-header">

            <h2 class="missions-title">Misiones del Evento</h2>

        </div>

        <div class="panel-divider"></div>

        {{-- Lista de misiones --}}
        <div class="missions-list" id="missions-list" role="list">
            {{-- Renderizado dinámico vía JS --}}
        </div>

        {{-- Pie: temporizador --}}
        <div class="missions-footer">
            <div class="timer-row">
                <span class="timer-label">Evento activo hasta…</span>
                <span class="timer-countdown" id="timer-countdown">--:--:--</span>
            </div>
            <div class="timer-bar">
                <div class="timer-bar-fill" id="timer-bar-fill" style="width: 0%"></div>
            </div>
        </div>

    </div>

</div>
@endif

</div>

@endsection