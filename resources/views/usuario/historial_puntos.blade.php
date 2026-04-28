@extends('layouts.plantillaHome')

@section('title', 'Mi Historial de Puntos · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
<style>
    .historial-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    .hist-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .hist-stat-card {
        background: var(--usr-surface);
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        border: 1px solid var(--usr-border);
        box-shadow: var(--usr-shadow);
        transition: transform 0.2s ease;
    }

    .hist-stat-card:hover {
        transform: translateY(-4px);
    }

    .hist-stat-card .label {
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .hist-stat-card .amount {
        font-size: 1.8rem;
        font-weight: 900;
        line-height: 1;
    }

    .hist-filter-bar {
        display: flex;
        gap: 12px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 24px;
        scrollbar-width: none;
    }

    .hist-filter-bar::-webkit-scrollbar { display: none; }

    .hist-chip {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: 999px;
        font-size: 0.95rem;
        font-weight: 800;
        white-space: nowrap;
        text-decoration: none;
        background: var(--usr-surface);
        color: var(--usr-text-muted);
        border: 2px solid var(--usr-border);
        transition: all 0.2s ease;
    }

    .hist-chip:hover {
        border-color: var(--usr-primary);
        color: var(--usr-text);
    }

    .hist-chip.active {
        background: var(--usr-primary);
        color: white;
        border-color: var(--usr-primary);
    }

    .hist-item {
        background: var(--usr-surface);
        border: 1px solid var(--usr-border);
        border-radius: 20px;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease;
    }

    .hist-item:hover {
        transform: translateX(4px);
        border-color: var(--usr-primary);
    }

    .hist-item-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .hist-item-body {
        flex: 1;
        min-width: 0;
    }

    .hist-item-body .motivo {
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--usr-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .hist-item-body .fecha {
        font-size: 0.85rem;
        color: var(--usr-text-muted);
        font-weight: 600;
        margin-top: 4px;
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

    @media (max-width: 600px) {
        .hist-stats-grid {
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px;
        }
        .hist-stat-card {
            padding: 12px 8px;
        }
        .hist-stat-card .label {
            font-size: 0.7rem;
        }
        .hist-stat-card .amount {
            font-size: 1.2rem;
        }
        .hist-filter-bar {
            gap: 8px;
        }
        .hist-chip {
            padding: 8px 14px;
            font-size: 0.82rem;
        }
        .hist-item {
            padding: 12px 14px;
            gap: 10px;
        }
        .hist-item-body .motivo {
            font-size: 0.95rem;
        }
        .historial-wrapper {
            padding: 24px 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="usuario-page">
    <div class="inventario-topbar">
        <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver al perfil</a>
    </div>

    <div style="margin-bottom: 32px; text-align: center;">
        <h1 class="usuario-page-title" style="margin: 0;">Mi Historial</h1>
        <p style="color: var(--usr-text-muted); font-size: 1rem; font-weight: 600; margin: 8px 0 0;">Todos tus movimientos de puntos en Moveet.</p>
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
