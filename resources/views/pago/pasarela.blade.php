@extends('layouts.plantillaHome')

@section('title', 'Pasarela de Pago · Moveet')

@push('styles')
<style>
    .pasarela-container {
        max-width: 600px;
        margin: 4rem auto;
        padding: 0 1rem;
    }

    .pasarela-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        padding: 3rem;
        border: 1px solid #edf2f7;
    }

    .pasarela-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .pasarela-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .pasarela-header p {
        color: #718096;
        font-size: 1.1rem;
    }

    .summary-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
    }

    .summary-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #a0aec0;
        margin-bottom: 1rem;
        letter-spacing: 0.05em;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        color: #4a5568;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px dashed #e2e8f0;
        font-weight: 800;
        font-size: 1.2rem;
        color: #1a202c;
    }

    .secure-payment {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
        padding: 1rem;
        border-radius: 12px;
        font-size: 0.9rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

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


</style>
@endpush

@section('content')
<div class="pasarela-container">
    <div class="pasarela-card">
        <div class="pasarela-header">
            <h1>Pasarela de Pago</h1>
            <p>Finalice su pedido de forma segura</p>
        </div>

        <div class="summary-box">
            <h3 class="summary-title">Resumen del pedido</h3>
            <div class="summary-item">
                <span>{{ $producto['nombre'] ?? 'Renovación de misiones' }}</span>
                <span>{{ number_format($producto['precio'] ?? 0, 2, ',', '.') }} €</span>
            </div>
            <div class="summary-total">
                <span>Total a pagar</span>
                <span>{{ number_format($producto['precio'] ?? 0, 2, ',', '.') }} €</span>
            </div>
        </div>

        <div class="secure-payment">
            <strong>🔒 Pago Seguro</strong>
            Sus datos están encriptados y procesados de forma segura por PayPal.
        </div>

        <div id="paypal-button-container" style="margin-top: 2rem;"></div>



        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('home') }}" style="color: #a0aec0; text-decoration: none; font-size: 0.9rem;">
                Cancelar y volver
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR&vault=true"></script>
<script>
    const captureUrl = "{{ route('pago.paypal.capturar.misiones') }}";
    const csrfToken = "{{ csrf_token() }}";

    function processPaymentSuccess(orderID) {
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div style="text-align:center; padding: 2rem; background: white; border-radius: 12px; border: 1px solid #edf2f7;">
                <div class="spinner-border" role="status"></div>
                <p style="margin-top:1rem; color:#4a5568; font-weight: 600;">Procesando pedido...</p>
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
                window.showAppAlert(res.message || "Hubo un error al procesar el pago.", 'error', 'Pago no completado');
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            window.showAppAlert("Error de conexión al procesar el pago.", 'error', 'Error de red');
            location.reload();
        });
    }

    if (document.getElementById('paypal-button-container')) {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '{{ $producto['precio'] ?? '0.99' }}' },
                        description: '{{ $producto['nombre'] ?? 'Renovación de misiones' }} - Moveet'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    processPaymentSuccess(data.orderID);
                });
            },
            onError: function(err) {
                console.error(err);
                window.showAppAlert("Hubo un error con la pasarela de PayPal.", 'error', 'Error de PayPal');
            }
        }).render('#paypal-button-container');
    }


</script>
@endpush
@endsection
