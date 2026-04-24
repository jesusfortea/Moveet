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
                        <div style="display: flex; flex-direction: column; gap: 1rem; width: 100%;">
                            <form method="POST" action="{{ route('tienda.comprar', ['recompensa' => $articulo->id]) }}" style="width: 100%;">
                                @csrf
                                <button type="submit" class="store-btn" style="width: 100%;">Comprar con {{ number_format((int) $articulo->puntos_necesarios, 0, ',', '.') }} puntos</button>
                            </form>
                            
                            <div style="text-align: center; color: #718096; font-size: 0.9rem; margin: 0.5rem 0;">— O —</div>
                            
                            <div id="paypal-button-container" style="width: 100%;"></div>
                        </div>
                    @endif
                    <a href="{{ route('tienda.index') }}" class="store-btn-secondary" style="margin-top: 1rem; display: block; text-align: center;">Cancelar</a>
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
@push('scripts')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=EUR&vault=true"></script>
<script>
    const captureUrl = "{{ route('tienda.articulo.paypal.capturar', $articulo->id) }}";
    const csrfToken = "{{ csrf_token() }}";

    function processPaymentSuccess(orderID) {
        const container = document.getElementById('paypal-button-container');
        container.innerHTML = `
            <div style="text-align:center; padding: 2rem;">
                <p>Procesando compra...</p>
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

    if (document.getElementById('paypal-button-container')) {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '{{ number_format($articulo->puntos_necesarios / 100, 2, '.', '') }}' },
                        description: 'Compra de artículo: {{ $articulo->nombre }} - Moveet'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    processPaymentSuccess(data.orderID);
                });
            }
        }).render('#paypal-button-container');
    }
</script>
@endpush
@endsection
