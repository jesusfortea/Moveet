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
                    <div id="paypal-button-container" style="width: 100%;"></div>
                    <a href="{{ route('tienda.puntos') }}" class="store-btn-secondary" style="margin-top: 1rem; display: block; text-align: center;">Cancelar</a>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR&vault=true"></script>
<script>
    const captureUrl = "{{ route('tienda.puntos.paypal.capturar') }}";
    const csrfToken = "{{ csrf_token() }}";
    const packId = "{{ $pack->id }}";

    function processPaymentSuccess(orderID) {
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div style="text-align:center; padding: 2rem;">
                <p>Procesando compra de puntos...</p>
            </div>
        `;

        fetch(captureUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ 
                orderID: orderID,
                pack_id: packId
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success') {
                window.location.href = res.redirect;
            } else {
                alert(res.message || "Error al procesar el pago.");
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error de conexión.");
            location.reload();
        });
    }

    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '{{ $pack->precio_euros }}' },
                    description: 'Compra de {{ $pack->puntos }} puntos - Moveet'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                processPaymentSuccess(data.orderID);
            });
        }
    }).render('#paypal-button-container');
</script>
@endpush
@endsection
