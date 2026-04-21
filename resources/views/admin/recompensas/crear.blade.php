@extends('layouts.admin')

@section('title', 'Crear Recompensa - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">CREAR RECOMPENSA</h1>
                <a href="{{ route('admin.recompensas') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
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

            <form action="{{ route('admin.recompensas.guardar') }}" method="POST" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Pase de Paseo <span style="color: #c00;">*</span></label>
                    <select name="pase_de_paseo_id" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('pase_de_paseo_id') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('pase_de_paseo_id') ? '#fff5f5' : '#fff' }};">
                        <option value="">-- Selecciona un pase --</option>
                        @foreach ($pases as $pase)
                            <option value="{{ $pase->id }}" @if(old('pase_de_paseo_id') == $pase->id) selected @endif>{{ $pase->nombre }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('pase_de_paseo_id'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('pase_de_paseo_id') }}</span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre <span style="color: #c00;">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required minlength="3" maxlength="255" placeholder="Ej: Moneda de Oro" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('nombre'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('nombre') }}</span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción <span style="color: #c00;">*</span></label>
                    <textarea name="descripcion" required minlength="10" maxlength="1000" placeholder="Describe la recompensa" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('descripcion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('descripcion') ? '#fff5f5' : '#fff' }}; font-family: Arial, sans-serif; min-height: 80px; resize: vertical;">{{ old('descripcion') }}</textarea>
                    @if ($errors->has('descripcion'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('descripcion') }}</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Puntos Necesarios <span style="color: #c00;">*</span></label>
                        <input type="number" name="puntos_necesarios" value="{{ old('puntos_necesarios') }}" required min="1" max="999999" placeholder="100" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('puntos_necesarios') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('puntos_necesarios') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('puntos_necesarios'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('puntos_necesarios') }}</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nivel Necesario <span style="color: #c00;">*</span></label>
                        <input type="number" name="nivel_necesario" value="{{ old('nivel_necesario') }}" required min="1" max="100" placeholder="1" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nivel_necesario') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nivel_necesario') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('nivel_necesario'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('nivel_necesario') }}</span>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Tipo <span style="color: #c00;">*</span></label>
                        <select name="tipo" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('tipo') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('tipo') ? '#fff5f5' : '#fff' }};">
                            <option value="">-- Selecciona un tipo --</option>
                            <option value="normal" @if(old('tipo') === 'normal') selected @endif>Normal</option>
                            <option value="especial" @if(old('tipo') === 'especial') selected @endif>Especial</option>
                            <option value="legendaria" @if(old('tipo') === 'legendaria') selected @endif>Legendaria</option>
                        </select>
                        @if ($errors->has('tipo'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('tipo') }}</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Premium</label>
                        <div style="display: flex; align-items: center; height: 36px;">
                            <input type="checkbox" name="premium" value="1" @if(old('premium')) checked @endif style="width: 18px; height: 18px; cursor: pointer;">
                            <span style="margin-left: 8px; color: #666; font-size: 13px;">Marcar como premium</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Ruta de Imagen <span style="color: #c00;">*</span></label>
                    <input type="text" name="ruta_imagen" value="{{ old('ruta_imagen') }}" required minlength="5" maxlength="500" placeholder="/img/recompensas/moneda.png" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ruta_imagen') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ruta_imagen') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('ruta_imagen'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('ruta_imagen') }}</span>
                    @endif
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; background: #8FA8A6; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                        GUARDAR
                    </button>
                    <a href="{{ route('admin.recompensas') }}" style="flex: 1; background: #999; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; text-align: center; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#777'" onmouseout="this.style.background='#999'">
                        CANCELAR
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
