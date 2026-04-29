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

    <section class="panel-card inventario-panel-full">
        <div class="panel-header">
            <h2>Tus recompensas</h2>
            <span class="inventario-count">{{ $inventario->count() }} objetos</span>
        </div>

        <div class="inventario-grid inventario-grid--full">
            @forelse ($inventario as $item)
                <article class="inventario-item inventario-item--full">
                    <span class="cantidad">x1</span>

                    <div class="recompensa-visual recompensa-visual--large">
                        @if ($item->recompensa?->ruta_imagen)
                            @php
                                $recompensaUrl = \Illuminate\Support\Str::startsWith($item->recompensa->ruta_imagen, ['http://', 'https://'])
                                    ? $item->recompensa->ruta_imagen
                                    : (\Illuminate\Support\Str::startsWith($item->recompensa->ruta_imagen, 'storage/')
                                        ? asset($item->recompensa->ruta_imagen)
                                        : asset($item->recompensa->ruta_imagen));
                            @endphp
                            <img src="{{ $recompensaUrl }}" alt="{{ $item->recompensa->nombre }}">
                        @else
                            <span>🏅</span>
                        @endif
                    </div>

                    <p>{{ $item->recompensa?->nombre ?? 'Recompensa desbloqueada' }}</p>

                    <div class="inventario-meta">
                        <span>{{ $item->origen ? ucfirst($item->origen) : 'Origen desconocido' }}</span>
                        <span>{{ optional($item->obtenida_at)->format('d/m/Y') }}</span>
                    </div>

                    <button type="button" class="btn-main btn-small" disabled>Usar</button>
                </article>
            @empty
                <p class="panel-empty panel-empty--center">Todavia no tienes objetos en inventario.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
