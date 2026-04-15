@extends('layouts.plantillaHome')

@section('title', 'Anadir tarjeta · Moveet')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/usuario.css') }}">
@endpush

@section('content')
<div class="usuario-page usuario-page--card">
    <h1 class="usuario-page-title">Usuario tarjeta</h1>

    <a class="volver-link" href="{{ route('usuario.index') }}">&lt; Volver</a>

    <h2 class="card-page-title">Anadir tarjeta</h2>

    <section class="panel-card panel-card-form">
        <div class="panel-header">
            <h3>Anadir tarjeta de credito</h3>
        </div>

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('usuario.tarjeta.store') }}" class="card-form">
            @csrf

            <label for="numero_tarjeta">Numero de la tarjeta</label>
            <input id="numero_tarjeta" name="numero_tarjeta" type="text" value="{{ old('numero_tarjeta') }}" placeholder="9999 9999 9999 9999" required>

            <div class="card-form-row">
                <div>
                    <label for="fecha_caducidad">Fecha de caducidad</label>
                    <input id="fecha_caducidad" name="fecha_caducidad" type="text" value="{{ old('fecha_caducidad') }}" placeholder="00/00" required>
                </div>

                <div>
                    <label for="codigo_seguridad">Codigo de seguridad</label>
                    <input id="codigo_seguridad" name="codigo_seguridad" type="text" value="{{ old('codigo_seguridad') }}" placeholder="999" required>
                </div>
            </div>

            <label for="titular">Titular de la tarjeta</label>
            <input id="titular" name="titular" type="text" value="{{ old('titular', $usuario->name) }}" required>

            <button type="submit" class="btn-main">Anadir tarjeta</button>
        </form>
    </section>
</div>
@endsection
