<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h1>Verifica tu correo</h1>
        <p style="margin-bottom: 16px; color: #4b5563;">Te hemos enviado un email con un enlace de verificación. Necesitas verificarlo para asegurar tu cuenta.</p>

        @if (session('status'))
            <div class="error-messages" style="background: #eef8ee; border-color: #b8dfb8; color: #2f6f2f;">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" class="login-btn">Reenviar correo de verificación</button>
        </form>

        <div class="register-link" style="margin-top: 14px;">
            <a href="{{ route('home') }}">Continuar a Moveet</a>
        </div>
    </div>
</body>
</html>
