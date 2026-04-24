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

    @if (($tarjetaCaducada ?? false) === true)
        <div class="usuario-alert usuario-alert-warning">Tu tarjeta actual esta caducada. Actualizala para seguir usandola.</div>
    @endif

    <div class="usuario-grid">
        <form class="perfil-card perfil-form" method="POST" action="{{ route('usuario.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="perfil-avatar-wrap">
                <div class="perfil-avatar">
                    @if ($usuario->ruta_imagen_url)
                        <img src="{{ $usuario->ruta_imagen_url }}" alt="Foto de perfil">
                    @else
                        <span>{{ strtoupper(substr($usuario->name, 0, 1)) }}</span>
                    @endif
                </div>

                <label class="btn-link file-trigger" for="ruta_imagen">Cambiar imagen</label>
                <input id="ruta_imagen" name="ruta_imagen" type="file" accept="image/*" class="file-input">
            </div>

            <div class="perfil-fields">
                <label for="name">Cambiar nombre</label>
                <input id="name" name="name" type="text" value="{{ old('name', $usuario->name) }}" required>

                <label for="email">Cambiar correo</label>
                <input id="email" name="email" type="email" value="{{ old('email', $usuario->email) }}" required>

                <label for="telefono">Cambiar telefono</label>
                <input id="telefono" name="telefono" type="text" value="{{ old('telefono', $usuario->telefono) }}" placeholder="Sin telefono">
            </div>

            <button type="submit" class="btn-main">Guardar</button>
        </form>

        <section class="usuario-main">
            <article class="panel-card">
                <div class="panel-header">
                    <h2>Racha diaria</h2>
                </div>

                <div class="tarjeta-row">
                    <span>Racha actual</span>
                    <strong>{{ $usuario->current_streak }} dias</strong>
                </div>
                <div class="tarjeta-row">
                    <span>Mejor racha</span>
                    <strong>{{ $usuario->longest_streak }} dias</strong>
                </div>
                <div class="tarjeta-row" style="margin-bottom: 12px;">
                    <span>Congeladores</span>
                    <strong>{{ $usuario->streak_freezes }}</strong>
                </div>

                <form method="POST" action="{{ route('usuario.streak.freeze.buy') }}" class="tarjeta-actions">
                    @csrf
                    <button type="submit" class="btn-main">Comprar congelador ({{ number_format($streakFreezeCost, 0, ',', '.') }} ptos)</button>
                </form>

                @if($usuario->premium)
                    <p class="panel-empty" style="margin-top: 10px;">Premium incluye congeladores mensuales gratis.</p>
                @endif
            </article>

            <article class="panel-card">
                <div class="panel-header">
                    <h2>Tarjetas</h2>
                    <a class="btn-link" href="{{ route('usuario.tarjeta.create') }}">+ Añadir tarjeta</a>
                </div>

                @if ($tarjeta)
                    <div class="tarjeta-row">
                        <span>{{ $tarjeta->titular }}</span>
                        <span>{{ $tarjeta->numero_enmascarado }}</span>
                    </div>

                    <div class="tarjeta-meta">
                        <span>Caducidad: {{ $tarjeta->fecha_caducidad ?? 'No disponible' }}</span>
                        @if (($tarjetaCaducada ?? false) === true)
                            <strong class="tarjeta-status tarjeta-status--expired">Caducada</strong>
                        @else
                            <strong class="tarjeta-status tarjeta-status--active">Activa</strong>
                        @endif
                    </div>

                    @if (($tarjetaCaducada ?? false) === false)
                        <form method="POST" action="{{ route('usuario.tarjeta.destroy') }}" class="tarjeta-actions">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-main btn-danger">Eliminar tarjeta</button>
                        </form>
                    @endif
                @else
                    <p class="panel-empty">Aún no tienes una tarjeta registrada.</p>
                @endif
            </article>

            <article class="panel-card inventario-card">
                <div class="panel-header panel-header-stack">
                    <h2>Inventario</h2>
                    <a class="btn-link" href="{{ route('usuario.inventario') }}">Ver todos &gt;</a>
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
