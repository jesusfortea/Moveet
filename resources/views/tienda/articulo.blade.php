@extends('layouts.plantillaHome')

@section('title', 'Articulo · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    @php
        $bloqueadoPremium = $articulo->premium && !$esPremium;
    @endphp

    <a class="store-back" href="{{ route('tienda.index') }}">&lt; Volver</a>

    <section class="store-card-confirm">
        <div class="store-confirm-grid">
            <div class="store-img-box">
                <img src="{{ asset($articulo->ruta_imagen) }}" alt="{{ $articulo->nombre }}">
            </div>

            <div>
                <h1 class="store-confirm-title">{{ $articulo->nombre }}</h1>
                <p class="store-desc">{{ $articulo->descripcion }}</p>
                <p><strong>Precio:</strong> {{ number_format((int) $articulo->puntos_necesarios, 0, ',', '.') }} puntos</p>
                @if ($articulo->premium)
                    <p class="store-premium-note">
                        Este articulo requiere pase de paseo premium.
                    </p>
                @endif

                <div class="store-actions">
                    @if ($bloqueadoPremium)
                        <span class="store-btn store-btn-disabled" aria-disabled="true">Requiere premium</span>
                    @else
                        <a href="{{ route('tienda.confirmacion', ['recompensa' => $articulo->id]) }}" class="store-btn">Ir a confirmacion</a>
                    @endif
                    <a href="{{ route('tienda.puntos') }}" class="store-btn-secondary">Comprar puntos</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
