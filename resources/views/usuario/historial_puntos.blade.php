@extends('layouts.plantillaHome')

@section('title', 'Mi Historial de Puntos · Moveet')

@push('styles')
<style>
    .historial-wrapper {
        max-width: 680px;
        margin: 0 auto;
        padding: 20px 16px 40px;
    }

    .hist-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .hist-stat-card {
        border-radius: 14px;
        padding: 14px 12px;
        text-align: center;
    }

    .hist-stat-card .label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }

    .hist-stat-card .amount {
        font-size: 20px;
        font-weight: 900;
        line-height: 1;
    }

    .hist-filter-bar {
        display: flex;
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 4px;
        margin-bottom: 16px;
        scrollbar-width: none;
    }

    .hist-filter-bar::-webkit-scrollbar { display: none; }

    .hist-chip {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        text-decoration: none;
        border: 2px solid transparent;
        background: white;
        color: #516260;
        border-color: #d8e3e0;
        transition: all 0.15s;
    }

    .hist-chip.active {
        background: #8FA8A6;
        color: white;
        border-color: #8FA8A6;
    }

    .hist-item {
        background: white;
        border: 1px solid #d8e3e0;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        transition: box-shadow 0.2s;
    }

    .hist-item:hover {
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }

    .hist-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .hist-item-body {
        flex: 1;
        min-width: 0;
    }

    .hist-item-body .motivo {
        font-weight: 700;
        font-size: 14px;
        color: #1E2A28;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hist-item-body .fecha {
        font-size: 12px;
        color: #7a9190;
        margin-top: 2px;
    }

    .hist-item-amount {
        text-align: right;
        flex-shrink: 0;
    }

    .hist-item-amount .pts {
        font-size: 17px;
        font-weight: 900;
        line-height: 1;
    }

    .hist-item-amount .badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 5px;
        margin-top: 4px;
    }

    @media (max-width: 480px) {
        .hist-stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .hist-stat-card .amount {
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="historial-wrapper">
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 1.7rem; font-weight: 900; color: #1E2A28; margin: 0;">Mi Historial</h1>
        <p style="color: #516260; font-size: 13px; margin: 4px 0 0;">Todos tus movimientos de puntos en Moveet.</p>
    </div>

    {{-- ── Estadísticas ── --}}
    <div class="hist-stats-grid">
        <div class="hist-stat-card" style="background: #e9f6ee; border: 1px solid #86efac;">
            <div class="label" style="color: #166534;">Ganados</div>
            <div class="amount" style="color: #2e7d32;">+{{ number_format($estadisticas['total_ganados']) }}</div>
        </div>
        <div class="hist-stat-card" style="background: #fee2e2; border: 1px solid #fca5a5;">
            <div class="label" style="color: #991b1b;">Gastados</div>
            <div class="amount" style="color: #c62828;">-{{ number_format($estadisticas['total_gastados']) }}</div>
        </div>
        <div class="hist-stat-card" style="background: #fff4db; border: 1px solid #fcd34d;">
            <div class="label" style="color: #92400e;">Saldo</div>
            <div class="amount" style="color: #d97706;">{{ number_format($estadisticas['saldo_actual']) }}</div>
        </div>
    </div>

    {{-- ── Filtro chips ── --}}
    <div class="hist-filter-bar">
        <a href="{{ route('usuario.historial_puntos') }}" class="hist-chip {{ !request('tipo') ? 'active' : '' }}">Todos</a>
        @php
            $chipLabels = [
                'mission' => '✅ Misiones',
                'earned' => '🏃 Actividad',
                'reward' => '🎁 Recompensas',
                'referral' => '👥 Referidos',
                'store' => '🛒 Tienda',
                'spent' => '💸 Gastados',
            ];
        @endphp
        @foreach($tipos as $t)
            <a href="{{ route('usuario.historial_puntos', ['tipo' => $t]) }}" class="hist-chip {{ request('tipo') == $t ? 'active' : '' }}">
                {{ $chipLabels[$t] ?? ucfirst(str_replace('_', ' ', $t)) }}
            </a>
        @endforeach
    </div>

    {{-- ── Lista de movimientos ── --}}
    <div style="display: grid; gap: 10px;">
        @forelse($historial as $registro)
            @php
                $isSpent = in_array($registro->tipo, ['spent', 'store']) || (int) $registro->cantidad < 0;

                $icon = match($registro->tipo) {
                    'mission' => '✅',
                    'earned' => '🏃',
                    'reward' => '🎁',
                    'referral' => '👥',
                    'store', 'spent' => '🛒',
                    'admin_adjustment' => '🔧',
                    default => '💰',
                };

                $iconBg = $isSpent ? '#fee2e2' : '#e9f6ee';

                $badgeBg = match($registro->tipo) {
                    'mission' => '#d1fae5',
                    'earned' => '#dcfce7',
                    'reward' => '#fef3c7',
                    'referral' => '#ede9fe',
                    'store', 'spent' => '#fee2e2',
                    default => '#f0f2f1',
                };

                $badgeColor = $isSpent ? '#991b1b' : '#166534';

                $badgeLabel = match($registro->tipo) {
                    'mission' => 'Misión',
                    'earned' => 'Actividad',
                    'reward' => 'Recompensa',
                    'referral' => 'Referido',
                    'store' => 'Tienda',
                    'spent' => 'Gasto',
                    'admin_adjustment' => 'Ajuste',
                    default => ucfirst(str_replace('_', ' ', $registro->tipo)),
                };
            @endphp
            <div class="hist-item">
                <div class="hist-item-icon" style="background: {{ $iconBg }};">{{ $icon }}</div>
                <div class="hist-item-body">
                    <div class="motivo">{{ $registro->motivo ?? 'Movimiento de puntos' }}</div>
                    <div class="fecha">{{ $registro->created_at->format('d/m/Y · H:i') }}</div>
                </div>
                <div class="hist-item-amount">
                    <div class="pts" style="color: {{ $isSpent ? '#c62828' : '#2e7d32' }};">
                        {{ $isSpent ? '-' : '+' }}{{ number_format(abs((int) $registro->cantidad)) }}
                    </div>
                    <div class="badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">{{ $badgeLabel }}</div>
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 48px 20px; background: white; border: 1px dashed #d8e3e0; border-radius: 14px;">
                <div style="font-size: 40px; margin-bottom: 10px;">📊</div>
                <div style="font-weight: 700; color: #1E2A28; margin-bottom: 6px;">Sin movimientos</div>
                <div style="color: #7a9190; font-size: 13px;">¡Completa misiones y rutas para ganar puntos!</div>
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div style="margin-top: 24px;">
        {{ $historial->withQueryString()->links() }}
    </div>
</div>
@endsection
