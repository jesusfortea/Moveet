@extends('layouts.plantillaHome')

@section('title', 'Tienda - Confirmacion · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}">
@endpush

@section('content')
<div class="store-page">
    @php
        $bloqueadoPremium = $articulo->premium && !$esPremium;
    @endphp

    <a class="store-back" href="{{ route('tienda.articulo', ['recompensa' => $articulo->id]) }}">&lt; Volver</a>

    @if (session('status'))
        <div class="store-status">{{ session('status') }}</div>
    @endif

    <section class="store-card-confirm">
        <h1 class="store-confirm-title">¿Estas seguro de comprar este articulo?</h1>

        <div class="store-confirm-grid">
            <div class="store-img-box">
                <img src="{{ asset($articulo->ruta_imagen) }}" alt="{{ $articulo->nombre }}">
            </div>

            <div>
                <p><strong>{{ $articulo->nombre }}</strong></p>
                <p class="store-desc">{{ $articulo->descripcion }}</p>
                <p><strong>Coste:</strong> {{ number_format((int) $articulo->puntos_necesarios, 0, ',', '.') }} puntos</p>
                @if ($articulo->premium)
                    <p class="store-premium-note">
                        Este articulo solo se puede comprar con pase de paseo premium.
                    </p>
                @endif

                <div class="store-actions">
                    @if ($bloqueadoPremium)
                        <span class="store-btn store-btn-disabled" aria-disabled="true">Requiere premium</span>
                    @else
                        <form method="POST" action="{{ route('tienda.comprar', ['recompensa' => $articulo->id]) }}">
                            @csrf
                            <button type="submit" class="store-btn">Si</button>
                        </form>
                    @endif
                    <a href="{{ route('tienda.index') }}" class="store-btn-secondary">No</a>
                </div>

                <ol class="store-help">
                    <li>Abre la aplicacion correspondiente.</li>
                    <li>Toca el icono de tu perfil.</li>
                    <li>Selecciona pagos y suscripciones.</li>
                    <li>Pulsa en canjear codigo.</li>
                    <li>Introduce el codigo de tu compra.</li>
                </ol>
            </div>
        </div>
    </section>
</div>
@endsection
