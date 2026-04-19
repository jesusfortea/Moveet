@extends('layouts.plantillaHome')

@section('title', 'Tienda · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    <h1 class="store-title">Tienda</h1>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <div class="store-grid">
        @foreach ($articulos as $articulo)
            <article class="store-card {{ $articulo->premium ? 'store-card-premium' : '' }}">
                <span class="store-price">{{ number_format((int) $articulo->puntos_necesarios, 0, ',', '.') }} ptos</span>
                <div class="store-img-box">
                    <img src="{{ asset($articulo->ruta_imagen) }}" alt="{{ $articulo->nombre }}">
                </div>
                <h2 class="store-name">{{ $articulo->nombre }}</h2>
                <p class="store-desc">{{ $articulo->descripcion }}</p>
                <div class="store-actions">
                    <a href="{{ route('tienda.articulo', ['recompensa' => $articulo->id]) }}" class="store-btn">Ver articulo</a>
                    <a href="{{ route('tienda.confirmacion', ['recompensa' => $articulo->id]) }}" class="store-btn-secondary">Comprar</a>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endsection
