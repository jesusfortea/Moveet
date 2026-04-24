<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="register-container">
        <h1>Registro</h1>

        @if ($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="registerForm" action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="FernandoPerez23"
                    value="{{ old('username') }}"
                    required
                >
                <span class="error-message"></span>
                @error('username')
                    <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="ejemplo@gmail.com"
                    value="{{ old('email') }}"
                    required
                >
                <span class="error-message"></span>
                @error('email')
                    <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dni">DNI</label>
                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        placeholder="99999999A"
                        value="{{ old('dni') }}"
                        required
                    >
                    <span class="error-message"></span>
                    @error('dni')
                        <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="+34999999999"
                        value="{{ old('phone') }}"
                        required
                    >
                    <span class="error-message"></span>
                    @error('phone')
                        <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Escribe aquí..."
                    required
                >
                <span class="error-message"></span>
                @error('password')
                    <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Escribe aquí..."
                    required
                >
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="birth_date">Fecha nacimiento</label>
                <input
                    type="date"
                    id="birth_date"
                    name="birth_date"
                    placeholder="DD/MM/YYYY"
                    value="{{ old('birth_date') }}"
                    required
                >
                <span class="error-message"></span>
                @error('birth_date')
                    <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="referral_code">Código de referido (opcional)</label>
                <input
                    type="text"
                    id="referral_code"
                    name="referral_code"
                    placeholder="ABC12345"
                    value="{{ old('referral_code') }}"
                >
                <span class="error-message"></span>
                @error('referral_code')
                    <span style="color: #c33; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="register-btn">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </div>
    </div>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>
