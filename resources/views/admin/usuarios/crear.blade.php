@extends('layouts.admin')

@section('title', 'Crear Usuario')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">CREAR USUARIO</h1>
                <a href="{{ route('admin.usuarios') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                    VOLVER ATRÁS
                </a>
            </div>

            @if ($errors->any())
                <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.usuarios.guardar') }}" method="POST" style="background: #d0dbd9; padding: 40px; border-radius: 8px;">
                @csrf

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario</label>
                    <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">DNI</label>
                        <input type="text" name="dni" value="{{ old('dni') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario (usuario)</label>
                    <input type="text" name="username" value="{{ old('username') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Contraseña</label>
                        <input type="password" name="password" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Fecha nacimiento</label>
                    <input type="date" name="nacimiento" value="{{ old('nacimiento') }}" required style="width: 100%; padding: 12px 14px; border: 1px solid #999; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                </div>

                <button type="submit" style="background: #9db3b0; color: white; padding: 14px 40px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; transition: all 0.2s;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Crear Usuario
                </button>
            </form>
        </div>
    </div>
@endsection
