@extends('layouts.plantillaHome')

@section('title', 'Usuario · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page">
    <h1 class="usuario-page-title">Usuario</h1>

    @if (session('status'))
        <div class="usuario-alert">{{ session('status') }}</div>
    @endif

    <div class="usuario-grid">
        <section class="perfil-card">
            <div class="perfil-avatar-wrap">
                <div class="perfil-avatar">
                    @if ($usuario->ruta_imagen)
                        <img src="{{ asset($usuario->ruta_imagen) }}" alt="Avatar de usuario">
                    @else
                        <span>{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                    @endif
                </div>
                <button type="button" class="btn-link">Cambiar imagen</button>
            </div>

            <div class="perfil-fields">
                <label for="nombre">Cambiar nombre</label>
                <input id="nombre" type="text" value="{{ $usuario->name }}" readonly>

                <label for="correo">Cambiar correo</label>
                <input id="correo" type="text" value="{{ $usuario->email }}" readonly>

                <label for="telefono">Cambiar telefono</label>
                <input id="telefono" type="text" value="{{ $usuario->telefono ?? 'Sin telefono' }}" readonly>
            </div>

            <button type="button" class="btn-main" disabled>Guardar</button>
        </section>

        <section class="usuario-main">
            <article class="panel-card">
                <div class="panel-header">
                    <h2>Tarjetas</h2>
                    <a class="btn-link" href="{{ route('usuario.tarjeta.create') }}">+ Anadir tarjeta</a>
                </div>

                @if ($tarjeta)
                    <div class="tarjeta-row">
                        <span>{{ $tarjeta->titular }}</span>
                        <span>{{ $tarjeta->numero_enmascarado }}</span>
                    </div>
                @else
                    <p class="panel-empty">Aun no tienes una tarjeta registrada.</p>
                @endif
            </article>

            <article class="panel-card inventario-card">
                <div class="panel-header panel-header-stack">
                    <h2>Inventario</h2>
                    <a class="btn-link" href="#">Ver todos &gt;</a>
                </div>

                <div class="inventario-grid">
                    @forelse ($inventario->take(2) as $item)
                        <div class="inventario-item">
                            <span class="cantidad">x1</span>

                            <div class="recompensa-visual">
                                @if ($item->recompensa?->ruta_imagen)
                                    <img src="{{ asset($item->recompensa->ruta_imagen) }}" alt="{{ $item->recompensa->nombre }}">
                                @else
                                    <span>🏅</span>
                                @endif
                            </div>

                            <p>{{ $item->recompensa?->nombre ?? 'Recompensa desbloqueada' }}</p>

                            <button type="button" class="btn-main btn-small" disabled>Usar</button>
                        </div>
                    @empty
                        <p class="panel-empty">Todavia no tienes objetos en inventario.</p>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
</div>
@endsection
