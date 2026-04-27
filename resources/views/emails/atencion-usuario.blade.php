<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva consulta de atenci&oacute;n al usuario</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1E2A28; line-height: 1.6;">
    <h2 style="margin-bottom: 16px;">Nueva consulta de atenci&oacute;n al usuario</h2>

    <p><strong>Nombre:</strong> {{ $datos['nombre'] }}</p>
    <p><strong>Correo:</strong> {{ $datos['email'] }}</p>
    <p><strong>Asunto:</strong> {{ $datos['asunto'] }}</p>

    <div style="margin-top: 24px;">
        <p><strong>Mensaje:</strong></p>
        <div style="padding: 16px; background: #f4f7f6; border: 1px solid #d5e0df; border-radius: 8px; white-space: pre-line;">{{ $datos['mensaje'] }}</div>
    </div>
</body>
</html>
