@extends('layouts.plantillaHome')

@section('title', 'Inicio · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/home.js') }}"></script>
@endpush

@section('content')

{{-- Inyectar datos de misiones --}}
<script>
    window.misionesData = @json($misiones);
    window.misionesReset = {
        diarias: @json($fechaLimiteDiarias),
        semanales: @json($fechaLimiteSemanales),
    };
</script>

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

            <h2 class="missions-title">Misiones</h2>

            {{-- Tabs --}}
            <div class="missions-tabs" role="tablist" aria-label="Tipo de misión">
                <button class="tab-btn active"
                        role="tab"
                        aria-selected="true"
                        data-tab="diarias">
                    Diarias
                </button>
                <button class="tab-btn"
                        role="tab"
                        aria-selected="false"
                        data-tab="semanales">
                    Semanales
                </button>
            </div>

            {{-- Botón cambiar misiones --}}
            <button class="change-missions-btn" id="change-missions-btn" title="Cambiar misiones por 2,99€">
                <span class="btn-icon">↻</span>
                <span>Cambiar misiones</span>
                <span class="btn-price">2,99 €</span>
            </button>

        </div>

        <div class="panel-divider"></div>

        {{-- Lista de misiones --}}
        <div class="missions-list" id="missions-list" role="list">
            {{-- Renderizado dinámico vía JS --}}
        </div>

        {{-- Pie: temporizador --}}
        <div class="missions-footer">
            <div class="timer-row">
                <span class="timer-label">Se cambian en…</span>
                <span class="timer-countdown" id="timer-countdown">--:--:--</span>
            </div>
            <div class="timer-bar">
                <div class="timer-bar-fill" id="timer-bar-fill" style="width: 0%"></div>
            </div>
        </div>

    </div>

</div>

@endsection