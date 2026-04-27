<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h1>Recuperar contraseña</h1>

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

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <button type="submit" class="login-btn">Enviar enlace</button>
        </form>

        <div class="register-link">
            <a href="{{ route('login') }}">Volver al inicio de sesión</a>
        </div>
    </div>
</body>
</html>
