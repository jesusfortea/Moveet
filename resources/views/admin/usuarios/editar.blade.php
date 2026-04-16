@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; width: 100%; padding: 20px 0;">
        <div style="width: 90%; max-width: 800px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">EDITAR USUARIO</h1>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.usuarios') }}" style="background: #8FA8A6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#7a9a98'" onmouseout="this.style.background='#8FA8A6'">
                        VOLVER ATRÁS
                    </a>
                    <a href="{{ route('admin.usuarios.eliminar', $user) }}" style="background: #cc0000; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#990000'" onmouseout="this.style.background='#cc0000'">
                        ELIMINAR USUARIO
                    </a>
                </div>
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

            <form action="{{ route('admin.usuarios.actualizar', $user) }}" method="POST" style="background: #d0dbd9; padding: 40px; border-radius: 8px;" novalidate>
                @csrf
                @method('PUT')

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('name') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @if ($errors->has('name'))
                        <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('email') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @if ($errors->has('email'))
                        <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">DNI</label>
                        <input type="text" name="dni" value="{{ old('dni', $user->dni) }}" required maxlength="20" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('dni') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        @if ($errors->has('dni'))
                            <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('dni') }}</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Teléfono</label>
                        <input type="tel" name="telefono" value="{{ old('telefono', $user->telefono) }}" required maxlength="20" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('telefono') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        @if ($errors->has('telefono'))
                            <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('telefono') }}</span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Nombre de usuario (usuario)</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required maxlength="255" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('username') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @if ($errors->has('username'))
                        <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('username') }}</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" minlength="6" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('password') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        @if ($errors->has('password'))
                            <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" minlength="6" style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('password') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">Fecha nacimiento</label>
                    <input type="date" name="nacimiento" value="{{ old('nacimiento', $user->nacimiento?->format('Y-m-d')) }}" required style="width: 100%; padding: 12px 14px; border: 2px solid {{ $errors->has('nacimiento') ? '#cc0000' : '#999' }}; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                    @if ($errors->has('nacimiento'))
                        <span style="color: #cc0000; font-size: 12px; margin-top: 4px; display: block;">{{ $errors->first('nacimiento') }}</span>
                    @endif
                </div>

                <button type="submit" style="width: 100%; background: #9db3b0; color: white; padding: 14px 40px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px; transition: all 0.2s; box-sizing: border-box;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Actualizar Usuario
                </button>
            </form>
        </div>
    </div>
@endsection
