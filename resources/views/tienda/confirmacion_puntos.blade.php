@extends('layouts.plantillaHome')

@section('title', 'Tienda - Confirmacion puntos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    <a class="store-back" href="{{ route('tienda.puntos') }}">&lt; Volver</a>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <section class="store-card-confirm">
        <h1 class="store-confirm-title">¿Confirmas la compra de puntos?</h1>

        <div class="store-confirm-grid">
            <div class="store-img-box">
                <img src="{{ asset($pack->ruta_imagen ?: 'img/Moneda.png') }}" alt="{{ $pack->nombre }}">
            </div>

            <div>
                <p><strong>{{ $pack->nombre }}</strong></p>
                <p class="store-desc">Añadiras {{ number_format((int) $pack->puntos, 0, ',', '.') }} puntos a tu cuenta.</p>
                <p><strong>Precio:</strong> {{ number_format((float) $pack->precio_euros, 2, ',', '.') }}€</p>

                <div class="store-actions">
                    <form method="POST" action="{{ route('tienda.puntos.comprar', ['packPuntos' => $pack->id]) }}">
                        @csrf
                        <button type="submit" class="store-btn">Si, comprar</button>
                    </form>
                    <a href="{{ route('tienda.puntos') }}" class="store-btn-secondary">No</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
