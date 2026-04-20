@extends('layouts.plantillaHome')

@section('title', 'Compra de Puntos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="shop-page">
    <h1 class="shop-title">Compra de puntos</h1>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <div class="shop-grid">
        @foreach ($packs as $pack)
            <div class="shop-pack {{ $pack->destacado ? 'shop-pack--hero' : '' }}">
                @if($pack->destacado)
                    <div class="shop-hero-badge">Destacado</div>
                @endif
                <div class="shop-card">
                    <div class="shop-coins">
                        <img src="{{ asset($pack->ruta_imagen ?: 'img/Moneda.png') }}" class="shop-coin" alt="Moneda Moveet">
                        <div class="shop-price-rect">{{ number_format((float) $pack->precio_euros, 2, ',', '.') }}€</div>
                    </div>
                    <p class="shop-pts">+{{ number_format((int) $pack->puntos, 0, ',', '.') }} ptos</p>
                    <form method="GET" action="{{ route('tienda.puntos.confirmacion', ['packPuntos' => $pack->id]) }}" class="shop-form">
                        <button type="submit" class="shop-btn-sq">Comprar</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
