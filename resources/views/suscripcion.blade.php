@extends('layouts.plantillaHome')

@section('title', 'Suscripción · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/suscripcion.css') }}">
<style>
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        vertical-align: text-bottom;
        border: .25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
        color: var(--primario);
    }

    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }

    .btn-simulate {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
        width: 100%;
    }

    .btn-simulate:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
</style>
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
        {{-- Columna Izquierda: Información --}}
        <div class="subs-left">
            <div class="card-section">
                <div class="section-header">
                    <div class="header-main">
                        <span class="icon-card">💎</span>
                        <h2>Moveet Premium</h2>
                    </div>
                </div>
                
                <div class="section-content" style="display: block; padding: 2rem;">
                    <p style="color: #4a5568; line-height: 1.6; margin-bottom: 1.5rem;">
                        Únase a nuestra comunidad premium y desbloquee funciones exclusivas para mejorar su experiencia en Moveet.
                    </p>
                    
                    <div style="background: #ebf8ff; border: 1px solid #bee3f8; border-radius: 12px; padding: 1rem; color: #2b6cb0; font-size: 0.9rem;">
                        <strong><i class="fas fa-info-circle"></i> Pago Seguro:</strong> 
                        Operamos exclusivamente con PayPal para garantizar la máxima seguridad en sus transacciones.
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
                
                @if($esPremium)
                    <div style="text-align: center; padding: 1rem; background: #f0fff4; border: 1px solid #c6f6d5; color: #2f855a; border-radius: 12px; font-weight: 700;">
                        ¡Ya eres usuario Premium!
                    </div>
                @else
                    <div id="paypal-button-container" style="margin-top: 1rem;"></div>
                    
                    <button type="button" id="btn-simulate-dev" class="btn-simulate">
                        ⚡ Simular Pago Premium (Modo Dev)
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>

@if(!$esPremium)
@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR&vault=true"></script>
<script>
    const captureUrl = "{{ route('suscripcion.paypal.capturar') }}";
    const csrfToken = "{{ csrf_token() }}";

    function processSubscriptionSuccess(orderID) {
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div style="text-align:center; padding: 2rem; background: white; border-radius: 12px; border: 1px solid #edf2f7;">
                <div class="spinner-border" role="status"></div>
                <p style="margin-top:1rem; color:#4a5568; font-weight: 600;">Activando suscripción...</p>
            </div>
        `;

        fetch(captureUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ orderID: orderID })
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                window.location.href = res.redirect;
            } else {
                alert(res.message || "Hubo un error al activar la suscripción.");
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error de conexión.");
            location.reload();
        });
    }

    if (document.getElementById('paypal-button-container')) {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '19.99' },
                        description: 'Suscripción Premium Mensual - Moveet'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    processSubscriptionSuccess(data.orderID);
                });
            }
        }).render('#paypal-button-container');
    }

    document.getElementById('btn-simulate-dev').addEventListener('click', function() {
        if(confirm('¿Deseas simular el pago Premium exitoso?')) {
            processSubscriptionSuccess('SIMULATED_PREMIUM_ID');
        }
    });
</script>
@endpush
@endif
@endsection
