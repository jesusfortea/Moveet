@extends('layouts.plantillaHome')

@section('title', 'Historial de Puntos · Moveet')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 24px 18px;">
    <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 20px; color: #1E2A28;">Mi Historial de Puntos</h1>

    {{-- Estadísticas --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 24px;">
        <div style="background: #e9f6ee; border: 1px solid #b6ddc3; border-radius: 10px; padding: 14px;">
            <div style="font-size: 11px; color: #1e613b; font-weight: 700; margin-bottom: 6px;">GANADOS</div>
            <div style="font-size: 20px; font-weight: 800; color: #2e7d32;">+{{ number_format($estadisticas['total_ganados']) }}</div>
        </div>
        <div style="background: #ffe4e4; border: 1px solid #ffb6b6; border-radius: 10px; padding: 14px;">
            <div style="font-size: 11px; color: #8b0000; font-weight: 700; margin-bottom: 6px;">GASTADOS</div>
            <div style="font-size: 20px; font-weight: 800; color: #e06060;">-{{ number_format($estadisticas['total_gastados']) }}</div>
        </div>
        <div style="background: #fff4db; border: 1px solid #ffd699; border-radius: 10px; padding: 14px;">
            <div style="font-size: 11px; color: #9a6700; font-weight: 700; margin-bottom: 6px;">SALDO ACTUAL</div>
            <div style="font-size: 20px; font-weight: 800; color: #f4a62a;">{{ number_format($estadisticas['saldo_actual']) }}</div>
        </div>
    </div>

    {{-- Filtro por tipo --}}
    <form method="GET" style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 12px; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 150px;">
            <select name="tipo" onchange="this.form.submit()" style="width: 100%; padding: 8px; border: 1px solid #d8e3e0; border-radius: 8px; font-weight: 600;">
                <option value="">Todos los movimientos</option>
                @foreach($tipos as $t)
                    <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>
                        @switch($t)
                            @case('earned')
                                Ganados
                                @break
                            @case('spent')
                                Gastados
                                @break
                            @case('reward')
                                Recompensas
                                @break
                            @case('mission')
                                Misiones
                                @break
                            @case('store')
                                Tienda
                                @break
                            @case('referral')
                                Referidos
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $t)) }}
                        @endswitch
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Historial en lista --}}
    <div style="display: grid; gap: 10px;">
        @forelse($historial as $registro)
            @php
                $isSpent = in_array($registro->tipo, ['spent', 'store']) || (int) $registro->cantidad < 0;
            @endphp
            <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 12px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 700; color: #1E2A28;">{{ $registro->motivo ?? 'Movimiento de puntos' }}</div>
                    <div style="font-size: 12px; color: #7a9190; margin-top: 4px;">{{ $registro->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 18px; font-weight: 800; color: {{ $isSpent ? '#e06060' : '#2e7d32' }};">
                        {{ $isSpent ? '-' : '+' }}{{ number_format(abs((int) $registro->cantidad)) }}
                    </div>
                    <span style="font-size: 11px; color: #7a9190; background: #f0f2f1; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 4px;">
                        @switch($registro->tipo)
                            @case('earned')
                                Ganado
                                @break
                            @case('spent')
                                Gastado
                                @break
                            @case('reward')
                                Recompensa
                                @break
                            @case('mission')
                                Misión
                                @break
                            @case('store')
                                Compra tienda
                                @break
                            @case('referral')
                                Referido
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $registro->tipo)) }}
                        @endswitch
                    </span>
                </div>
            </div>
        @empty
            <div style="text-align: center; color: #7a9190; padding: 40px 20px; background: white; border: 1px dashed #d8e3e0; border-radius: 10px;">
                No tienes movimientos de puntos aún.
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div style="margin-top: 24px;">
        {{ $historial->links() }}
    </div>
</div>
@endsection
