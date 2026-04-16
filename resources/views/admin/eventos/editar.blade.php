@extends('layouts.admin')

@section('title', 'Editar Evento - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">EDITAR EVENTO</h1>
                <a href="{{ route('admin.eventos') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            @if ($errors->any())
                <div style="background: #fee; border: 2px solid #c00; color: #600; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-weight: 700; margin-bottom: 10px; font-size: 14px;">⚠️ Se encontraron los siguientes errores:</div>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                        @foreach ($errors->all() as $error)
                            <li style="margin-bottom: 5px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.eventos.actualizar', $evento) }}" method="POST" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre del evento <span style="color: #c00;">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $evento->nombre) }}" required minlength="3" maxlength="255" placeholder="Ej: Carrera Deportiva del Parque" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('nombre'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('nombre') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 3 caracteres, máximo 255</span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción <span style="color: #c00;">*</span></label>
                    <textarea name="descripcion" required minlength="10" maxlength="1000" placeholder="Describe los detalles del evento" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('descripcion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('descripcion') ? '#fff5f5' : '#fff' }}; font-family: Arial, sans-serif; min-height: 80px; resize: vertical;">{{ old('descripcion', $evento->descripcion) }}</textarea>
                    @if ($errors->has('descripcion'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('descripcion') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 10 caracteres, máximo 1000</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada X <span style="color: #c00;">*</span></label>
                        <input type="number" name="ejeX" value="{{ old('ejeX', $evento->ejeX) }}" required step="any" min="-180" max="180" placeholder="2.180559074338345" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ejeX') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ejeX') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('ejeX'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('ejeX') }}</span>
                        @else
                            <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">-180 a 180</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada Y <span style="color: #c00;">*</span></label>
                        <input type="number" name="ejeY" value="{{ old('ejeY', $evento->ejeY) }}" required step="any" min="-90" max="90" placeholder="41.391197225342204" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ejeY') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ejeY') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('ejeY'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('ejeY') }}</span>
                        @else
                            <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">-90 a 90</span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Dirección <span style="color: #c00;">*</span></label>
                    <input type="text" name="direccion" value="{{ old('direccion', $evento->direccion) }}" required minlength="5" maxlength="500" placeholder="Calle Principal 123, Parque Central" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('direccion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('direccion') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('direccion'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('direccion') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 5 caracteres, máximo 500</span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha de inicio <span style="color: #c00;">*</span></label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $evento->fecha_inicio?->format('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('fecha_inicio') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('fecha_inicio') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('fecha_inicio'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('fecha_inicio') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Formato: YYYY-MM-DD</span>
                    @endif
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha de fin <span style="color: #c00;">*</span></label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $evento->fecha_fin?->format('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('fecha_fin') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('fecha_fin') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('fecha_fin'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('fecha_fin') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Debe ser igual o posterior a la fecha de inicio</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <a href="{{ route('admin.eventos') }}" style="background: #999; color: white; padding: 12px; border-radius: 4px; text-decoration: none; text-align: center; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#777'" onmouseout="this.style.background='#999'">CANCELAR</a>
                    <button type="submit" style="background: #8FA8A6; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">ACTUALIZAR EVENTO</button>
                </div>
            </form>
        </div>
    </div>
@endsection
