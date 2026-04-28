@extends('layouts.plantillaHome')

@section('title', 'Notificaciones · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    {{-- Header: back + button on same row, title below --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px;">
        <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver</a>
        <form action="{{ route('usuario.notificaciones.read_all') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-main" style="height: 40px; padding: 0 18px; font-size: 0.82rem; width: auto; white-space: nowrap;">Marcar todas leídas</button>
        </form>
    </div>
    <h1 class="usuario-page-title" style="text-align: center; margin-bottom: 24px;">Notificaciones</h1>

    @if (session('status'))
        <div class="usuario-alert">{{ session('status') }}</div>
    @endif

    <section class="panel-card inventario-panel-full">
        <div class="panel-header">
            <h2>Tus notificaciones</h2>
            @if($notifications->where('read_at', null)->count() > 0)
                <span class="inventario-count">{{ $notifications->where('read_at', null)->count() }} sin leer</span>
            @endif
        </div>

        <div style="display: grid; gap: 10px; margin-top: 4px;">
            @forelse($notifications as $item)
                <div class="tarjeta-row" style="
                    display: flex;
                    flex-direction: column;
                    align-items: stretch;
                    background: {{ $item->read_at ? 'var(--usr-surface)' : '#ecfdf5' }};
                    border-color: {{ $item->read_at ? 'var(--usr-border)' : '#34d399' }};
                    border-radius: 16px;
                    gap: 12px;
                ">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <strong style="font-size: 1.1rem; color: {{ $item->read_at ? 'var(--usr-text)' : '#064e3b' }};">{{ $item->title }}</strong>
                        <small style="color: var(--usr-text-muted); font-size: 0.85rem; font-weight: 600;">{{ $item->created_at?->diffForHumans() }}</small>
                    </div>

                    @if($item->body)
                        <p style="margin: 0; font-size: 0.95rem; color: var(--usr-text-muted); font-weight: 500; line-height: 1.4;">{{ $item->body }}</p>
                    @endif

                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 8px;">
                        @if($item->action_url)
                            <a href="{{ $item->action_url }}" class="btn-link" style="background: rgba(34, 197, 94, 0.1); padding: 8px 20px;">Abrir &rarr;</a>
                        @endif
                        @if(!$item->read_at)
                            <form action="{{ route('usuario.notificaciones.read_one', $item) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-link">Marcar como leída</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="panel-empty panel-empty--center">Todavía no tienes notificaciones.</p>
            @endforelse
        </div>
    </section>

    <div style="margin-top: 16px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
