@extends('layouts.plantillaHome')

@section('title', 'Eventos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<style>
    html,
    body {
        overflow: auto;
    }

    .page-content {
        height: auto;
        min-height: calc(100vh - var(--nav-h));
        overflow: auto;
    }

    .event-page {
        flex: 1 1 auto;
        min-height: calc(100vh - var(--nav-h));
        overflow: auto;
    }

    .event-page .home-layout {
        min-height: calc(100vh - var(--nav-h));
        overflow: visible;
    }

    @media (max-width: 900px) {
        .event-page {
            height: auto;
            min-height: calc(100vh - var(--nav-h));
            overflow: auto;
        }

        .event-page .home-layout {
            min-height: calc(100vh - var(--nav-h));
        }
    }
</style>
@endpush

@push('scripts')
<script>
    window.misionesData = @json($misiones);
    window.eventoData = @json($evento);
    window.fechaFinEvento = @json($fechaFinEvento);
</script>
<script src="{{ asset('js/eventos.js') }}"></script>
@endpush

@section('content')
<div class="event-page flex flex-col">
    @if($evento)
        <div class="home-layout">
            <div class="map-panel">
                <div class="map-loading" id="map-loading">
                    <div class="spinner"></div>
                    <p>Obteniendo tu ubicaci&oacute;n...</p>
                </div>
                <div id="map"></div>
                <button class="location-btn" id="location-btn" title="Ir a mi ubicaci&oacute;n">
                    <span class="location-icon">📍</span>
                </button>
            </div>

            <div class="missions-panel">
                <div class="missions-header">
                    <div class="event-header-title">
                        <span class="event-label">Evento</span>
                        <h2 class="missions-title">{{ $evento['nombre'] }}</h2>
                        <p class="event-subtitle">{{ $evento['descripcion'] }}</p>
                    </div>
                    <div class="event-meta">
                        <span><strong>Fechas:</strong> {{ $evento['fecha_inicio'] }} &mdash; {{ $evento['fecha_fin'] }}</span>
                        <span><strong>Ubicaci&oacute;n:</strong> {{ $evento['direccion'] }}</span>
                    </div>
                </div>

                <div class="panel-divider"></div>

                <div class="missions-list" id="missions-list" role="list">
                    @php
                        $ordenadas = collect($misiones)->sortBy('completada');
                    @endphp
                    @if($ordenadas->isEmpty())
                        <div class="missions-empty">
                            <div class="missions-empty__icon">🎯</div>
                            <p>El evento no tiene misiones disponibles.</p>
                        </div>
                    @else
                        @foreach($ordenadas as $m)
                            <div class="mission-card {{ $m['completada'] ? 'completed' : '' }}" data-id="{{ $m['id'] }}" role="listitem">
                                <div class="mission-card__check">{{ $m['completada'] ? '✓' : '' }}</div>
                                <div class="mission-card__body">
                                    <div class="mission-card__name">{{ $m['nombre'] }}</div>
                                    @if($m['premium'])
                                        <span class="mission-card__premium">⭐ Premium</span>
                                    @endif
                                    @if($m['direccion'])
                                        <div class="mission-card__sub direccion-sub">📍 {{ $m['direccion'] }}</div>
                                    @endif
                                    @if($m['ejeX'] != null && !$m['completada'])
                                        <div class="mission-card__sub route-hint">🗺️ Ruta activa · ac&eacute;rcate a 50 m</div>
                                    @endif
                                </div>
                                <div class="mission-card__points points-label">+{{ $m['puntos'] }} ptos</div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="missions-footer">
                    <div class="timer-row">
                        <span class="timer-label">Evento activo hasta...</span>
                        <span class="timer-countdown" id="timer-countdown">--:--:--</span>
                    </div>
                    <div class="timer-bar">
                        <div class="timer-bar-fill" id="timer-bar-fill" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <section class="event-summary p-8 rounded-3xl shadow-sm text-center bg-slate-50">
            <h1 class="text-3xl font-bold text-slate-900">No hay eventos activos</h1>
            <p class="mt-4 text-base text-slate-700">Ahora mismo no hay ning&uacute;n evento en curso. Vuelve m&aacute;s tarde para descubrir nuevas misiones exclusivas.</p>
            <a href="{{ route('home') }}" class="mt-8 inline-flex rounded-full bg-[#8FA8A6] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#7a968f]">Volver al inicio</a>
        </section>
    @endif
</div>
@endsection
