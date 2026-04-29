@extends('layouts.admin')

@section('title', 'Historial de Puntos &middot; Admin &middot; Moveet')

@push('styles')
<style>
    .points-history-shell {
        width: min(100%, 1400px);
        margin: 0 auto;
    }

    .points-history-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px;
    }

    .points-history-title {
        margin: 0;
        color: #1E2A28;
        font-size: clamp(1.65rem, 2vw, 1.95rem);
        font-weight: 800;
    }

    .points-history-subtitle {
        margin: 6px 0 0;
        color: #516260;
        font-size: 14px;
        line-height: 1.5;
    }

    .points-history-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 24px;
    }

    .points-history-card,
    .points-history-panel {
        background: #fff;
        border: 1px solid #d8e3e0;
        border-radius: 12px;
    }

    .points-history-card {
        padding: 18px;
        min-width: 0;
    }

    .points-history-card-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }

    .points-history-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
    }

    .points-history-card-label {
        font-size: 11px;
        color: #516260;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .points-history-card-value {
        font-size: clamp(1.5rem, 3vw, 1.75rem);
        font-weight: 900;
    }

    .points-history-card-note {
        margin-top: 4px;
        font-size: 11px;
        color: #7a9190;
        line-height: 1.45;
    }

    .points-history-panel {
        padding: 20px;
        margin-bottom: 24px;
    }

    .points-history-panel-title {
        margin: 0 0 16px;
        color: #1E2A28;
        font-size: 15px;
        font-weight: 800;
    }

    .points-history-ranking {
        display: grid;
        gap: 8px;
    }

    .points-history-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 8px;
        border: 1px solid #e8eceb;
        min-width: 0;
    }

    .points-history-top-main,
    .points-history-top-user {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .points-history-top-rank {
        width: 28px;
        text-align: center;
        font-size: 18px;
        flex: 0 0 auto;
    }

    .points-history-top-user-copy {
        min-width: 0;
    }

    .points-history-top-name {
        font-weight: 700;
        color: #1E2A28;
        overflow-wrap: anywhere;
    }

    .points-history-top-link {
        font-size: 11px;
        color: #8FA8A6;
        text-decoration: none;
    }

    .points-history-top-total {
        color: #2e7d32;
        font-size: 16px;
        font-weight: 800;
        white-space: nowrap;
    }

    .points-history-filters {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .points-history-field {
        min-width: 0;
    }

    .points-history-field--action {
        display: flex;
        align-items: end;
    }

    .points-history-label {
        display: block;
        margin-bottom: 6px;
        color: #516260;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .points-history-input,
    .points-history-select,
    .points-history-button,
    .points-history-link-button {
        width: 100%;
        min-height: 40px;
        border-radius: 8px;
        font-size: 13px;
        font-family: 'Nunito', sans-serif;
        box-sizing: border-box;
    }

    .points-history-input,
    .points-history-select {
        border: 1px solid #d8e3e0;
        background: #fff;
        color: #1E2A28;
        padding: 8px 10px;
    }

    .points-history-button {
        border: 0;
        background: #8FA8A6;
        color: #fff;
        font-weight: 700;
        cursor: pointer;
    }

    .points-history-link-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 12px;
        background: #eef4f3;
        color: #1E2A28;
        border: 1px solid #d8e3e0;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
    }

    .points-history-table-shell {
        overflow: hidden;
        margin-bottom: 20px;
    }

    .points-history-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .points-history-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }

    .points-history-table thead tr {
        background: #eef4f3;
        border-bottom: 2px solid #d8e3e0;
    }

    .points-history-table th {
        padding: 12px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #516260;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .points-history-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0f2f1;
        vertical-align: middle;
        min-width: 0;
    }

    .points-history-table tbody tr.is-even {
        background: #fafbfa;
    }

    .points-history-table tbody tr.is-suspect {
        background: #fff5f5 !important;
    }

    .points-history-user-link {
        color: #8FA8A6;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .points-history-related {
        margin-top: 2px;
        font-size: 11px;
        color: #7a9190;
        overflow-wrap: anywhere;
    }

    .points-history-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .points-history-amount {
        text-align: right;
        font-weight: 900;
        font-size: 15px;
        white-space: nowrap;
    }

    .points-history-motivo-cell {
        max-width: 280px;
        font-size: 13px;
        color: #516260;
    }

    .points-history-motivo-cell.is-suspect {
        color: #c62828;
    }

    .points-history-motivo {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .points-history-speed {
        font-size: 12px;
        color: #7a9190;
    }

    .points-history-speed-chip {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
    }

    .points-history-empty {
        padding: 40px 24px;
        text-align: center;
        color: #7a9190;
    }

    .points-history-empty-icon {
        font-size: 32px;
        margin-bottom: 8px;
    }

    .points-history-pagination {
        margin-top: 20px;
    }

    @media (max-width: 1080px) {
        .points-history-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .points-history-filters {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .points-history-shell {
            width: 100%;
        }

        .points-history-header,
        .points-history-top-row,
        .points-history-top-main {
            flex-direction: column;
            align-items: flex-start;
        }

        .points-history-summary,
        .points-history-filters {
            grid-template-columns: 1fr;
        }

        .points-history-panel,
        .points-history-card {
            padding: 16px;
        }

        .points-history-table,
        .points-history-table thead,
        .points-history-table tbody,
        .points-history-table tr,
        .points-history-table th,
        .points-history-table td {
            display: block;
        }

        .points-history-table {
            min-width: 0;
        }

        .points-history-table thead {
            display: none;
        }

        .points-history-table tbody {
            display: grid;
            gap: 14px;
            padding: 14px;
        }

        .points-history-table tbody tr {
            border: 1px solid #d8e3e0;
            border-radius: 12px;
            background: #fff !important;
            padding: 12px;
        }

        .points-history-table td {
            padding: 8px 0;
            border: 0;
            text-align: left;
        }

        .points-history-table td::before {
            content: attr(data-label);
            display: block;
            margin-bottom: 6px;
            color: #516260;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .points-history-amount {
            text-align: left;
        }

        .points-history-motivo-cell,
        .points-history-motivo {
            max-width: none;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            overflow-wrap: anywhere;
        }

        .points-history-empty {
            padding: 28px 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="points-history-shell">
    <div class="points-history-header">
        <div>
            <h1 class="points-history-title">Historial de Puntos del Sistema</h1>
            <p class="points-history-subtitle">Seguimiento completo de c&oacute;mo se ganan, gastan y distribuyen los puntos.</p>
        </div>
    </div>

    <div class="points-history-summary">
        <section class="points-history-card">
            <div class="points-history-card-head">
                <span class="points-history-card-icon" style="background: #dcfce7; color: #166534;">+</span>
                <div class="points-history-card-label">Puntos ganados</div>
            </div>
            <div class="points-history-card-value" style="color: #2e7d32;">{{ number_format($estadisticas['total_ganados']) }}</div>
            <div class="points-history-card-note">Misiones, rutas, referidos y recompensas.</div>
        </section>

        <section class="points-history-card">
            <div class="points-history-card-head">
                <span class="points-history-card-icon" style="background: #fee2e2; color: #991b1b;">-</span>
                <div class="points-history-card-label">Puntos gastados</div>
            </div>
            <div class="points-history-card-value" style="color: #c62828;">{{ number_format($estadisticas['total_gastados']) }}</div>
            <div class="points-history-card-note">Tienda y congeladores de racha.</div>
        </section>

        <section class="points-history-card">
            <div class="points-history-card-head">
                <span class="points-history-card-icon" style="background: #fff4db; color: #9a6700;">R</span>
                <div class="points-history-card-label">Recompensas otorgadas</div>
            </div>
            <div class="points-history-card-value" style="color: #e65100;">{{ number_format($estadisticas['total_recompensas']) }}</div>
            <div class="points-history-card-note">Bonos y premios repartidos desde la actividad.</div>
        </section>
    </div>

    @if($estadisticas['top_ganadores']->isNotEmpty())
        <section class="points-history-panel">
            <h3 class="points-history-panel-title">Top 5 usuarios por puntos ganados</h3>

            <div class="points-history-ranking">
                @foreach($estadisticas['top_ganadores'] as $record)
                    <div
                        class="points-history-top-row"
                        style="background: {{ $loop->first ? '#fff4db' : '#f7f9f8' }}; border-color: {{ $loop->first ? '#f59e0b' : '#e8eceb' }};"
                    >
                        <div class="points-history-top-main">
                            <div class="points-history-top-rank">
                                {{ $loop->first ? '1' : ($loop->index === 1 ? '2' : ($loop->index === 2 ? '3' : $loop->iteration)) }}
                            </div>
                            <div class="points-history-top-user-copy">
                                <div class="points-history-top-name">{{ $record->usuario->name ?? '-' }}</div>
                                <a href="{{ route('admin.historial_puntos', ['user_id' => $record->user_id]) }}" class="points-history-top-link">Ver historial</a>
                            </div>
                        </div>

                        <strong class="points-history-top-total">+{{ number_format($record->total) }}</strong>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <form method="GET" class="points-history-panel">
        <div class="points-history-panel-title">Filtros</div>

        <div class="points-history-filters">
            <div class="points-history-field">
                <label for="points-user-id" class="points-history-label">Usuario</label>
                <select id="points-user-id" name="user_id" class="points-history-select">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="points-history-field">
                <label for="points-tipo" class="points-history-label">Tipo de movimiento</label>
                <select id="points-tipo" name="tipo" class="points-history-select">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        @php
                            $tipoLabel = match($t) {
                                'earned' => 'Ganados (actividad)',
                                'spent' => 'Gastados (tienda)',
                                'reward' => 'Recompensa (bonus creador)',
                                'mission' => 'Misi&oacute;n completada',
                                'store' => 'Compra tienda',
                                'referral' => 'Referido',
                                'admin_adjustment' => 'Ajuste admin',
                                default => ucfirst(str_replace('_', ' ', $t))
                            };
                        @endphp
                        <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $tipoLabel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="points-history-field">
                <label for="points-desde" class="points-history-label">Desde</label>
                <input id="points-desde" type="date" name="desde" value="{{ request('desde') }}" class="points-history-input">
            </div>

            <div class="points-history-field">
                <label for="points-hasta" class="points-history-label">Hasta</label>
                <input id="points-hasta" type="date" name="hasta" value="{{ request('hasta') }}" class="points-history-input">
            </div>

            <div class="points-history-field points-history-field--action">
                <button type="submit" class="points-history-button">Filtrar</button>
            </div>

            @if(request()->hasAny(['user_id', 'tipo', 'desde', 'hasta']))
                <div class="points-history-field points-history-field--action">
                    <a href="{{ route('admin.historial_puntos') }}" class="points-history-link-button">Limpiar filtros</a>
                </div>
            @endif
        </div>
    </form>

    <section class="points-history-panel points-history-table-shell">
        <div class="points-history-table-wrap">
            <table class="points-history-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Tipo</th>
                        <th style="text-align: right;">Cantidad</th>
                        <th>Motivo / Descripci&oacute;n</th>
                        <th>Velocidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $registro)
                        @php
                            $isSpent = in_array($registro->tipo, ['spent', 'store']) || (int) $registro->cantidad < 0;
                            $isSuspect = str_contains($registro->motivo ?? '', '🚨');

                            $tipoBadgeColor = match($registro->tipo) {
                                'earned' => '#dcfce7:#166534',
                                'mission' => '#d1fae5:#065f46',
                                'reward' => '#fef3c7:#92400e',
                                'referral' => '#ede9fe:#4c1d95',
                                'spent', 'store' => '#fee2e2:#991b1b',
                                'admin_adjustment' => '#f3f4f6:#374151',
                                default => '#f0f2f1:#516260',
                            };
                            [$bg, $fg] = explode(':', $tipoBadgeColor);

                            $tipoLabel = match($registro->tipo) {
                                'earned' => 'Actividad',
                                'mission' => 'Misi&oacute;n',
                                'reward' => 'Bonus',
                                'referral' => 'Referido',
                                'spent' => 'Gasto',
                                'store' => 'Tienda',
                                'admin_adjustment' => 'Admin',
                                default => ucfirst(str_replace('_', ' ', $registro->tipo)),
                            };
                        @endphp
                        <tr class="{{ $loop->even ? 'is-even' : '' }} {{ $isSuspect ? 'is-suspect' : '' }}">
                            <td data-label="Usuario">
                                <a href="{{ route('admin.usuarios.editar', $registro->usuario) }}" class="points-history-user-link">
                                    {{ $registro->usuario->name ?? '-' }}
                                </a>
                                @if($registro->usuarioRelacionado)
                                    <div class="points-history-related">rel: {{ $registro->usuarioRelacionado->name }}</div>
                                @endif
                            </td>
                            <td data-label="Tipo">
                                <span class="points-history-badge" style="background: {{ $bg }}; color: {{ $fg }};">
                                    {{ $tipoLabel }}
                                </span>
                            </td>
                            <td data-label="Cantidad" class="points-history-amount" style="color: {{ $isSpent ? '#c62828' : '#2e7d32' }};">
                                {{ $isSpent ? '-' : '+' }}{{ number_format(abs((int) $registro->cantidad)) }}
                            </td>
                            <td data-label="Motivo" class="points-history-motivo-cell {{ $isSuspect ? 'is-suspect' : '' }}">
                                <div class="points-history-motivo" title="{{ $registro->motivo }}">
                                    {{ $registro->motivo ?? '-' }}
                                </div>
                            </td>
                            <td data-label="Velocidad" class="points-history-speed">
                                @if($registro->velocidad_maxima)
                                    <span
                                        class="points-history-speed-chip"
                                        style="background: {{ $registro->velocidad_maxima > 50 ? '#fee2e2' : '#f0f2f1' }}; color: {{ $registro->velocidad_maxima > 50 ? '#c62828' : '#516260' }};"
                                    >
                                        {{ round($registro->velocidad_maxima, 1) }} km/h{{ $registro->velocidad_maxima > 50 ? ' alerta' : '' }}
                                    </span>
                                @else
                                    <span style="color: #c9d4d2;">-</span>
                                @endif
                            </td>
                            <td data-label="Fecha" style="font-size: 12px; color: #7a9190; white-space: nowrap;">
                                {{ $registro->created_at->format('d/m/Y') }}<br>
                                <span style="color: #b4c0be;">{{ $registro->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="points-history-empty">
                                <div class="points-history-empty-icon">0</div>
                                No hay registros con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="points-history-pagination">
        {{ $historial->withQueryString()->links() }}
    </div>
</div>
@endsection
