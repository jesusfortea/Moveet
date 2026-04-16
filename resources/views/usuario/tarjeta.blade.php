@extends('layouts.plantillaHome')

@section('title', 'Anadir tarjeta · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page usuario-page--card">
    <h1 class="usuario-page-title">Usuario tarjeta</h1>

    <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver</a>

    <h2 class="card-page-title">Añadir tarjeta</h2>

    <section class="panel-card panel-card-form">
        <div class="panel-header">
            <h3>Añadir tarjeta de credito</h3>
        </div>

        @if ($usuario->tarjetaBancaria)
            <div class="tarjeta-resumen">
                <p><strong>Tarjeta actual:</strong> {{ $usuario->tarjetaBancaria->numero_enmascarado }}</p>
                <p><strong>Caducidad:</strong> {{ $usuario->tarjetaBancaria->fecha_caducidad ?? 'No disponible' }}</p>
                @if ($usuario->tarjetaBancaria->esta_caducada)
                    <p class="tarjeta-status tarjeta-status--expired">Tu tarjeta esta caducada.</p>
                @else
                    <p class="tarjeta-status tarjeta-status--active">Tu tarjeta esta activa.</p>
                    <form method="POST" action="{{ route('usuario.tarjeta.destroy') }}" class="tarjeta-actions">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-main btn-danger">Eliminar tarjeta actual</button>
                    </form>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('usuario.tarjeta.store') }}" class="card-form">
            @csrf

            <label for="numero_tarjeta">Número de la tarjeta</label>
            <input id="numero_tarjeta" name="numero_tarjeta" type="text" value="{{ old('numero_tarjeta') }}" placeholder="9999 9999 9999 9999" required>

            <div class="card-form-row">
                <div>
                    <label for="fecha_caducidad">Fecha de caducidad</label>
                    <input id="fecha_caducidad" name="fecha_caducidad" type="text" value="{{ old('fecha_caducidad') }}" placeholder="00/00" required>
                </div>

                <div>
                    <label for="codigo_seguridad">Código de seguridad</label>
                    <input id="codigo_seguridad" name="codigo_seguridad" type="text" value="{{ old('codigo_seguridad') }}" placeholder="999" required>
                </div>
            </div>

            <label for="titular">Titular de la tarjeta</label>
            <input id="titular" name="titular" type="text" value="{{ old('titular', $usuario->name) }}" required>

            <button type="submit" class="btn-main">Añadir tarjeta</button>
        </form>
    </section>
</div>
@endsection
