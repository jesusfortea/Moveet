@extends('layouts.admin')

@section('title', 'Crear Recompensa')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 850px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 16px; flex-wrap: wrap;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">CREAR RECOMPENSA</h1>
                <a href="{{ route('admin.recompensas') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            @if ($errors->any())
                <div style="background: #fee; border: 2px solid #c00; color: #600; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-weight: 700; margin-bottom: 10px; font-size: 14px;">Se encontraron los siguientes errores:</div>
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                        @foreach ($errors->all() as $error)
                            <li style="margin-bottom: 5px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.recompensas.guardar') }}" method="POST" enctype="multipart/form-data" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre <span style="color: #c00;">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción <span style="color: #c00;">*</span></label>
                    <textarea name="descripcion" required maxlength="255" rows="4" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('descripcion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('descripcion') ? '#fff5f5' : '#fff' }};">{{ old('descripcion') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Tipo <span style="color: #c00;">*</span></label>
                        <select id="tipo_recompensa" name="tipo" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('tipo') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('tipo') ? '#fff5f5' : '#fff' }};">
                            <option value="tienda" {{ old('tipo', 'tienda') === 'tienda' ? 'selected' : '' }}>Tienda</option>
                            <option value="pase_de_paseo" {{ old('tipo') === 'pase_de_paseo' ? 'selected' : '' }}>Pase de paseo</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Imagen de recompensa <span style="color: #c00;">*</span></label>
                        <input type="file" name="ruta_imagen" required accept="image/*" style="width: 100%; padding: 9px 12px; border: 2px solid {{ $errors->has('ruta_imagen') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ruta_imagen') ? '#fff5f5' : '#fff' }};">
                    </div>
                </div>

                <div id="pase_selector_wrap" style="margin-bottom: 15px; display: none;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Pase de paseo asociado <span style="color: #c00;">*</span></label>
                    <select id="pase_de_paseo_id" name="pase_de_paseo_id" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('pase_de_paseo_id') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('pase_de_paseo_id') ? '#fff5f5' : '#fff' }};">
                        <option value="">Selecciona un pase</option>
                        @foreach($pases as $pase)
                            <option value="{{ $pase->id }}" {{ (string) old('pase_de_paseo_id') === (string) $pase->id ? 'selected' : '' }}>
                                {{ $pase->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('pase_de_paseo_id'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('pase_de_paseo_id') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Obligatorio para recompensas tipo Pase de paseo.</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Puntos necesarios <span style="color: #c00;">*</span></label>
                        <input type="number" name="puntos_necesarios" value="{{ old('puntos_necesarios') }}" required min="0" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('puntos_necesarios') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('puntos_necesarios') ? '#fff5f5' : '#fff' }};">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nivel necesario <span style="color: #c00;">*</span></label>
                        <input type="number" name="nivel_necesario" value="{{ old('nivel_necesario', 1) }}" required min="1" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nivel_necesario') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nivel_necesario') ? '#fff5f5' : '#fff' }};">
                    </div>
                </div>

                <div style="margin-bottom: 18px; display: flex; flex-direction: column; gap: 10px; background: #eef4f3; padding: 12px 14px; border-radius: 6px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #333; font-size: 13px;">
                        <input type="checkbox" name="premium" value="1" {{ old('premium') ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        Producto premium
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #333; font-size: 13px;">
                        <input type="checkbox" name="visible_en_tienda" value="1" {{ old('visible_en_tienda') ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        Visible en la tienda
                    </label>
                </div>

                <button type="submit" style="width: 100%; background: #9db3b0; color: white; padding: 11px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; box-sizing: border-box;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Crear Recompensa
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const tipo = document.getElementById('tipo_recompensa');
            const wrap = document.getElementById('pase_selector_wrap');
            const paseSelect = document.getElementById('pase_de_paseo_id');

            if (!tipo || !wrap || !paseSelect) {
                return;
            }

            const refresh = function () {
                const isPase = tipo.value === 'pase_de_paseo';
                wrap.style.display = isPase ? 'block' : 'none';
                paseSelect.required = isPase;
                if (!isPase) {
                    paseSelect.value = '';
                }
            };

            tipo.addEventListener('change', refresh);
            refresh();
        })();
    </script>
@endsection