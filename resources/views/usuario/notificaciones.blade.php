@extends('layouts.plantillaHome')

@section('title', 'Notificaciones · Moveet')

@section('content')
<div style="max-width: 900px; margin: 24px auto; padding: 0 12px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1E2A28;">Notificaciones</h1>
        <form action="{{ route('usuario.notificaciones.read_all') }}" method="POST">
            @csrf
            <button style="padding:10px 14px; border:none; border-radius:10px; background:#8FA8A6; color:white; font-weight:700;">Marcar todas como leídas</button>
        </form>
    </div>

    @if (session('status'))
        <div style="margin-bottom:12px; background:#ecfdf3; border:1px solid #86efac; color:#166534; border-radius:10px; padding:10px 12px;">{{ session('status') }}</div>
    @endif

    <div style="display:grid; gap:10px;">
        @forelse($notifications as $item)
            <div style="border:1px solid #d1d5db; border-radius:12px; background: {{ $item->read_at ? '#ffffff' : '#f0fdf4' }}; padding:12px;">
                <div style="display:flex; justify-content:space-between; gap:10px; align-items:center; flex-wrap:wrap;">
                    <strong style="color:#1f2937;">{{ $item->title }}</strong>
                    <small style="color:#6b7280;">{{ $item->created_at?->diffForHumans() }}</small>
                </div>
                @if($item->body)
                    <p style="margin:8px 0; color:#4b5563;">{{ $item->body }}</p>
                @endif
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    @if($item->action_url)
                        <a href="{{ $item->action_url }}" style="color:#0f766e; font-weight:700;">Abrir</a>
                    @endif
                    @if(!$item->read_at)
                        <form action="{{ route('usuario.notificaciones.read_one', $item) }}" method="POST">
                            @csrf
                            <button style="border:none; background:transparent; color:#047857; font-weight:700;">Marcar como leída</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:#6b7280;">Todavía no tienes notificaciones.</p>
        @endforelse
    </div>

    <div style="margin-top:16px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
