@extends('layouts.admin')

@section('title', 'Historial de Puntos · Admin · Moveet')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 24px 18px;">
    <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0 0 20px; color: #1E2A28;">Historial de Puntos del Sistema</h1>

    {{-- Estadísticas --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;">
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 16px;">
            <div style="font-size: 12px; color: #516260; font-weight: 700; margin-bottom: 8px;">PUNTOS GANADOS</div>
            <div style="font-size: 24px; font-weight: 800; color: #2e7d32;">{{ number_format($estadisticas['total_ganados']) }}</div>
        </div>
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 16px;">
            <div style="font-size: 12px; color: #516260; font-weight: 700; margin-bottom: 8px;">PUNTOS GASTADOS</div>
            <div style="font-size: 24px; font-weight: 800; color: #e06060;">{{ number_format($estadisticas['total_gastados']) }}</div>
        </div>
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 16px;">
            <div style="font-size: 12px; color: #516260; font-weight: 700; margin-bottom: 8px;">RECOMPENSAS OTORGADAS</div>
            <div style="font-size: 24px; font-weight: 800; color: #f4a62a;">{{ number_format($estadisticas['total_recompensas']) }}</div>
        </div>
    </div>

    {{-- Top 5 Ganadores --}}
    @if($estadisticas['top_ganadores']->isNotEmpty())
        <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
            <h3 style="margin: 0 0 12px; color: #1E2A28;">Top 5 Ganadores</h3>
            <div style="display: grid; gap: 8px;">
                @foreach($estadisticas['top_ganadores'] as $record)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f7f9f8; border-radius: 8px;">
                        <span>{{ $record->usuario->name }}</span>
                        <strong style="color: #2e7d32;">+{{ number_format($record->total) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Filtros --}}
    <form method="GET" style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 16px; margin-bottom: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #516260;">Usuario</label>
                <select name="user_id" style="width: 100%; padding: 8px; border: 1px solid #d8e3e0; border-radius: 8px;">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #516260;">Tipo</label>
                <select name="tipo" style="width: 100%; padding: 8px; border: 1px solid #d8e3e0; border-radius: 8px;">
                    <option value="">Todos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #516260;">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" style="width: 100%; padding: 8px; border: 1px solid #d8e3e0; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: #516260;">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" style="width: 100%; padding: 8px; border: 1px solid #d8e3e0; border-radius: 8px;">
            </div>
            <div style="align-self: flex-end;">
                <button type="submit" style="width: 100%; padding: 8px; background: #8FA8A6; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Filtrar</button>
            </div>
        </div>
    </form>

    {{-- Tabla de historial --}}
    <div style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #eef4f3; border-bottom: 1px solid #d8e3e0;">
                    <th style="padding: 12px; text-align: left; font-weight: 700; font-size: 12px; color: #516260;">Usuario</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; font-size: 12px; color: #516260;">Tipo</th>
                    <th style="padding: 12px; text-align: right; font-weight: 700; font-size: 12px; color: #516260;">Cantidad</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; font-size: 12px; color: #516260;">Motivo</th>
                    <th style="padding: 12px; text-align: left; font-weight: 700; font-size: 12px; color: #516260;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historial as $registro)
                    <tr style="border-bottom: 1px solid #f0f2f1; {{ $loop->even ? 'background: #f7f9f8;' : '' }}">
                        <td style="padding: 12px;">
                            <a href="{{ route('admin.usuarios.editar', $registro->usuario) }}" style="color: #8FA8A6; text-decoration: none; font-weight: 600;">
                                {{ $registro->usuario->name }}
                            </a>
                        </td>
                        <td style="padding: 12px; font-size: 12px;">
                            <span style="background: #eef4f3; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $registro->tipo)) }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 700; color: {{ $registro->tipo === 'spent' || $registro->tipo === 'admin_adjustment' ? '#e06060' : '#2e7d32' }};">
                            {{ $registro->tipo === 'spent' ? '-' : '+' }}{{ number_format($registro->cantidad) }}
                        </td>
                        <td style="padding: 12px; font-size: 13px; color: #516260;">{{ $registro->motivo }}</td>
                        <td style="padding: 12px; font-size: 12px; color: #7a9190;">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #7a9190;">No hay registros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div style="margin-top: 24px;">
        {{ $historial->links() }}
    </div>
</div>
@endsection
