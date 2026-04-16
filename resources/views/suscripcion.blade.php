@extends('layouts.plantillaHome')

@section('title', 'Suscripción · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/suscripcion.css') }}">
@endpush

@section('content')
<div class="subs-wrapper">
    <div class="subs-header">
        <a href="{{ route('pase.paseo') }}" class="back-link">
            <span class="arrow">&lt;</span> Volver
        </a>
        <h1>Suscribirse</h1>
    </div>

    @if (session('success'))
        <div class="status-msg success-msg">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="status-msg error-msg">{{ session('error') }}</div>
    @endif

    <div class="subs-container">
        {{-- Columna Izquierda: Tarjetas --}}
        <div class="subs-left">
            
            {{-- Acordeón: Añadir Tarjeta --}}
            <div class="card-section">
                <div class="section-header">
                    <div class="header-main">
                        <span class="icon-plus">+</span>
                        <span class="icon-card">💳</span>
                        <h2>Añadir tarjeta de crédito</h2>
                    </div>
                    <span class="chevron">▼</span>
                </div>
                
                <div class="section-content">
                    @if ($errors->any())
                        <div class="error-list">
                            @foreach ($errors->all() as $error)
                                <p class="error-item">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('suscripcion.tarjeta.store') }}" method="POST" class="card-form">
                        @csrf
                        <div class="form-group full">
                            <label for="numero_tarjeta">Número de la tarjeta</label>
                            <input type="text" id="numero_tarjeta" name="numero_tarjeta" value="{{ old('numero_tarjeta') }}" placeholder="9999 9999 9999 9999" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label for="fecha_caducidad">Fecha de caducidad</label>
                                <input type="text" id="fecha_caducidad" name="fecha_caducidad" value="{{ old('fecha_caducidad') }}" placeholder="00/00" required>
                            </div>
                            <div class="form-group half">
                                <label for="codigo_seguridad">Código de seguridad</label>
                                <input type="text" id="codigo_seguridad" name="codigo_seguridad" value="{{ old('codigo_seguridad') }}" placeholder="999" required>
                            </div>
                        </div>

                        <div class="form-group full">
                            <label for="titular">Titular de la tarjeta</label>
                            <input type="text" id="titular" name="titular" value="{{ old('titular', Auth::user() ? Auth::user()->name : '') }}" placeholder="Rodolfo Martínez Rodallón" required>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Añadir tarjeta</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Acordeón: Seleccionar Tarjeta --}}
            <div class="card-section">
                <div class="section-header">
                    <div class="header-main">
                        <span class="icon-card">💳</span>
                        <h2>Seleccionar tarjeta de crédito</h2>
                    </div>
                </div>

                <div class="section-content">
                    <div class="cards-list">
                        @forelse($tarjetas as $t)
                            <label class="card-selection-item">
                                <input type="radio" name="selected_card_id" value="{{ $t->id }}" 
                                       form="payment-form" {{ $loop->first ? 'checked' : '' }}>
                                <div class="saved-card">
                                    <div class="card-info">
                                        <span class="card-holder">{{ $t->titular }}</span>
                                        <span class="card-number">{{ $t->numero_enmascarado }}</span>
                                    </div>
                                    <div class="selection-indicator"></div>
                                </div>
                            </label>
                        @empty
                            <p class="empty-msg">No tienes tarjetas guardadas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Columna Derecha: Resumen y Pago --}}
        <div class="subs-right">
            
            <div class="benefits-box">
                <h3>Ventajas</h3>
                <ul class="benefits-list">
                    <li>Acceso al pase de paseo</li>
                    <li>Acceso a nuestra newsletter</li>
                    <li>Potenciador de experiencia durante una semana</li>
                    <li>1 Cambiazo gratis/semana</li>
                    <li>Acceso a compras premium</li>
                </ul>
            </div>

            <div class="payment-summary">
                <div class="price-tag">19,99€/mes</div>
                
                <form id="payment-form" action="{{ route('suscripcion.comprar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tarjeta_id" id="tarjeta_id_input" value="{{ $tarjetas->first() ? $tarjetas->first()->id : '' }}">
                    <button type="submit" class="btn-primary btn-subs" {{ $tarjetas->isEmpty() ? 'disabled' : '' }}>
                        Suscribirse
                    </button>
                    @if($tarjetas->isEmpty())
                        <p class="hint-msg">Añade una tarjeta para continuar.</p>
                    @endif
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const radioButtons = document.querySelectorAll('input[name="selected_card_id"]');
    const hiddenInput = document.getElementById('tarjeta_id_input');

    radioButtons.forEach(radio => {
        radio.addEventListener('change', (e) => {
            hiddenInput.value = e.target.value;
        });
    });
});
</script>
@endsection
