@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 850px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; gap: 16px; flex-wrap: wrap;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">EDITAR PRODUCTO</h1>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('admin.tienda') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                        VOLVER ATRÁS
                    </a>
                    <a href="{{ route('admin.tienda.eliminar', $producto) }}" style="background: white; color: #8FA8A6; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#990000'" onmouseout="this.style.background='#cc0000'">
                        ELIMINAR PRODUCTO
                    </a>
                </div>
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

            <form action="{{ route('admin.tienda.actualizar', $producto) }}" method="POST" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre <span style="color: #c00;">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required maxlength="255" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nombre') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nombre') ? '#fff5f5' : '#fff' }};">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción <span style="color: #c00;">*</span></label>
                    <textarea name="descripcion" required maxlength="255" rows="4" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('descripcion') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('descripcion') ? '#fff5f5' : '#fff' }};">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Puntos necesarios <span style="color: #c00;">*</span></label>
                        <input type="number" name="puntos_necesarios" value="{{ old('puntos_necesarios', $producto->puntos_necesarios) }}" required min="0" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('puntos_necesarios') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('puntos_necesarios') ? '#fff5f5' : '#fff' }};">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nivel necesario <span style="color: #c00;">*</span></label>
                        <input type="number" name="nivel_necesario" value="{{ old('nivel_necesario', $producto->nivel_necesario) }}" required min="1" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nivel_necesario') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nivel_necesario') ? '#fff5f5' : '#fff' }};">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Ruta de imagen <span style="color: #c00;">*</span></label>
                    <input type="text" name="ruta_imagen" value="{{ old('ruta_imagen', $producto->ruta_imagen) }}" required maxlength="255" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('ruta_imagen') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('ruta_imagen') ? '#fff5f5' : '#fff' }};">
                </div>

                <div style="margin-bottom: 18px; display: flex; flex-direction: column; gap: 10px; background: #eef4f3; padding: 12px 14px; border-radius: 6px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #333; font-size: 13px;">
                        <input type="checkbox" name="premium" value="1" {{ old('premium', $producto->premium) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        Producto premium
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 600; color: #333; font-size: 13px;">
                        <input type="checkbox" name="visible_en_tienda" value="1" {{ old('visible_en_tienda', $producto->visible_en_tienda) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                        Visible en la tienda
                    </label>
                </div>

                <button type="submit" style="width: 100%; background: #9db3b0; color: white; padding: 11px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; box-sizing: border-box;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Actualizar Producto
                </button>
            </form>
        </div>
    </div>
@endsection