@extends('layouts.plantillaHome')

@section('title', 'Rutas de la comunidad &middot; Moveet')

@section('content')
<div style="max-width: 1100px; margin: 0 auto; padding: 24px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: #1E2A28;">Rutas de la comunidad</h1>
        @if($canCreate)
            <a href="{{ route('rutas.crear') }}" style="background: #8FA8A6; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700;">Crear ruta</a>
        @else
            <span style="font-size: 13px; color: #586866; font-weight: 600;">Crear rutas: nivel 15+ o premium</span>
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
                            <div style="font-size: 12px; color: #5d6f6d; margin-top: 4px;">Completadas: {{ $ruta->completadas_count }} &middot; Rating: {{ number_format($ruta->rating_promedio, 2) }} ({{ $ruta->rating_count }}) &middot; Puntos pasivos: {{ $ruta->puntos_generados }}</div>
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
                        <p style="margin: 6px 0 0 0; color: #5d6f6d; font-size: 13px;">Creada por {{ $ruta->creador->name ?? 'Usuario' }} &middot; {{ ucfirst($ruta->dificultad) }} &middot; {{ number_format($ruta->distancia_metros) }} m</p>
                    </div>
                    <div style="font-weight: 700; color: #1E2A28;">+{{ $ruta->puntos_recompensa }} ptos</div>
                </div>

                @if($ruta->descripcion)
                    <p style="margin: 10px 0; color: #2c3b39;">{{ $ruta->descripcion }}</p>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 8px;">
                    <div style="font-size: 13px; color: #5d6f6d;">Rating {{ number_format($ruta->rating_promedio, 2) }} ({{ $ruta->rating_count }}) &middot; Completadas {{ $ruta->completadas_count }}</div>
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
