@extends('layouts.plantillaHome')

@section('title', 'Tienda - Compra · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    <a class="store-back" href="{{ route('tienda.index') }}">&lt; Volver a tienda</a>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <section class="store-card-buy">
        <h1 class="store-buy-title">¡Articulo comprado!</h1>
        <p>Gracias por confiar en nosotros.</p>

        <div class="store-confirm-grid">
            <div class="store-img-box">
                <img src="{{ asset($articulo->ruta_imagen) }}" alt="{{ $articulo->nombre }}">
            </div>

            <div>
                <p><strong>{{ $articulo->nombre }}</strong></p>
                <p class="store-desc">Compra confirmada por {{ number_format((int) $articulo->puntos_necesarios, 0, ',', '.') }} puntos.</p>

                <ol class="store-help">
                    <li>Revisa la seccion de inventario en tu perfil.</li>
                    <li>Si el articulo incluye codigo, se mostrara ahi.</li>
                    <li>Canjealo siguiendo las instrucciones.</li>
                </ol>
            </div>
        </div>
    </section>
</div>
@endsection
