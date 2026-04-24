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

        @if (($tarjetas ?? collect())->isNotEmpty())
            <div class="tarjeta-resumen">
                @foreach ($tarjetas as $tarjeta)
                    <p><strong>Tarjeta:</strong> {{ $tarjeta->numero_enmascarado }}</p>
                    <p><strong>Caducidad:</strong> {{ $tarjeta->fecha_caducidad ?? 'No disponible' }}</p>

                    @if ($tarjeta->esta_caducada)
                        <p class="tarjeta-status tarjeta-status--expired">Esta tarjeta esta caducada.</p>
                    @else
                        <p class="tarjeta-status tarjeta-status--active">Esta tarjeta esta activa.</p>
                    @endif

                    <form method="POST" action="{{ route('usuario.tarjeta.destroy', $tarjeta) }}" class="tarjeta-actions">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-main btn-danger">Eliminar tarjeta</button>
                    </form>
                @endforeach
            </div>
        @endif

        @if ($errors->any())
            <div class="error-box">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('usuario.tarjeta.store') }}" class="card-form" id="card-form">
            @csrf

            <label for="numero_tarjeta">Numero de la tarjeta</label>
            <input id="numero_tarjeta" name="numero_tarjeta" type="text" value="{{ old('numero_tarjeta') }}" placeholder="9999 9999 9999 9999" required>

            <div class="card-form-row">
                <div>
                    <label for="fecha_caducidad">Fecha de caducidad <small>(MM/YY)</small></label>
                    <input id="fecha_caducidad" name="fecha_caducidad" type="text" value="{{ old('fecha_caducidad') }}" placeholder="12/25" maxlength="5" required>
                </div>

                <div>
                    <label for="codigo_seguridad">Codigo de seguridad</label>
                    <input id="codigo_seguridad" name="codigo_seguridad" type="text" value="{{ old('codigo_seguridad') }}" placeholder="999" maxlength="4" required>
                </div>
            </div>

            <label for="titular">Titular de la tarjeta</label>
            <input id="titular" name="titular" type="text" value="{{ old('titular', $usuario->name) }}" required>

            <button type="submit" class="btn-main">Añadir tarjeta</button>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha_caducidad');
    const codigoInput = document.getElementById('codigo_seguridad');
    const numeroInput = document.getElementById('numero_tarjeta');

    fechaInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });

    codigoInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    numeroInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += ' ';
            }
            formattedValue += value[i];
        }
        e.target.value = formattedValue;
    });

    document.getElementById('card-form').addEventListener('submit', function(e) {
        const fecha = fechaInput.value;
        const regex = /^(0[1-9]|1[0-2])\/(\d{2})$/;

        if (!regex.test(fecha)) {
            e.preventDefault();
            alert('Formato de fecha incorrecto. Usa MM/YY (ej: 12/25)');
            fechaInput.focus();
            return;
        }

        const codigo = codigoInput.value;
        if (codigo.length < 3 || codigo.length > 4) {
            e.preventDefault();
            alert('El codigo de seguridad debe tener 3 o 4 digitos');
            codigoInput.focus();
        }
    });
});
</script>
@endsection
