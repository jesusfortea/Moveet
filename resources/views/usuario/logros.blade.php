@extends('layouts.plantillaHome')

@section('title', 'Mis Logros · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    <h1 class="usuario-page-title">Mis logros</h1>
    <a class="volver-link" href="{{ route('usuario.index') }}">&larr; Volver al perfil</a>

    <article class="panel-card inventario-panel-full">
        <div class="inventario-topbar">
            <p class="inventario-count">Desbloqueados: {{ $logrosDesbloqueados->count() }} / {{ $logrosCatalogo->count() }}</p>
        </div>

        @if($logrosCatalogo->count() > 0)
            <div class="logros-grid">
                @foreach($logrosCatalogo as $logro)
                    @php
                        $desbloqueado = $logrosDesbloqueados->get($logro->id);
                    @endphp

                    <div class="inventario-item inventario-item--full" style="background: {{ $desbloqueado ? '#f8fafc' : '#eef2f1' }}; opacity: {{ $desbloqueado ? '1' : '0.85' }};">
                        <span class="cantidad">+{{ $logro->puntos_bonus }} ptos</span>

                        <div class="recompensa-visual recompensa-visual--large">
                            <span>{{ $logro->icono }}</span>
                        </div>

                        <div class="item-text-content">
                            <p>{{ $logro->nombre }}</p>

                            <div class="inventario-meta">
                                <span>{{ $logro->descripcion }}</span>
                                @if($desbloqueado)
                                    <span>Estado: Desbloqueado</span>
                                    <span>Fecha: {{ optional($desbloqueado->pivot->achieved_at)->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                @else
                                    <span>Estado: Bloqueado</span>
                                    <span>Completa sus requisitos para desbloquearlo.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="panel-empty panel-empty--center">No hay logros configurados en este momento.</p>
        @endif
    </article>
</div>
@endsection
