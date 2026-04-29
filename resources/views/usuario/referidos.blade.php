@extends('layouts.plantillaHome')

@section('title', 'Mis Referidos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    <h1 class="usuario-page-title">Mis referidos</h1>
    <a class="volver-link" href="{{ route('usuario.index') }}">&larr; Volver al perfil</a>

    <article class="panel-card" style="margin-bottom: 16px;">
        <div class="panel-header">
            <h2>Resumen de referidos</h2>
        </div>

        <div class="tarjeta-row">
            <span>Tu codigo de referido</span>
            <strong id="referral-code-text">{{ $usuario->referral_code ?? 'No disponible' }}</strong>
        </div>
        @if($usuario->referral_code)
            <div style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                <button
                    type="button"
                    id="copy-referral-code-btn"
                    class="btn-main"
                    style="height: 32px;"
                >
                    Copiar codigo
                </button>
                <small id="copy-referral-feedback" style="font-weight: 700; color: #3a4b49;"></small>
            </div>
        @endif
        <div class="tarjeta-row" style="margin-top: 8px;">
            <span>Total referidos</span>
            <strong>{{ $totales['total'] }}</strong>
        </div>
        <div class="tarjeta-row" style="margin-top: 8px;">
            <span>Referidos premiados</span>
            <strong>{{ $totales['premiados'] }}</strong>
        </div>
        <div class="tarjeta-row" style="margin-top: 8px;">
            <span>Referidos pendientes</span>
            <strong>{{ $totales['pendientes'] }}</strong>
        </div>
        <div class="tarjeta-row" style="margin-top: 8px;">
            <span>Puntos ganados por referidos</span>
            <strong>{{ number_format($totales['puntos_ganados'], 0, ',', '.') }}</strong>
        </div>
    </article>

    <article class="panel-card inventario-panel-full">
        <div class="panel-header">
            <h2>Listado de referidos</h2>
        </div>

        @if($referidos->count() > 0)
            <div style="display:grid; gap:10px;">
                @foreach($referidos as $ref)
                    <div class="tarjeta-row" style="padding: 10px 12px; border-radius: 10px;">
                        <span>
                            {{ $ref->referred?->name ?? 'Usuario eliminado' }}
                            @if($ref->referred?->email)
                                <small style="display:block; color:#5b6b69;">{{ $ref->referred->email }}</small>
                            @endif
                        </span>

                        <strong>
                            @if($ref->rewarded_at)
                                Premiado (+{{ $ref->reward_points }} ptos)
                            @else
                                Pendiente de primera mision
                            @endif
                        </strong>
                    </div>
                @endforeach
            </div>
        @else
            <p class="panel-empty panel-empty--center">Todavia no tienes referidos. Comparte tu codigo para empezar a ganar puntos.</p>
        @endif
    </article>
</div>

@if($usuario->referral_code)
<script>
    (function () {
        const copyBtn = document.getElementById('copy-referral-code-btn');
        const feedback = document.getElementById('copy-referral-feedback');
        const referralCode = @json($usuario->referral_code);

        if (!copyBtn || !feedback || !referralCode) {
            return;
        }

        copyBtn.addEventListener('click', async function () {
            feedback.textContent = '';

            try {
                await navigator.clipboard.writeText(referralCode);
                feedback.textContent = 'Codigo copiado.';
                feedback.style.color = '#257a3a';
            } catch (error) {
                feedback.textContent = 'No se pudo copiar. Copialo manualmente.';
                feedback.style.color = '#9c2f2f';
            }
        });
    })();
</script>
@endif
@endsection
