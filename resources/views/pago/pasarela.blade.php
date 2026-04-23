@extends('layouts.plantillaHome')

@section('title', 'Pasarela de Pago · Moveet')

@push('styles')
<style>
    .payment-layout {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 80px); /* Ajuste según nav */
        padding: 2rem;
        background-color: var(--fondo);
    }
    
    .payment-card {
        background: var(--fondo-paneles);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 500px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .sandbox-badge {
        position: absolute;
        top: 20px;
        right: -35px;
        background: #FF9800;
        color: white;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 5px 40px;
        transform: rotate(45deg);
        letter-spacing: 1px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .payment-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .payment-header h1 {
        font-size: 1.5rem;
        color: var(--texto-principal);
        margin-bottom: 0.5rem;
    }

    .payment-header p {
        color: var(--texto-secundario);
        font-size: 0.95rem;
    }

    .payment-amount {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primario);
        margin: 1rem 0;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: flex;
        gap: 1rem;
    }
    .form-row .form-group {
        flex: 1;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--texto-secundario);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.85rem 1rem;
        border: 2px solid #E2E8F0;
        border-radius: 12px;
        font-size: 1rem;
        font-family: inherit;
        background-color: #F8FAFC;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primario);
        background-color: white;
        box-shadow: 0 0 0 4px rgba(86, 196, 112, 0.1);
    }

    .btn-pay {
        width: 100%;
        padding: 1rem;
        background-color: var(--primario);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-pay:hover {
        background-color: var(--primario-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(86, 196, 112, 0.3);
    }

    .error-msg {
        color: #E06060;
        font-size: 0.8rem;
        margin-top: 0.4rem;
        display: block;
    }

    .sandbox-warning {
        background: rgba(255, 152, 0, 0.1);
        border-left: 4px solid #FF9800;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        font-size: 0.9rem;
        color: #B26A00;
    }

    .sandbox-warning strong {
        display: block;
        margin-bottom: 0.25rem;
        color: #E65100;
    }
</style>
@endpush

@section('content')
<div class="payment-layout">
    <div class="payment-card">
        <div class="sandbox-badge">SANDBOX</div>

        <div class="payment-header">
            <h1>Pago Seguro</h1>
            <p>{{ $producto['nombre'] ?? 'Producto' }}</p>
            <div class="payment-amount">{{ number_format($producto['precio'] ?? 0, 2, ',', '.') }} €</div>
        </div>

        <div class="sandbox-warning">
            <strong>⚠️ Modo de Prueba (Sandbox)</strong>
            El cargo se realizará a la tarjeta seleccionada (modo de prueba).
        </div>

        <form action="{{ route('pago.procesar') }}" method="POST" id="payment-form">
            @csrf
            
            @if($tarjetas->isEmpty())
                <div style="text-align: center; margin: 2rem 0;">
                    <p style="color: var(--texto-secundario); margin-bottom: 1rem;">No tienes tarjetas guardadas.</p>
                    <a href="{{ route('suscripcion') }}" class="btn-pay" style="text-decoration: none; display: inline-block;">Ir a añadir tarjeta</a>
                </div>
            @else
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" style="margin-bottom: 1rem;">Selecciona tu tarjeta:</label>
                    
                    @foreach($tarjetas as $t)
                        <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid #E2E8F0; border-radius: 12px; margin-bottom: 0.5rem; cursor: pointer; transition: all 0.3s ease;">
                            <input type="radio" name="tarjeta_id" value="{{ $t->id }}" {{ $loop->first ? 'checked' : '' }} style="margin-right: 1rem; transform: scale(1.2);">
                            <div>
                                <div style="font-weight: 600; color: var(--texto-principal);">{{ $t->titular }}</div>
                                <div style="font-size: 0.9rem; color: var(--texto-secundario);">{{ $t->numero_enmascarado }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
                
                @error('tarjeta_id') <span class="error-msg" style="margin-bottom: 1rem;">{{ $message }}</span> @enderror

                <button type="submit" class="btn-pay" id="btn-submit">
                    <span>Pagar {{ number_format($producto['precio'] ?? 0, 2, ',', '.') }} €</span>
                    <span style="font-size: 1.2rem;">🔒</span>
                </button>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Evitar doble envío
    const form = document.getElementById('payment-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = document.getElementById('btn-submit');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span>Procesando...</span> <div class="spinner-border spinner-border-sm" role="status"></div>';
            }
        });
    }
</script>
@endpush
@endsection
