@extends('layouts.plantillaHome')

@section('title', 'Compra de Puntos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    <h1 class="store-title">Compra de puntos</h1>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <div class="points-grid">
        @foreach ($packs as $pack)
            <article class="points-card {{ $pack->destacado ? 'featured' : '' }}">
                <span class="store-price">{{ number_format((float) $pack->precio_euros, 2, ',', '.') }}€</span>
                <img src="{{ asset($pack->ruta_imagen ?: 'img/Moneda.png') }}" class="points-coin" alt="Moneda Moveet">
                <p class="points-amount">+{{ number_format((int) $pack->puntos, 0, ',', '.') }} ptos</p>
                <a href="{{ route('tienda.puntos.confirmacion', ['packPuntos' => $pack->id]) }}" class="store-btn">Comprar</a>
            </article>
        @endforeach
    </div>
</div>
@endsection
