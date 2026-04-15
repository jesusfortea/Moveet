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

            <form action="{{ route('admin.usuarios.confirmar-eliminar', $user) }}" method="POST" style="background: #d0dbd9; padding: 40px; border-radius: 8px;">
                @csrf
                @method('DELETE')

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario</label>
                    <input type="text" name="name" value="{{ $user->name }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Correo electrónico</label>
                    <input type="email" name="email" value="{{ $user->email }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">DNI</label>
                        <input type="text" name="dni" value="{{ $user->dni }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Teléfono</label>
                        <input type="text" name="telefono" value="{{ $user->telefono }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario (usuario)</label>
                    <input type="text" name="username" value="{{ $user->username }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Contraseña</label>
                        <input type="password" name="password" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Fecha nacimiento</label>
                    <input type="date" name="nacimiento" value="{{ $user->nacimiento?->format('Y-m-d') }}" disabled style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <button type="submit" style="background: #9db3b0; color: white; padding: 14px 40px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; transition: all 0.2s;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Eliminar Usuario
                </button>
            </form>
        </div>
    </div>
@endsection
