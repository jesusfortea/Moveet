@extends('layouts.plantillaHome')

@section('title', 'Rutas de la comunidad · Moveet')

@push('styles')
<style>
    .rutas-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 32px 24px;
        font-family: 'Outfit', sans-serif;
    }
    .rutas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .rutas-header h1 {
        font-size: 2rem;
        font-weight: 900;
        margin: 0;
        color: #1E2A28;
    }
    .rutas-crear-btn {
        background: linear-gradient(135deg, #8FA8A6, #5B7C7A);
        color: white;
        padding: 12px 24px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .rutas-crear-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(91,124,122,0.3);
    }
    .rutas-lock-msg {
        font-size: 0.9rem;
        color: #586866;
        font-weight: 600;
        background: #f0f4f4;
        border: 1px solid #e2eaea;
        border-radius: 999px;
        padding: 10px 20px;
    }
    .rutas-alert {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 14px 18px;
        border-radius: 16px;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .mis-rutas-box {
        background: #eef4f3;
        border: 1px solid #d6e3e1;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 28px;
    }
    .mis-rutas-box h2 {
        margin: 0 0 14px;
        color: #1E2A28;
        font-size: 1.1rem;
        font-weight: 800;
    }
    .mis-rutas-item {
        background: white;
        border: 1px solid #dbe4e2;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .mis-rutas-item-info small {
        display: block;
        font-size: 0.8rem;
        color: #5d6f6d;
        margin-top: 4px;
    }
    .ruta-editar-btn {
        background: white;
        border: 2px solid #8FA8A6;
        color: #5B7C7A;
        padding: 8px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 800;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .ruta-editar-btn:hover {
        background: #8FA8A6;
        color: white;
    }
    .rutas-list {
        display: grid;
        gap: 16px;
    }
    .ruta-card {
        background: white;
        border: 1px solid #dbe4e2;
        border-radius: 20px;
        padding: 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .ruta-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(91,124,122,0.1);
    }
    .ruta-card-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .ruta-card-top h3 {
        margin: 0;
        font-size: 1.15rem;
        color: #1E2A28;
        font-weight: 800;
    }
    .ruta-card-top p {
        margin: 6px 0 0;
        color: #5d6f6d;
        font-size: 0.9rem;
    }
    .ruta-pts {
        font-weight: 900;
        color: #22C55E;
        font-size: 1.1rem;
        white-space: nowrap;
    }
    .ruta-desc {
        margin: 12px 0;
        color: #2c3b39;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    .ruta-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .ruta-stats {
        font-size: 0.85rem;
        color: #5d6f6d;
        font-weight: 600;
    }
    .ruta-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .ruta-badge-completada {
        background: #e9f6ee;
        border: 1px solid #b6ddc3;
        color: #1e613b;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 800;
    }
    .ruta-badge-propia {
        background: #f4f5f5;
        border: 1px solid #d8e1df;
        color: #5d6f6d;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 800;
    }
    .ruta-iniciar-btn {
        background: linear-gradient(135deg, #8FA8A6, #5B7C7A);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .ruta-iniciar-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(91,124,122,0.3);
    }
    .ruta-valorar-select {
        border: 1px solid #c9d5d2;
        border-radius: 10px;
        padding: 8px 12px;
        font-family: inherit;
        font-size: 0.9rem;
        background: white;
    }
    .ruta-valorar-btn {
        background: #8FA8A6;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-weight: 800;
        cursor: pointer;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .ruta-valorar-btn:hover { background: #5B7C7A; }
    .rutas-empty {
        text-align: center;
        color: #6a7a78;
        padding: 40px;
        background: #f7f9f8;
        border-radius: 20px;
        border: 1px dashed #cddad7;
        font-weight: 600;
    }
    @media (max-width: 640px) {
        .rutas-page { padding: 20px 14px; }
        .rutas-header h1 { font-size: 1.5rem; }
        .rutas-crear-btn { padding: 10px 18px; font-size: 0.9rem; }
        .ruta-card { padding: 16px; }
        .ruta-card-top h3 { font-size: 1rem; }
        .ruta-footer { flex-direction: column; align-items: flex-start; }
        .mis-rutas-item { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding: 24px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: #1E2A28;">Rutas de la comunidad</h1>
        @if($canCreate)
            <a href="{{ route('rutas.crear') }}" style="background: #8FA8A6; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700;">Crear ruta</a>
        @else
            <span style="font-size: 13px; color: #586866; font-weight: 600;">Crear rutas: Premium</span>
        @endif
    </div>

    @if(session('status'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 14px; border-radius: 8px; margin-bottom: 16px;">
            {{ session('status') }}
        </div>
    @endif

    @if($misRutas->isNotEmpty())
        <div style="background: #eef4f3; border: 1px solid #d6e3e1; border-radius: 10px; padding: 14px; margin-bottom: 20px;">
            <h2 style="margin: 0 0 10px 0; color: #1E2A28; font-size: 1.1rem; font-weight: 800;">Mis rutas publicadas</h2>
            <div style="display: grid; gap: 10px;">
                @foreach($misRutas as $ruta)
                    <div style="background: white; border: 1px solid #dbe4e2; border-radius: 8px; padding: 10px 12px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong>{{ $ruta->titulo }}</strong>
                            <div style="font-size: 12px; color: #5d6f6d; margin-top: 4px;">Completadas: {{ $ruta->completadas_count }} · Rating: {{ number_format($ruta->rating_promedio, 2) }} ({{ $ruta->rating_count }}) · Puntos pasivos: {{ $ruta->puntos_generados }}</div>
                        </div>
                        <a href="{{ route('rutas.editar', $ruta) }}" style="background: white; border: 1px solid #8FA8A6; color: #8FA8A6; padding: 8px 10px; border-radius: 8px; text-decoration: none; font-size: 12px; font-weight: 800; align-self: center;">Editar</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div style="display: grid; gap: 14px;">
        @forelse($rutas as $ruta)
            <article style="background: white; border: 1px solid #dbe4e2; border-radius: 10px; padding: 14px;">
                <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #1E2A28;">{{ $ruta->titulo }}</h3>
                        <p style="margin: 6px 0 0 0; color: #5d6f6d; font-size: 13px;">Creada por {{ $ruta->creador->name ?? 'Usuario' }} · {{ ucfirst($ruta->dificultad) }} · {{ number_format($ruta->distancia_metros) }} m</p>
                    </div>
                    <div style="font-weight: 700; color: #1E2A28;">+{{ $ruta->puntos_recompensa }} ptos</div>
                </div>

                @if($ruta->descripcion)
                    <p style="margin: 10px 0; color: #2c3b39;">{{ $ruta->descripcion }}</p>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
                    <div style="font-size: 13px; color: #5d6f6d;">Rating {{ number_format($ruta->rating_promedio, 2) }} ({{ $ruta->rating_count }}) · Completadas {{ $ruta->completadas_count }}</div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @if(in_array($ruta->id, $misCompletadas))
                            <span style="background: #e9f6ee; border: 1px solid #b6ddc3; color: #1e613b; padding: 8px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">Completada</span>
                            <form action="{{ route('rutas.valorar', $ruta) }}" method="POST" style="display: inline-flex; gap: 6px;">
                                @csrf
                                <select name="estrellas" style="border: 1px solid #c9d5d2; border-radius: 8px; padding: 7px 8px;" required>
                                    <option value="">Valorar</option>
                                    @for($i=1; $i<=5; $i++)
                                        <option value="{{ $i }}" {{ (string)($misValoraciones[$ruta->id] ?? '') === (string)$i ? 'selected' : '' }}>{{ $i }} estrella{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                <button type="submit" style="background: #8FA8A6; color: white; border: none; border-radius: 8px; padding: 8px 10px; font-weight: 700; cursor: pointer;">Guardar</button>
                            </form>
                        @elseif($ruta->creator_user_id !== auth()->id())
                            @php $attempt = $misIntentos[$ruta->id] ?? null; @endphp
                            <a href="{{ route('rutas.iniciar', $ruta) }}" style="background: #8FA8A6; color: white; border: none; border-radius: 8px; padding: 8px 12px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;">{{ $attempt?->status === 'completed' ? 'Ver ruta' : 'Iniciar ruta' }}</a>
                        @else
                            <span style="background: #f4f5f5; border: 1px solid #d8e1df; color: #5d6f6d; padding: 8px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;">Tu ruta</span>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p style="text-align: center; color: #6a7a78; padding: 28px; background: #f7f9f8; border-radius: 10px; border: 1px dashed #cddad7;">Aun no hay rutas publicadas por la comunidad.</p>
        @endforelse
    </div>
</div>
@endsection
