@extends('layouts.plantillaHome')

@section('title', 'Tienda - Puntos comprados · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    <a class="store-back" href="{{ route('tienda.puntos') }}">&lt; Volver a puntos</a>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <section class="store-card-buy">
        <h1 class="store-buy-title">¡Puntos comprados!</h1>
        <p>Tu saldo ha sido actualizado correctamente.</p>

        <div class="store-confirm-grid">
            <div class="store-img-box">
                <img src="{{ asset($pack->ruta_imagen ?: 'img/Moneda.png') }}" alt="{{ $pack->nombre }}">
            </div>

            <div>
                <p><strong>{{ $pack->nombre }}</strong></p>
                <p class="store-desc">Se han añadido {{ number_format((int) $pack->puntos, 0, ',', '.') }} puntos a tu cuenta.</p>
                <p><strong>Importe:</strong> {{ number_format((float) $pack->precio_euros, 2, ',', '.') }}€</p>
            </div>
        </div>
    </section>
</div>
@endsection
