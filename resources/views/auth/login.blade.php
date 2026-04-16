<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h1>Inicio de sesión</h1>

        @if (session('status'))
            <div class="error-messages" style="background: #eef8ee; border-color: #b8dfb8; color: #2f6f2f;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="loginForm" action="{{ route('login.store') }}" method="POST">
            @csrf

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

            <button type="submit" class="login-btn">Entrar</button>
        </form>

        <div class="register-link">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </div>
    </div>
    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>
