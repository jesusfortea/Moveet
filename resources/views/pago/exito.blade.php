@extends('layouts.plantillaHome')

@section('title', 'Pago Completado · Moveet')

@push('styles')
<style>
    .success-layout {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 80px);
        padding: 2rem;
        background-color: var(--fondo);
    }
    
    .success-card {
        background: var(--fondo-paneles);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 500px;
        padding: 3rem 2.5rem;
        text-align: center;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background-color: #56C470;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 2.5rem;
        margin: 0 auto 2rem auto;
        box-shadow: 0 10px 20px rgba(86, 196, 112, 0.3);
        animation: pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes pop {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .success-title {
        font-size: 1.8rem;
        color: var(--texto-principal);
        margin-bottom: 1rem;
        font-weight: 800;
    }

    .success-text {
        color: var(--texto-secundario);
        font-size: 1.05rem;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    .factura-box {
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .factura-box p {
        margin: 0 0 0.5rem 0;
        color: var(--texto-secundario);
        font-size: 0.95rem;
    }

    .factura-box strong {
        color: var(--texto-principal);
    }

    .success-actions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1rem;
    }

    .btn-home {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: 1.1rem;
        background: linear-gradient(135deg, #56C470 0%, #3DA656 100%);
        color: white !important;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(86, 196, 112, 0.3);
    }


    .btn-download {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1rem;
        background-color: #F1F5F9;
        color: #475569 !important;
        border: 2px solid #E2E8F0;
        border-radius: 16px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        background-color: #E2E8F0;
        border-color: #CBD5E1;
        color: #1E293B !important;
    }

    .btn-download span {
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="success-layout">
    <div class="success-card">
        <div class="success-icon">✓</div>
        
        <h1 class="success-title">¡Pago Completado!</h1>
        <p class="success-text">Tus misiones han sido renovadas exitosamente. Te hemos enviado un correo con el comprobante.</p>

        <div class="factura-box">
            <p>Importe pagado: <strong>{{ number_format($factura->importe, 2, ',', '.') }} €</strong></p>
            <p>Tarjeta terminada en: <strong>**** {{ $factura->ultimos_digitos }}</strong></p>
            <p>Fecha: <strong>{{ $factura->created_at->format('d/m/Y H:i') }}</strong></p>
        </div>

        <div class="success-actions">
            <a href="{{ route('pago.descargar', $factura->id) }}" class="btn-download">
                <span>📄</span> Descargar Factura PDF
            </a>

            <a href="{{ route('home') }}" class="btn-home">
                Volver a mis misiones
            </a>
        </div>
    </div>
</div>
@endsection
