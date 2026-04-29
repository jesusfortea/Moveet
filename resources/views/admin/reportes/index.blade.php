@extends('layouts.admin')

@section('title', 'Reportes · Admin · Moveet')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 1.8rem; font-weight: 800; margin: 0; color: #1E2A28;">Reportes de contenido</h1>
            <p style="margin: 6px 0 0; color: #516260; font-size: 14px;">Gestión de reportes enviados por los usuarios. Al resolver, se notifica automáticamente a ambas partes.</p>
        </div>
    </div>

    @if (session('status'))
        <div style="margin-bottom: 16px; background: #ecfdf3; border: 1px solid #86efac; color: #166534; border-radius: 10px; padding: 12px 16px; font-weight: 600;">
            {{ session('status') }}
        </div>
    @endif

    {{-- Filtro de estado --}}
    <form method="GET" style="background: white; border: 1px solid #d8e3e0; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <label style="font-size: 12px; font-weight: 700; color: #516260; text-transform: uppercase; letter-spacing: 0.04em;">Estado</label>
        <select name="status" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px; font-size: 13px; color: #1E2A28; font-family: 'Nunito', sans-serif;">
            <option value="">Todos los estados</option>
            <option value="pending"   @selected($status === 'pending')>⏳ Pendiente</option>
            <option value="resolved"  @selected($status === 'resolved')>✅ Resuelto</option>
            <option value="dismissed" @selected($status === 'dismissed')>📋 Descartado</option>
            <option value="blocked"   @selected($status === 'blocked')>🚫 Bloqueado</option>
        </select>
        <button type="submit" style="padding: 8px 18px; border: none; border-radius: 8px; background: #8FA8A6; color: white; font-weight: 700; cursor: pointer; font-size: 13px; font-family: 'Nunito', sans-serif;">Filtrar</button>
        @if($status !== '')
            <a href="{{ route('admin.reportes.index') }}" style="padding: 8px 14px; border: 1px solid #d8e3e0; border-radius: 8px; color: #516260; font-size: 13px; font-weight: 600; text-decoration: none; background: white;">Limpiar</a>
        @endif
        <span style="margin-left: auto; font-size: 12px; color: #7a9190;">{{ $reportes->total() }} reporte(s)</span>
    </form>

    {{-- Lista de reportes --}}
    <div style="display: grid; gap: 14px;">
        @forelse($reportes as $reporte)
            @php
                $badgeColor = match($reporte->status) {
                    'pending'   => '#fff4db:#9a6700',
                    'resolved'  => '#d1fae5:#065f46',
                    'dismissed' => '#f0f2f1:#516260',
                    'blocked'   => '#fee2e2:#991b1b',
                    default     => '#f0f2f1:#516260',
                };
                [$badgeBg, $badgeFg] = explode(':', $badgeColor);

                $statusLabel = match($reporte->status) {
                    'pending'   => '⏳ Pendiente',
                    'resolved'  => '✅ Resuelto',
                    'dismissed' => '📋 Descartado',
                    'blocked'   => '🚫 Bloqueado',
                    default     => ucfirst($reporte->status),
                };
            @endphp

            <article style="background: white; border: 1px solid #d8e3e0; border-radius: 12px;">
                {{-- Header del reporte --}}
                <div style="background: #f7f9f8; border-bottom: 1px solid #e8eceb; padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-weight: 800; font-size: 13px; color: #1E2A28;">#{{ $reporte->id }}</span>
                        <span style="font-size: 13px; color: #516260;">·</span>
                        <span style="font-weight: 700; font-size: 14px; color: #1E2A28;">{{ ucfirst(str_replace('_', ' ', $reporte->reason)) }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: {{ $badgeBg }}; color: {{ $badgeFg }}; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 800;">{{ $statusLabel }}</span>
                        <span style="font-size: 11px; color: #7a9190;">{{ $reporte->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                {{-- Cuerpo --}}
                <div style="padding: 14px 18px;">

                    {{-- Partes involucradas --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div style="background: #f0f5f4; border-radius: 8px; padding: 10px 14px;">
                            <div style="font-size: 10px; font-weight: 800; color: #516260; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Quien reporta</div>
                            <div style="font-weight: 700; color: #1E2A28; font-size: 14px;">{{ $reporte->reporter?->name ?? 'Usuario eliminado' }}</div>
                            <div style="font-size: 11px; color: #7a9190;">{{ $reporte->reporter?->email }}</div>
                        </div>
                        <div style="background: #fff4db; border-radius: 8px; padding: 10px 14px;">
                            <div style="font-size: 10px; font-weight: 800; color: #9a6700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Reportado</div>
                            <div style="font-weight: 700; color: #1E2A28; font-size: 14px;">{{ $reporte->reportedUser?->name ?? 'Usuario eliminado' }}</div>
                            <div style="font-size: 11px; color: #7a9190;">{{ $reporte->reportedUser?->email }}</div>
                        </div>
                    </div>

                    {{-- Detalles del reporte --}}
                    @if($reporte->details)
                        <div style="background: #fafbfa; border: 1px solid #e8eceb; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px;">
                            <div style="font-size: 11px; font-weight: 700; color: #516260; margin-bottom: 6px; text-transform: uppercase;">Descripción del reporte</div>
                            <p style="margin: 0; font-size: 13px; color: #1E2A28; line-height: 1.5;">{{ $reporte->details }}</p>
                        </div>
                    @endif

                    {{-- Nota de resolución si ya fue resuelto --}}
                    @if($reporte->resolution_note)
                        <div style="background: #ecfdf3; border: 1px solid #86efac; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px;">
                            <div style="font-size: 11px; font-weight: 700; color: #166534; margin-bottom: 4px;">Nota de resolución (por {{ $reporte->resolvedBy?->name ?? 'Admin' }})</div>
                            <p style="margin: 0; font-size: 13px; color: #065f46;">{{ $reporte->resolution_note }}</p>
                        </div>
                    @endif

                    {{-- Formulario de acción --}}
                    @if($reporte->status === 'pending')
                        <form method="POST" action="{{ route('admin.reportes.resolve', $reporte) }}" style="border-top: 1px solid #e8eceb; padding-top: 14px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                            @csrf
                            @method('PATCH')

                            <div style="flex: 1; min-width: 160px;">
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #516260; text-transform: uppercase; margin-bottom: 6px;">Acción</label>
                                <select name="status" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'Nunito', sans-serif; color: #1E2A28; background: white;">
                                    <option value="resolved">✅ Resolver (acción tomada)</option>
                                    <option value="dismissed">📋 Descartar (sin infracción)</option>
                                    <option value="blocked">🚫 Bloquear usuario reportado</option>
                                </select>
                            </div>

                            <div style="flex: 2; min-width: 220px;">
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #516260; text-transform: uppercase; margin-bottom: 6px;">Nota interna (opcional)</label>
                                <input type="text" name="resolution_note" placeholder="Ej: Enviada advertencia al usuario..." style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'Nunito', sans-serif; box-sizing: border-box;">
                            </div>

                            <button type="submit" style="padding: 9px 20px; border: none; border-radius: 8px; background: #1E2A28; color: white; font-weight: 700; cursor: pointer; font-size: 13px; font-family: 'Nunito', sans-serif; white-space: nowrap;">Guardar y notificar</button>
                        </form>
                    @else
                        <div style="border-top: 1px solid #e8eceb; padding-top: 12px; font-size: 12px; color: #7a9190;">
                            Resuelto el {{ $reporte->resolved_at?->format('d/m/Y \a \l\a\s H:i') ?? '—' }}
                            @if($reporte->resolvedBy) por <strong>{{ $reporte->resolvedBy->name }}</strong> @endif
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div style="text-align: center; padding: 60px 20px; background: white; border: 1px dashed #d8e3e0; border-radius: 12px;">
                <div style="font-size: 48px; margin-bottom: 12px;">📋</div>
                <div style="font-weight: 700; color: #1E2A28; font-size: 16px; margin-bottom: 6px;">No hay reportes</div>
                <div style="color: #7a9190; font-size: 14px;">No hay reportes con el filtro seleccionado.</div>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">
        {{ $reportes->links() }}
    </div>
</div>
@endsection
