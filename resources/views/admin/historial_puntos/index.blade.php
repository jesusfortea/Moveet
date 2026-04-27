@extends('layouts.admin')

@section('title', 'Historial de Puntos · Admin · Moveet')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #1E2A28;">Historial de Puntos del Sistema</h1>
            <p style="margin: 6px 0 0; color: #516260; font-size: 14px;">Seguimiento completo de cómo se ganan, gastan y distribuyen los puntos.</p>
        </div>
    </div>

    {{-- ── Estadísticas globales ── --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 28px;">
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; padding: 18px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="font-size: 20px;">🟢</span>
                <div style="font-size: 11px; color: #516260; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Puntos Ganados</div>
            </div>
            <div style="font-size: 26px; font-weight: 900; color: #2e7d32;">{{ number_format($estadisticas['total_ganados']) }}</div>
            <div style="font-size: 11px; color: #7a9190; margin-top: 4px;">Misiones, rutas, referidos, recompensas</div>
        </div>

        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; padding: 18px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="font-size: 20px;">🔴</span>
                <div style="font-size: 11px; color: #516260; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Puntos Gastados</div>
            </div>
            <div style="font-size: 26px; font-weight: 900; color: #c62828;">{{ number_format($estadisticas['total_gastados']) }}</div>
            <div style="font-size: 11px; color: #7a9190; margin-top: 4px;">Tienda, congeladores de racha</div>
        </div>

        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; padding: 18px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <span style="font-size: 20px;">🎁</span>
                <div style="font-size: 11px; color: #516260; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Recompensas Otorgadas</div>
            </div>
            <div style="font-size: 26px; font-weight: 900; color: #e65100;">{{ number_format($estadisticas['total_recompensas']) }}</div>
            <div style="font-size: 11px; color: #7a9190; margin-top: 4px;">Bonus por valoraciones 5★</div>
        </div>
    </div>

    {{-- ── Top 5 Ganadores ── --}}
    @if($estadisticas['top_ganadores']->isNotEmpty())
    <div style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
        <h3 style="margin: 0 0 16px; color: #1E2A28; font-size: 15px; font-weight: 800;">🏆 Top 5 Usuarios por Puntos Ganados</h3>
        <div style="display: grid; gap: 8px;">
            @foreach($estadisticas['top_ganadores'] as $i => $record)
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 14px; background: {{ $loop->first ? '#fff4db' : '#f7f9f8' }}; border-radius: 8px; border: 1px solid {{ $loop->first ? '#f59e0b' : '#e8eceb' }};">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 18px; width: 28px; text-align: center;">{{ $loop->first ? '🥇' : ($loop->index === 1 ? '🥈' : ($loop->index === 2 ? '🥉' : '🏅')) }}</span>
                    <div>
                        <div style="font-weight: 700; color: #1E2A28;">{{ $record->usuario->name ?? '—' }}</div>
                        <a href="{{ route('admin.historial_puntos', ['user_id' => $record->user_id]) }}" style="font-size: 11px; color: #8FA8A6; text-decoration: none;">Ver historial →</a>
                    </div>
                </div>
                <strong style="color: #2e7d32; font-size: 16px;">+{{ number_format($record->total) }}</strong>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Filtros ── --}}
    <form method="GET" style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; padding: 18px; margin-bottom: 24px;">
        <div style="font-size: 13px; font-weight: 800; color: #1E2A28; margin-bottom: 12px;">🔍 Filtros</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; color: #516260; text-transform: uppercase;">Usuario</label>
                <select name="user_id" style="width: 100%; padding: 8px 10px; border: 1px solid #d8e3e0; border-radius: 8px; font-size: 13px; background: white;">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; color: #516260; text-transform: uppercase;">Tipo de movimiento</label>
                <select name="tipo" style="width: 100%; padding: 8px 10px; border: 1px solid #d8e3e0; border-radius: 8px; font-size: 13px; background: white;">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        @php
                            $tipoLabel = match($t) {
                                'earned' => '🟢 Ganados (actividad)',
                                'spent' => '🔴 Gastados (tienda)',
                                'reward' => '🎁 Recompensa (bonus creator)',
                                'mission' => '✅ Misión completada',
                                'store' => '🛒 Compra tienda',
                                'referral' => '👥 Referido',
                                'admin_adjustment' => '🔧 Ajuste admin',
                                default => ucfirst(str_replace('_', ' ', $t))
                            };
                        @endphp
                        <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $tipoLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; color: #516260; text-transform: uppercase;">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" style="width: 100%; padding: 8px 10px; border: 1px solid #d8e3e0; border-radius: 8px; font-size: 13px;">
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; color: #516260; text-transform: uppercase;">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" style="width: 100%; padding: 8px 10px; border: 1px solid #d8e3e0; border-radius: 8px; font-size: 13px;">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" style="width: 100%; padding: 9px; background: #8FA8A6; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px;">Filtrar</button>
            </div>
            @if(request()->hasAny(['user_id','tipo','desde','hasta']))
            <div style="align-self: flex-end;">
                <a href="{{ route('admin.historial_puntos') }}" style="display: block; width: 100%; padding: 9px; background: #eef4f3; color: #1E2A28; border: 1px solid #d8e3e0; border-radius: 8px; font-weight: 700; text-align: center; text-decoration: none; font-size: 13px;">Limpiar filtros</a>
            </div>
            @endif
        </div>
    </form>

    {{-- ── Tabla ── --}}
    <div style="background: white; border: 1px solid #d8e3e0; border-radius: 12px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                <thead>
                    <tr style="background: #eef4f3; border-bottom: 2px solid #d8e3e0;">
                        <th style="padding: 12px 14px; text-align: left; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Usuario</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Tipo</th>
                        <th style="padding: 12px 14px; text-align: right; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Cantidad</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Motivo / Descripción</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Velocidad</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 800; font-size: 11px; color: #516260; text-transform: uppercase; letter-spacing: 0.05em;">Fecha</th>
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
                                'earned' => '🟢 Actividad',
                                'mission' => '✅ Misión',
                                'reward' => '🎁 Bonus',
                                'referral' => '👥 Referido',
                                'spent' => '🔴 Gasto',
                                'store' => '🛒 Tienda',
                                'admin_adjustment' => '🔧 Admin',
                                default => ucfirst(str_replace('_', ' ', $registro->tipo)),
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #f0f2f1; {{ $loop->even ? 'background: #fafbfa;' : '' }} {{ $isSuspect ? 'background: #fff5f5 !important;' : '' }}">
                            <td style="padding: 12px 14px;">
                                <a href="{{ route('admin.usuarios.editar', $registro->usuario) }}" style="color: #8FA8A6; text-decoration: none; font-weight: 700; font-size: 13px;">
                                    {{ $registro->usuario->name ?? '—' }}
                                </a>
                                @if($registro->usuarioRelacionado)
                                    <div style="font-size: 11px; color: #7a9190; margin-top: 2px;">rel: {{ $registro->usuarioRelacionado->name }}</div>
                                @endif
                            </td>
                            <td style="padding: 12px 14px;">
                                <span style="background: {{ $bg }}; color: {{ $fg }}; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; white-space: nowrap;">
                                    {{ $tipoLabel }}
                                </span>
                            </td>
                            <td style="padding: 12px 14px; text-align: right; font-weight: 900; font-size: 15px; color: {{ $isSpent ? '#c62828' : '#2e7d32' }};">
                                {{ $isSpent ? '-' : '+' }}{{ number_format(abs((int) $registro->cantidad)) }}
                            </td>
                            <td style="padding: 12px 14px; font-size: 13px; color: {{ $isSuspect ? '#c62828' : '#516260' }}; max-width: 260px;">
                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px;" title="{{ $registro->motivo }}">
                                    {{ $registro->motivo ?? '—' }}
                                </div>
                            </td>
                            <td style="padding: 12px 14px; font-size: 12px; color: #7a9190;">
                                @if($registro->velocidad_maxima)
                                    <span style="background: {{ $registro->velocidad_maxima > 50 ? '#fee2e2' : '#f0f2f1' }}; color: {{ $registro->velocidad_maxima > 50 ? '#c62828' : '#516260' }}; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                        {{ round($registro->velocidad_maxima, 1) }} km/h {{ $registro->velocidad_maxima > 50 ? '⚠️' : '' }}
                                    </span>
                                @else
                                    <span style="color: #c9d4d2;">—</span>
                                @endif
                            </td>
                            <td style="padding: 12px 14px; font-size: 12px; color: #7a9190; white-space: nowrap;">
                                {{ $registro->created_at->format('d/m/Y') }}<br>
                                <span style="color: #b4c0be;">{{ $registro->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #7a9190;">
                                <div style="font-size: 32px; margin-bottom: 8px;">📊</div>
                                No hay registros con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginación --}}
    <div style="margin-top: 20px;">
        {{ $historial->withQueryString()->links() }}
    </div>
</div>
@endsection
