@extends('layouts.plantillaHome')

@section('title', 'Inventario &middot; Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    <div class="inventario-topbar">
        <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver</a>
        <h1 class="usuario-page-title usuario-page-title--center">Inventario</h1>
    </div>

    @if (session('status'))
        <div class="usuario-alert">{{ session('status') }}</div>
    @endif

    <div class="inventario-sections-container">
        {{-- SECCIÓN: DISPONIBLES --}}
        <section class="panel-card inventario-panel-full mb-8">
            <div class="panel-header">
                <h2>Objetos disponibles</h2>
                <span class="inventario-count">{{ $disponibles->count() }} objetos</span>
            </div>

            <div class="inventario-grid inventario-grid--full">
                @forelse ($disponibles as $item)
                    <article class="inventario-item inventario-item--full">
                        <span class="cantidad">x1</span>

                        <div class="recompensa-visual recompensa-visual--large">
                            @if ($item->recompensa?->ruta_imagen)
                                <img src="{{ asset($item->recompensa->ruta_imagen) }}" alt="{{ $item->recompensa->nombre }}">
                            @else
                                <span>🏅</span>
                            @endif
                        </div>

                        <div class="item-text-content">
                            <p>{{ $item->recompensa?->nombre ?? 'Recompensa desbloqueada' }}</p>
                            <div class="inventario-meta">
                                <span>{{ $item->origen ? ucfirst($item->origen) : 'Origen desconocido' }}</span>
                                <span>{{ optional($item->obtenida_at)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('usuario.inventario.usar', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-simple-outline">Canjear</button>
                        </form>
                    </article>
                @empty
                    <p class="panel-empty panel-empty--center">No tienes objetos disponibles para usar.</p>
                @endforelse
            </div>
        </section>

        {{-- SECCIÓN: USADAS --}}
        @if($usados->isNotEmpty())
            <section class="panel-card inventario-panel-full inventario-panel--used">
                <div class="panel-header">
                    <h2 class="text-gray-500">Historial de uso</h2>
                    <span class="inventario-count text-gray-400">{{ $usados->count() }} canjeados</span>
                </div>

                <div class="inventario-grid inventario-grid--full">
                    @foreach ($usados as $item)
                        <article class="inventario-item inventario-item--full inventario-item--used">
                            <div class="recompensa-visual recompensa-visual--small">
                                @if ($item->recompensa?->ruta_imagen)
                                    <img src="{{ asset($item->recompensa->ruta_imagen) }}" alt="{{ $item->recompensa->nombre }}" style="filter: grayscale(1); opacity: 0.6;">
                                @else
                                    <span style="opacity: 0.5;">🏅</span>
                                @endif
                            </div>

                            <div class="item-text-content">
                                <p class="text-gray-500">{{ $item->recompensa?->nombre ?? 'Recompensa usada' }}</p>
                                <div class="inventario-meta">
                                    <span class="badge-used">Canjeado el {{ optional($item->usado_at)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
