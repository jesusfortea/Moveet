@extends('layouts.plantillaHome')

@section('title', 'Compra de Puntos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda_puntos.css') }}">
@endpush

@section('content')

<div class="shop-page">
    <h1 class="shop-title">Compra de puntos</h1>

    <div class="shop-grid">

        {{-- Pack básico --}}
        <div class="shop-card">
            <span class="shop-price">4,99€</span>
            <div class="shop-coins">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin" alt="Moneda Moveet">
            </div>
            <p class="shop-pts">+400 ptos</p>
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="pack" value="400">
                <button type="submit" class="shop-btn">Comprar</button>
            </form>
        </div>

        {{-- Pack destacado --}}
        <div class="shop-card shop-card--featured">
            <span class="shop-badge">Destacado</span>
            <span class="shop-price">17,99€</span>
            <div class="shop-coins">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin shop-coin--back"  alt="Moneda Moveet">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin shop-coin--front" alt="Moneda Moveet">
            </div>
            <p class="shop-pts">+1.700 ptos</p>
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="pack" value="1700">
                <button type="submit" class="shop-btn">Comprar</button>
            </form>
        </div>

        {{-- Pack premium --}}
        <div class="shop-card">
            <span class="shop-price">34,99€</span>
            <div class="shop-coins">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin shop-coin--back2"  alt="Moneda Moveet">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin shop-coin--back"   alt="Moneda Moveet">
                <img src="{{ asset('img/Moneda.png') }}" class="shop-coin shop-coin--front"  alt="Moneda Moveet">
            </div>
            <p class="shop-pts">+3.000 ptos</p>
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="pack" value="3000">
                <button type="submit" class="shop-btn">Comprar</button>
            </form>
        </div>

    </div>
</div>

@endsection     </form>
            </div>
        </div>

    </div>
</div>

@endsection