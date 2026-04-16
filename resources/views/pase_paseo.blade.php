@extends('layouts.plantillaHome')

@section('title', 'Pase de Paseo · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/pase_paseo.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/pase_paseo.js') }}"></script>
@endpush

@section('content')
<div class="battlepass-wrapper">
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
                        <div class="reward-card {{ $esPremium ? 'js-reclamar' : '' }}"
                             data-id="{{ $data['premium']->id }}"
                             data-nombre="{{ $data['premium']->nombre }}"
                             data-reclamable="{{ $esPremium && $nivelUsuario >= $nivel && !in_array($data['premium']->id, $reclamadas) ? 'true' : 'false' }}">
                            
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
</div>

{{-- Inyectar URL de reclamación para el JS --}}
<script>
    window.battlepassRoutes = {
        reclamar: "{{ route('pase.reclamar', ['recompensa' => ':id']) }}"
    };
</script>
@endsection
