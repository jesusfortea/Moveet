@extends('layouts.plantillaHome')

@section('title', 'Pase de Paseo · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pase_paseo.css') }}">
@endpush

@push('scripts')
<script>
    // El pase de paseo gestiona su propio scroll horizontal.
    // Inyectamos estilos al final del DOM para ganar la cascada CSS.
    (function () {
        var style = document.createElement('style');
        style.textContent = [
            'html, body { overflow: hidden !important; }',
            '.page-content { overflow: hidden !important; height: calc(100vh - 15vh) !important; padding: 0 !important; display: block !important; }',
            '.battlepass-wrapper { height: calc(100vh - 15vh) !important; overflow: hidden !important; }'
        ].join(' ');
        document.head.appendChild(style);
    })();
</script>
<script src="{{ asset('js/pase_paseo.js') }}"></script>
@endpush


@section('content')
<div class="battlepass-wrapper">
    @if (session('status'))
        <div style="max-width: 980px; margin: 10px auto 16px; background: #e8f7ed; border: 1px solid #9ed1ad; border-radius: 8px; padding: 10px 12px; font-weight: 700; color: #2e6e3e;">
            {{ session('status') }}
        </div>
    @endif

    @if(!$pase)
        <div style="max-width: 980px; margin: 20px auto; background: #f5f7f7; border: 1px solid #d5dddd; border-radius: 12px; padding: 20px; text-align: center;">
            <h2 style="margin: 0 0 8px; font-size: 1.4rem;">Pase de paseo no disponible</h2>
            <p style="margin: 0; color: #556563;">Todavia no hay un pase activo. Vuelve mas tarde o contacta con administracion.</p>
        </div>
    @elseif(empty($niveles))
        <div style="max-width: 980px; margin: 20px auto; background: #f5f7f7; border: 1px solid #d5dddd; border-radius: 12px; padding: 20px; text-align: center;">
            <h2 style="margin: 0 0 8px; font-size: 1.4rem;">Pase sin recompensas configuradas</h2>
            <p style="margin: 0; color: #556563;">Existe un pase activo, pero no tiene recompensas asociadas. Configuralas desde el panel de administracion.</p>
        </div>
    @else
    <div class="battlepass-track-container">
        <div class="battlepass-track">
            
            <div class="battlepass-sidebar">
                <div class="label-section free-label">
                    <span>GRATIS</span>
                </div>
                <div class="label-section-divider"></div>
                <div class="label-section paid-label">
                    <span>DE PAGO</span>
                    <a href="{{ route('suscripcion') }}" class="subscribe-btn">Suscribirse</a>
                </div>
            </div>

            @foreach($niveles as $nivel => $data)
            <div class="level-column {{ $nivel == $nivelUsuario ? 'current-level' : '' }}">
                
                {{-- Recompensa GRATIS --}}
                <div class="reward-slot free-slot 
                    {{ $nivelUsuario < $nivel ? 'locked' : '' }} 
                    {{ $data['gratis'] && in_array($data['gratis']->id, $reclamadas) ? 'claimed' : '' }}">
                    
                    @if($data['gratis'])
                        <div class="reward-card js-reclamar" 
                             data-id="{{ $data['gratis']->id }}"
                             data-nombre="{{ $data['gratis']->nombre }}"
                             data-reclamable="{{ $nivelUsuario >= $nivel && !in_array($data['gratis']->id, $reclamadas) ? 'true' : 'false' }}">
                            
                            <img src="{{ asset($data['gratis']->ruta_imagen) }}" alt="{{ $data['gratis']->nombre }}" onerror="this.src='https://placehold.co/100x100?text=Item'">
                            <span class="reward-name">{{ $data['gratis']->nombre }}</span>

                            @if(in_array($data['gratis']->id, $reclamadas))
                                <div class="claimed-badge">✅ Reclamado</div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="level-indicator">
                    <div class="level-circle {{ $nivelUsuario >= $nivel ? 'completed' : '' }}">
                        {{ $nivel }}
                    </div>
                </div>

                {{-- Recompensa DE PAGO --}}
                <div class="reward-slot paid-slot 
                    {{ $nivelUsuario < $nivel ? 'locked' : '' }}
                    {{ $data['premium'] && in_array($data['premium']->id, $reclamadas) ? 'claimed' : '' }}">
                    
                    @if($data['premium'])
                        <div class="reward-card js-reclamar"
                             data-id="{{ $data['premium']->id }}"
                             data-nombre="{{ $data['premium']->nombre }}"
                             data-reclamable="{{ $esPremium && $nivelUsuario >= $nivel && !in_array($data['premium']->id, $reclamadas) ? 'true' : 'false' }}"
                             data-is-premium="true">
                            
                            <img src="{{ asset($data['premium']->ruta_imagen) }}" alt="{{ $data['premium']->nombre }}" onerror="this.src='https://placehold.co/100x100?text=Premium'">
                            <span class="reward-name">{{ $data['premium']->nombre }}</span>

                            @if(in_array($data['premium']->id, $reclamadas))
                                <div class="claimed-badge">✅ Reclamado</div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
            @endforeach

        </div>
    </div>
    @endif
</div>

{{-- Inyectar URL de reclamación para el JS --}}
@if($pase && !empty($niveles))
    <script>
        window.battlepassRoutes = {
            reclamar: "{{ route('pase.reclamar', ['recompensa' => ':id']) }}"
        };
    </script>
@endif
@endsection
