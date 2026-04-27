@extends('layouts.plantillaHome')

@section('title', 'Editar ruta · Moveet')

@section('content')
<div style="max-width: 980px; margin: 0 auto; padding: 24px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; margin: 0; color: #1E2A28;">Editar ruta</h1>
            <p style="margin: 6px 0 0; color: #516260; font-weight: 600;">Puedes cambiar el texto, dificultad, visibilidad y acceso premium. El recorrido original se mantiene.</p>
        </div>
        <a href="{{ route('rutas.index') }}" style="background: #8FA8A6; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700;">Volver</a>
    </div>

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #e4b8b8; color: #7b1f1f; padding: 12px 14px; border-radius: 8px; margin-bottom: 14px;">
            <ul style="margin: 0; padding-left: 16px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.25fr 0.75fr; gap: 18px; align-items: start;">
        <form method="POST" action="{{ route('rutas.actualizar', $ruta) }}" style="background: #d0dbd9; border-radius: 16px; padding: 18px;">
            @csrf
            @method('PUT')

            <div style="display: grid; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 4px;">Titulo</label>
                    <input type="text" name="titulo" value="{{ old('titulo', $ruta->titulo) }}" required maxlength="150" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                </div>

                <div>
                    <label style="display: block; font-weight: 700; margin-bottom: 4px;">Descripcion</label>
                    <textarea name="descripcion" maxlength="1000" rows="3" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">{{ old('descripcion', $ruta->descripcion) }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Dificultad</label>
                        <select name="dificultad" required style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                            <option value="facil" {{ old('dificultad', $ruta->dificultad) === 'facil' ? 'selected' : '' }}>Facil</option>
                            <option value="media" {{ old('dificultad', $ruta->dificultad) === 'media' ? 'selected' : '' }}>Media</option>
                            <option value="dificil" {{ old('dificultad', $ruta->dificultad) === 'dificil' ? 'selected' : '' }}>Dificil</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Puntos de recompensa</label>
                        <input type="number" name="puntos_recompensa" value="{{ old('puntos_recompensa', $ruta->puntos_recompensa) }}" required min="20" max="500" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Nivel minimo requerido</label>
                        <input type="number" name="min_nivel" value="{{ old('min_nivel', $ruta->min_nivel) }}" required min="1" max="100" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; margin-bottom: 4px;">Estado</label>
                        <select name="publicado" style="width: 100%; border: 1px solid #9cb3b0; border-radius: 10px; padding: 10px; background: white;">
                            <option value="1" {{ old('publicado', $ruta->publicado) ? 'selected' : '' }}>Publicado</option>
                            <option value="0" {{ !old('publicado', $ruta->publicado) ? 'selected' : '' }}>Oculto</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700;">
                        <input type="checkbox" name="premium_only" value="1" {{ old('premium_only', $ruta->premium_only) ? 'checked' : '' }}>
                        Solo premium
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700;">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $ruta->activo) ? 'checked' : '' }}>
                        Activa
                    </label>
                </div>

                <button type="submit" style="background: #8FA8A6; color: white; border: none; border-radius: 10px; padding: 13px 16px; font-weight: 800; cursor: pointer; font-size: 14px;">Guardar cambios</button>
            </div>
        </form>

        <aside style="display: grid; gap: 12px; position: sticky; top: 18px;">
            <div style="background: #eef4f3; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                <h2 style="margin: 0 0 10px; font-size: 1rem; color: #1E2A28;">Recorrido fijo</h2>
                <p style="margin: 0 0 10px; color: #5e6f6d; font-size: 13px;">El trazado no se cambia desde aqui para no invalidar las completaciones anteriores.</p>
                <textarea readonly rows="9" style="width: 100%; border: 1px solid #c8d4d1; border-radius: 10px; padding: 10px; font-family: monospace; background: #f8fbfb; font-size: 12px;">{{ json_encode($ruta->ruta_geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
            </div>

            <div style="background: #ffffff; border: 1px solid #d2dedc; border-radius: 12px; padding: 14px;">
                <h3 style="margin: 0 0 10px; font-size: 0.98rem; color: #1E2A28;">Consejo</h3>
                <p style="margin: 0; color: #5e6f6d; font-size: 13px; line-height: 1.5;">Edita el texto, el premio y si es premium para optimizar conversiones. Si un recorrido cambia de verdad, lo mejor es crear una ruta nueva.</p>
            </div>
        </aside>
    </div>
</div>
@endsection
