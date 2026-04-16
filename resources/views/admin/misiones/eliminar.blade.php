@extends('layouts.admin')

@section('title', 'Eliminar Misión - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">ELIMINAR MISIÓN</h1>
                <a href="{{ route('admin.misiones') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            <div style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                <div style="background: #ffeaea; border: 2px solid #c00; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <p style="color: #c00; margin: 0; font-weight: 600; font-size: 14px;">⚠️ Advertencia: Esta acción es irreversible</p>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Nombre:</label>
                    <input type="text" value="{{ $mision->nombre }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Evento:</label>
                    <input type="text" value="{{ $mision->evento->nombre ?? 'N/A' }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Descripción:</label>
                    <textarea disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666; min-height: 80px; resize: vertical;">{{ $mision->descripcion }}</textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Metros requeridos:</label>
                    <input type="text" value="{{ $mision->metros_requeridos }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Coordenada X:</label>
                        <input type="text" value="{{ $mision->ejeX }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">Coordenada Y:</label>
                        <input type="text" value="{{ $mision->ejeY }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>
                </div>

                <p style="color: #c00; margin: 20px 0; font-weight: 600; font-size: 14px;">¿Estás seguro de que deseas eliminar esta misión?</p>

                <form action="{{ route('admin.misiones.confirmar-eliminar', $mision) }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('admin.misiones') }}" style="background: #8FA8A6; color: white; padding: 12px; border-radius: 4px; text-decoration: none; text-align: center; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">NO, VOLVER</a>
                    <button type="submit" style="background: #c00; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#a00'" onmouseout="this.style.background='#c00'">SÍ, ELIMINAR</button>
                </form>
            </div>
        </div>
    </div>
@endsection
