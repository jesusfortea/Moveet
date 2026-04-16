@extends('layouts.admin')

@section('title', 'Eliminar Evento - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">ELIMINAR EVENTO</h1>
                <a href="{{ route('admin.eventos') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            <div style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                <div style="background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-weight: 700; margin-bottom: 8px; font-size: 14px;">⚠️ Advertencia:</div>
                    <p style="margin: 0; font-size: 13px;">Esta acción es irreversible. Se eliminará todo junto con sus misiones asociadas.</p>
                </div>

                <form style="background: white; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre del evento</label>
                        <input type="text" value="{{ $evento->nombre }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Descripción</label>
                        <textarea disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666; font-family: Arial, sans-serif; min-height: 80px; resize: vertical;">{{ $evento->descripcion }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada X</label>
                            <input type="number" value="{{ $evento->ejeX }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Coordenada Y</label>
                            <input type="number" value="{{ $evento->ejeY }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Dirección</label>
                        <input type="text" value="{{ $evento->direccion }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha de inicio</label>
                            <input type="text" value="{{ $evento->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha de fin</label>
                            <input type="text" value="{{ $evento->fecha_fin?->format('d/m/Y') ?? 'N/A' }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                        </div>
                    </div>
                </form>

                <div style="background: #f9f9f9; border-left: 3px solid #c00; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 13px; color: #333; font-weight: 600;">¿Estás seguro de que deseas eliminar este evento?</p>
                </div>

                <form action="{{ route('admin.eventos.confirmar-eliminar', $evento) }}" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('admin.eventos') }}" style="background: #8FA8A6; color: white; padding: 12px; border-radius: 4px; text-decoration: none; text-align: center; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">NO, VOLVER</a>
                    <button type="submit" style="background: #c00; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 13px;" onmouseover="this.style.background='#a00'" onmouseout="this.style.background='#c00'">SÍ, ELIMINAR</button>
                </form>
            </div>
        </div>
    </div>
@endsection
