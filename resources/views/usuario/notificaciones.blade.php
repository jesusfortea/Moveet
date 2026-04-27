@extends('layouts.plantillaHome')

@section('title', 'Notificaciones · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    <div class="inventario-topbar">
        <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver</a>
        <h1 class="usuario-page-title usuario-page-title--center">Notificaciones</h1>
        <form action="{{ route('usuario.notificaciones.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="btn-main">Marcar todas leídas</button>
        </form>
    </div>

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
                    display: grid;
                    grid-template-columns: 1fr;
                    background: {{ $item->read_at ? '#f3f6f5' : '#e9f6ee' }};
                    border-color: {{ $item->read_at ? '#5f6f6d' : '#86c9a0' }};
                    border-radius: 4px;
                    gap: 6px;
                ">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <strong style="font-size: .95rem;">{{ $item->title }}</strong>
                        <small style="color: #7a9190; font-size: .78rem;">{{ $item->created_at?->diffForHumans() }}</small>
                    </div>

                    @if($item->body)
                        <p style="margin: 0; font-size: .88rem; color: #41514f; font-weight: 400;">{{ $item->body }}</p>
                    @endif

                    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        @if($item->action_url)
                            <a href="{{ $item->action_url }}" class="btn-link" style="font-size: .85rem;">Abrir &rarr;</a>
                        @endif
                        @if(!$item->read_at)
                            <form action="{{ route('usuario.notificaciones.read_one', $item) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-link" style="font-size: .85rem;">Marcar como leída</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="panel-empty panel-empty--center" style="padding: 24px 0;">Todavía no tienes notificaciones.</p>
            @endforelse
        </div>
    </section>

    <div style="margin-top: 16px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
