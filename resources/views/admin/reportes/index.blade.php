@extends('layouts.admin')

@section('title', 'Admin Reportes - Moveet')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 16px;">Reportes de contenido</h1>

    @if (session('status'))
        <div style="margin-bottom:12px; background:#ecfdf3; border:1px solid #86efac; color:#166534; border-radius:10px; padding:10px 12px;">{{ session('status') }}</div>
    @endif

    <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
        <select name="status" style="border:1px solid #d1d5db; border-radius:8px; padding:8px 10px;">
            <option value="">Todos los estados</option>
            <option value="pending" @selected($status === 'pending')>Pendiente</option>
            <option value="resolved" @selected($status === 'resolved')>Resuelto</option>
            <option value="dismissed" @selected($status === 'dismissed')>Descartado</option>
            <option value="blocked" @selected($status === 'blocked')>Bloqueado</option>
        </select>
        <button type="submit" style="padding:8px 12px; border:none; border-radius:8px; background:#8FA8A6; color:white; font-weight:700;">Filtrar</button>
    </form>

    <div style="display:grid; gap:12px;">
        @forelse($reportes as $reporte)
            <article style="border:1px solid #d1d5db; border-radius:12px; background:#fff; padding:14px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                    <strong>#{{ $reporte->id }} · {{ ucfirst($reporte->reason) }}</strong>
                    <span style="padding:4px 8px; border-radius:999px; background:#eef2ff; color:#3730a3; font-size:12px;">{{ $reporte->status }}</span>
                </div>
                <p style="margin:8px 0 4px; color:#4b5563;">Reporta: {{ $reporte->reporter?->name ?? 'N/A' }} | Reportado: {{ $reporte->reportedUser?->name ?? 'N/A' }}</p>
                @if($reporte->details)
                    <p style="margin:0 0 8px; color:#1f2937;">{{ $reporte->details }}</p>
                @endif

                <form method="POST" action="{{ route('admin.reportes.resolve', $reporte) }}" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-top:8px;">
                    @csrf
                    @method('PATCH')
                    <select name="status" required style="border:1px solid #d1d5db; border-radius:8px; padding:8px;">
                        <option value="resolved">Resuelto</option>
                        <option value="dismissed">Descartado</option>
                        <option value="blocked">Bloqueado</option>
                    </select>
                    <input type="text" name="resolution_note" placeholder="Nota de resolución (opcional)" style="min-width: 260px; border:1px solid #d1d5db; border-radius:8px; padding:8px;">
                    <button type="submit" style="padding:8px 12px; border:none; border-radius:8px; background:#1f2937; color:white;">Guardar</button>
                </form>
            </article>
        @empty
            <p>No hay reportes en este filtro.</p>
        @endforelse
    </div>

    <div style="margin-top:16px;">{{ $reportes->links() }}</div>
</div>
@endsection
