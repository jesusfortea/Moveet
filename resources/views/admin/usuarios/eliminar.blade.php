@extends('layouts.admin')

@section('title', 'Eliminar Usuario')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">ELIMINAR USUARIO</h1>
                <a href="{{ route('admin.usuarios') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            <div style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre de usuario</label>
                    <input type="text" value="{{ $user->name }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Correo electrónico</label>
                    <input type="email" value="{{ $user->email }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">DNI</label>
                        <input type="text" value="{{ $user->dni }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Teléfono</label>
                        <input type="text" value="{{ $user->telefono }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre de usuario (usuario)</label>
                    <input type="text" value="{{ $user->username }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha nacimiento</label>
                    <input type="date" value="{{ $user->nacimiento?->format('Y-m-d') }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid #999; border-radius: 4px; font-size: 13px; box-sizing: border-box; background-color: #f5f5f5;">
                </div>

                <h3 style="font-size: 1.1rem; font-weight: bold; text-align: center; margin: 25px 0 20px 0; color: #333;">¿Estás seguro de eliminar este usuario?</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <form action="{{ route('admin.usuarios.confirmar-eliminar', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 100%; background: #8FA8A6; color: white; padding: 11px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; box-sizing: border-box;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                            Sí
                        </button>
                    </form>
                    <a href="{{ route('admin.usuarios') }}" style="width: 100%; background: #9db3b0; color: white; padding: 11px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; text-decoration: none; display: flex; align-items: center; justify-content: center; box-sizing: border-box;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                        No
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
