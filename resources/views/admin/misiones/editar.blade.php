@extends('layouts.admin')

@section('title', 'Editar Misión - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">EDITAR MISIÓN</h1>
                <a href="{{ route('admin.misiones') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
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

            <form action="{{ route('admin.misiones.actualizar', $mision) }}" method="POST" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf
                @method('PUT')

        <!-- Evento -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Evento <span style="color: #c00;">*</span></label>
            <select name="evento_id" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('evento_id') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('evento_id') ? '#fff5f5' : '#fff' }};">
                <option value="">-- Selecciona un evento --</option>
                @foreach ($eventos as $evento)
                    <option value="{{ $evento->id }}" {{ old('evento_id', $mision->evento_id) == $evento->id ? 'selected' : '' }}>{{ $evento->nombre }}</option>
                @endforeach
            </select>
            @if ($errors->has('evento_id'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('evento_id') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Selecciona el evento asociado</span>
            @endif
        </div>

        <!-- Nombre -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre de la misión <span style="color: #c00;">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $mision->nombre) }}" required minlength="3" maxlength="255" placeholder="Mínimo 3 caracteres" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
            @if ($errors->has('nombre'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('nombre') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 3 caracteres, máximo 255</span>
            @endif
        </div>

        <!-- Descripción -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción <span style="color: #c00;">*</span></label>
            <textarea name="descripcion" required minlength="10" maxlength="1000" placeholder="Mínimo 10 caracteres" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('descripcion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('descripcion') ? '#fff5f5' : '#fff' }}; font-family: Arial, sans-serif; min-height: 80px; resize: vertical;">{{ old('descripcion', $mision->descripcion) }}</textarea>
            @if ($errors->has('descripcion'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('descripcion') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 10 caracteres, máximo 1000</span>
            @endif
        </div>

        <!-- Metros Requeridos -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Metros requeridos <span style="color: #c00;">*</span></label>
            <input type="number" name="metros_requeridos" value="{{ old('metros_requeridos', $mision->metros_requeridos) }}" required min="1" max="99999" placeholder="1000" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('metros_requeridos') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('metros_requeridos') ? '#fff5f5' : '#fff' }};">
            @if ($errors->has('metros_requeridos'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('metros_requeridos') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Entre 1 y 99.999 metros</span>
            @endif
        </div>

        <!-- Coordenadas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada X <span style="color: #c00;">*</span></label>
                <input type="number" name="ejeX" value="{{ old('ejeX', $mision->ejeX) }}" required step="0.0001" min="-180" max="180" placeholder="-3.7038" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ejeX') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ejeX') ? '#fff5f5' : '#fff' }};">
                @if ($errors->has('ejeX'))
                    <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('ejeX') }}</span>
                @else
                    <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">-180 a 180</span>
                @endif
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada Y <span style="color: #c00;">*</span></label>
                <input type="number" name="ejeY" value="{{ old('ejeY', $mision->ejeY) }}" required step="0.0001" min="-90" max="90" placeholder="40.4168" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ejeY') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ejeY') ? '#fff5f5' : '#fff' }};">
                @if ($errors->has('ejeY'))
                    <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('ejeY') }}</span>
                @else
                    <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">-90 a 90</span>
                @endif
            </div>
        </div>

        <!-- Dirección -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Dirección <span style="color: #c00;">*</span></label>
            <input type="text" name="direccion" value="{{ old('direccion', $mision->direccion) }}" required minlength="5" maxlength="500" placeholder="Calle Principal 123, Madrid" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('direccion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('direccion') ? '#fff5f5' : '#fff' }};">
            @if ($errors->has('direccion'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('direccion') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 5 caracteres, máximo 500</span>
            @endif
        </div>

        <!-- Puntos -->
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Puntos de recompensa <span style="color: #c00;">*</span></label>
            <input type="number" name="puntos" value="{{ old('puntos', $mision->puntos) }}" required min="1" max="9999" placeholder="100" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('puntos') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('puntos') ? '#fff5f5' : '#fff' }};">
            @if ($errors->has('puntos'))
                <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('puntos') }}</span>
            @else
                <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Entre 1 y 9.999 puntos</span>
            @endif
        </div>

        <!-- Checkboxes -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center;">
                <input type="checkbox" name="premium" id="premium" value="1" {{ old('premium', $mision->premium) ? 'checked' : '' }} style="margin-right: 8px;">
                <label for="premium" style="color: #333; font-size: 13px; cursor: pointer;">Misión Premium</label>
            </div>
            <div style="display: flex; align-items: center;">
                <input type="checkbox" name="semanal" id="semanal" value="1" {{ old('semanal', $mision->semanal) ? 'checked' : '' }} style="margin-right: 8px;">
                <label for="semanal" style="color: #333; font-size: 13px; cursor: pointer;">Misión Semanal</label>
            </div>
        </div>

        <!-- Botones -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <a href="{{ route('admin.misiones') }}" style="background: #999; color: white; padding: 12px; border-radius: 4px; text-decoration: none; text-align: center; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#777'" onmouseout="this.style.background='#999'">CANCELAR</a>
            <button type="submit" style="background: #8FA8A6; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">ACTUALIZAR MISIÓN</button>
        </div>
            </form>
        </div>
    </div>
@endsection
