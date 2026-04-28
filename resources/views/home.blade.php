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
            @if(auth()->user()->free_mission_changes > 0)
                <form action="{{ route('misiones.renovar_gratis') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="change-missions-btn" style="background: #1E2A28; color: white;" title="Tienes {{ auth()->user()->free_mission_changes }} cambios gratuitos">
                        <span class="btn-icon">↻</span>
                        <span>¡Cambiazo GRATIS!</span>
                        <span class="btn-price" style="background: #C5D8D6; color: #1E2A28;">{{ auth()->user()->free_mission_changes }} disp.</span>
                    </button>
                </form>
            @else
                <button class="change-missions-btn" id="change-missions-btn" title="Cambiar misiones por 0,99€" onclick="window.location.href='{{ route('pago.pasarela') }}'">
                    <span class="btn-icon">↻</span>
                    <span>Cambiar misiones</span>
                    <span class="btn-price">0,99 €</span>
                </button>
            @endif

        </div>

        <div class="panel-divider"></div>

        {{-- Listas de misiones --}}
        <div class="missions-lists-container">
            {{-- Diarias --}}
            <div id="diarias-list" class="missions-list" role="list">
                @php
                    $diarias = collect($misiones)->where('semanal', false)->sortBy('completada');
                @endphp
                @if($diarias->isEmpty())
                    <div class="missions-empty">
                        <div class="missions-empty__icon">🎯</div>
                        <p>No hay misiones diarias disponibles.</p>
                    </div>
                @else
                    @foreach($diarias as $m)
                        <div class="mission-card {{ $m['completada'] ? 'completed' : '' }}" data-id="{{ $m['id'] }}" role="listitem">
                            <div class="mission-card__check">{{ $m['completada'] ? '✓' : '' }}</div>
                            <div class="mission-card__body">
                                <div class="mission-card__name">{{ $m['nombre'] }}</div>
                                @if($m['premium'])
                                    <span class="mission-card__premium">⭐ Premium</span>
                                @endif
                                @if($m['direccion'] && !$m['completada'])
                                    <div class="mission-card__sub direccion-sub">📍 {{ $m['direccion'] }}</div>
                                @endif
                            </div>
                            <div class="mission-card__points points-label">{{ $m['completada'] ? '+'.$m['puntos'].' ptos' : $m['puntos'].' ptos' }}</div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Semanales --}}
            <div id="semanales-list" class="missions-list" role="list" style="display: none;">
                @php
                    $semanales = collect($misiones)->where('semanal', true)->sortBy('completada');
                @endphp
                @if($semanales->isEmpty())
                    <div class="missions-empty">
                        <div class="missions-empty__icon">🎯</div>
                        <p>No hay misiones semanales disponibles.</p>
                    </div>
                @else
                    @foreach($semanales as $m)
                        <div class="mission-card {{ $m['completada'] ? 'completed' : '' }}" data-id="{{ $m['id'] }}" role="listitem">
                            <div class="mission-card__check">{{ $m['completada'] ? '✓' : '' }}</div>
                            <div class="mission-card__body">
                                <div class="mission-card__name">{{ $m['nombre'] }}</div>
                                @if($m['premium'])
                                    <span class="mission-card__premium">⭐ Premium</span>
                                @endif
                                @if($m['direccion'] && !$m['completada'])
                                    <div class="mission-card__sub direccion-sub">📍 {{ $m['direccion'] }}</div>
                                @endif
                            </div>
                            <div class="mission-card__points points-label">{{ $m['completada'] ? '+'.$m['puntos'].' ptos' : $m['puntos'].' ptos' }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
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