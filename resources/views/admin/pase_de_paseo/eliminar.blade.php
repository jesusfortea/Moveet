@extends('layouts.admin')

@section('title', 'Eliminar Pase de Paseo - Admin')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">ELIMINAR PASE DE PASEO</h1>
                <a href="{{ route('admin.pase_paseo') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            <div style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                <div style="background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-weight: 700; margin-bottom: 8px; font-size: 14px;">⚠️ Advertencia:</div>
                    <p style="margin: 0; font-size: 13px;">Esta acción es irreversible. Se eliminará el pase junto con todas sus recompensas asociadas.</p>
                </div>

                <form style="background: white; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre del pase</label>
                        <input type="text" value="{{ $pasedepaseo->nombre }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Ruta de imagen</label>
                        <input type="text" value="{{ $pasedepaseo->ruta_imagen }}" disabled style="width: 100%; padding: 10px 12px; border: 2px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: #f5f5f5; color: #666;">
                    </div>
                </form>

                <div style="background: #f9f9f9; border-left: 3px solid #c00; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        ¿Estás seguro de que deseas eliminar este pase de paseo? Una vez eliminado, no se puede recuperar.
                    </p>
                </div>

                <form action="{{ route('admin.pase_paseo.confirmar-eliminar', $pasedepaseo) }}" method="POST" style="display: flex; gap: 12px;">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" style="flex: 1; background: #c00; color: white; padding: 12px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#a00'" onmouseout="this.style.background='#c00'">
                        SÍ, ELIMINAR
                    </button>
                    <a href="{{ route('admin.pase_paseo') }}" style="flex: 1; background: #999; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; text-align: center; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#777'" onmouseout="this.style.background='#999'">
                        CANCELAR
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
