@extends('layouts.plantillaHome')

@section('title', 'Confirmar ruta · Moveet')

@section('content')
<div style="max-width: 760px; margin: 0 auto; padding: 24px 18px;">
    <div style="background: #ffffff; border: 1px solid #d8e3e0; border-radius: 16px; padding: 18px;">
        <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 8px; color: #1E2A28;">Confirmar ruta</h1>
        <p style="margin: 0 0 18px; color: #5d6f6d; font-weight: 600;">Revisa los detalles antes de marcar la ruta como completada.</p>

        <div style="display: grid; gap: 10px; margin-bottom: 18px;">
            <div><strong>Titulo:</strong> {{ $ruta->titulo }}</div>
            <div><strong>Dificultad:</strong> {{ ucfirst($ruta->dificultad) }}</div>
            <div><strong>Distancia:</strong> {{ number_format($ruta->distancia_metros) }} m</div>
            <div><strong>Puntos:</strong> +{{ $ruta->puntos_recompensa }} ptos</div>
            <div><strong>Nivel minimo:</strong> {{ $ruta->min_nivel }}</div>
            <div><strong>Premium:</strong> {{ $ruta->premium_only ? 'Sí' : 'No' }}</div>
        </div>

        @if($ruta->descripcion)
            <p style="color: #2c3b39; margin-bottom: 18px;">{{ $ruta->descripcion }}</p>
        @endif

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form action="{{ route('rutas.completar', $ruta) }}" method="POST">
                @csrf
                <button type="submit" style="background: #8FA8A6; color: white; border: none; border-radius: 10px; padding: 10px 14px; font-weight: 800; cursor: pointer;">Confirmar completada</button>
            </form>
            <a href="{{ route('rutas.index') }}" style="background: white; color: #8FA8A6; border: 1px solid #8FA8A6; border-radius: 10px; padding: 10px 14px; font-weight: 800; text-decoration: none;">Cancelar</a>
        </div>
    </div>
</div>
@endsection
