@extends('layouts.admin')

@section('title', 'Editar Pase de Paseo - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">EDITAR PASE DE PASEO</h1>
                <a href="{{ route('admin.pase_paseo') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
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

            <form action="{{ route('admin.pase_paseo.actualizar', $pasedepaseo) }}" method="POST" enctype="multipart/form-data" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre del pase <span style="color: #c00;">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $pasedepaseo->nombre) }}" required minlength="3" maxlength="255" placeholder="Ej: Pase de Paseo Deportivo" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('nombre'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('nombre') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 3 caracteres, máximo 255</span>
                    @endif
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Imagen del pase (opcional)</label>
                    @if($pasedepaseo->ruta_imagen)
                        <div style="margin-bottom: 8px;">
                            <img src="{{ asset($pasedepaseo->ruta_imagen) }}" alt="Imagen actual del pase" style="max-height: 120px; border-radius: 6px; border: 1px solid #c7d2d1;">
                        </div>
                    @endif
                    <input type="file" name="ruta_imagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ruta_imagen') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ruta_imagen') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('ruta_imagen'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('ruta_imagen') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Si no seleccionas una imagen, se mantiene la actual.</span>
                    @endif
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" style="flex: 1; background: #8FA8A6; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                        ACTUALIZAR
                    </button>
                    <a href="{{ route('admin.pase_paseo') }}" style="flex: 1; background: #999; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; text-align: center; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#777'" onmouseout="this.style.background='#999'">
                        CANCELAR
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
