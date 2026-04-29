<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura de Pago - Moveet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f5;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #56C470;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #56C470;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Gracias por tu compra!</h1>
        </div>
        <div class="content">
            <p>Hola {{ $factura->nombre_titular }},</p>
            <p>Te confirmamos que hemos recibido tu pago de <strong>{{ number_format($factura->importe, 2, ',', '.') }} €</strong> por el concepto de <strong>{{ $factura->concepto }}</strong>.</p>
            <p>Tus misiones han sido renovadas exitosamente en tu cuenta de Moveet.</p>
            <p>Encontrarás adjunta a este correo la factura en formato PDF correspondiente a esta transacción.</p>
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/home" class="btn">Volver a Moveet</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Moveet. Todos los derechos reservados.<br>
            moveetrun@gmail.com
        </div>
    </div>
</body>
</html>
