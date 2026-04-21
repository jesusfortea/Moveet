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
                    <a href="{{ route('admin.usuarios.eliminar', $user) }}" style="background: white; color: #8FA8A6; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; transition: all 0.2s;" onmouseover="this.style.background='#990000'" onmouseout="this.style.background='#cc0000'">
                        ELIMINAR USUARIO
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

            <form action="{{ route('admin.usuarios.actualizar', $user) }}" method="POST" style="background: #d0dbd9; padding: 25px; border-radius: 8px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre <span style="color: #c00;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required minlength="3" maxlength="255" placeholder="Mínimo 3 caracteres" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('name') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('name') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('name'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('name') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Mínimo 3 caracteres, máximo 255</span>
                    @endif
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Correo electrónico <span style="color: #c00;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="ejemplo@correo.com" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('email') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('email') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('email'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('email') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Debe ser un correo electrónico válido</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">DNI <span style="color: #c00;">*</span></label>
                        <input type="text" name="dni" value="{{ old('dni', $user->dni) }}" required maxlength="9" placeholder="12345678A" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('dni') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('dni') ? '#fff5f5' : '#fff' }}; text-transform: uppercase;">
                        @if ($errors->has('dni'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('dni') }}</span>
                        @else
                            <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">8 números + 1 letra</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Teléfono <span style="color: #c00;">*</span></label>
                        <input type="tel" name="telefono" value="{{ old('telefono', $user->telefono) }}" required maxlength="15" placeholder="+34 600000000" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('telefono') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('telefono') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('telefono'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('telefono') }}</span>
                        @else
                            <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">9-15 dígitos</span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Nombre de usuario (usuario) <span style="color: #c00;">*</span></label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required minlength="3" maxlength="255" placeholder="usuario_123" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('username') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('username') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('username'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('username') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Letras, números, guiones y guiones bajos</span>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" minlength="8" placeholder="Min. 8 caracteres" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('password') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('password') ? '#fff5f5' : '#fff' }};">
                        @if ($errors->has('password'))
                            <span style="color: #c00; font-size: 10px; margin-top: 2px; display: block;">❌ {{ $errors->first('password') }}</span>
                        @else
                            <span style="color: #666; font-size: 10px; margin-top: 2px; display: block;">Mayús, minús, números</span>
                        @endif
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" minlength="8" placeholder="Repetir contraseña" style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('password') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('password') ? '#fff5f5' : '#fff' }};">
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #333; font-size: 13px;">Fecha de nacimiento <span style="color: #c00;">*</span></label>
                    <input type="date" name="nacimiento" value="{{ old('nacimiento', $user->nacimiento?->format('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 2px solid {{ $errors->has('nacimiento') ? '#c00' : '#999' }}; border-radius: 4px; font-size: 13px; box-sizing: border-box; background: {{ $errors->has('nacimiento') ? '#fff5f5' : '#fff' }};">
                    @if ($errors->has('nacimiento'))
                        <span style="color: #c00; font-size: 11px; margin-top: 3px; display: block;">❌ {{ $errors->first('nacimiento') }}</span>
                    @else
                        <span style="color: #666; font-size: 11px; margin-top: 3px; display: block;">Debe ser anterior a hoy</span>
                    @endif
                </div>

                <div style="margin-bottom: 18px; display: flex; align-items: center; gap: 10px; background: #eef4f3; padding: 12px 14px; border-radius: 6px;">
                    <input type="checkbox" name="premium" value="1" {{ old('premium', $user->premium) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #333; font-size: 13px; margin-bottom: 2px;">Usuario premium</label>
                        <span style="color: #666; font-size: 11px;">Permite acceder a contenido y productos premium.</span>
                    </div>
                </div>

                <button type="submit" style="width: 100%; background: #9db3b0; color: white; padding: 11px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; box-sizing: border-box;" onmouseover="this.style.background='#8a9b98'" onmouseout="this.style.background='#9db3b0'">
                    Actualizar Usuario
                </button>
            </form>
        </div>
    </div>
@endsection
