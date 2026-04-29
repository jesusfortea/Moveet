<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #56C470;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #56C470;
            letter-spacing: -1px;
        }
        .company-info {
            text-align: right;
            font-size: 14px;
            color: #666;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
        }
        .details-box {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
        }
        .details-box h3 {
            margin-top: 0;
            font-size: 14px;
            color: #56C470;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #56C470;
            color: white;
            text-align: left;
            padding: 12px;
            font-size: 14px;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .items-table th.right, .items-table td.right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="logo">MOVEET</div>
                </td>
                <td class="company-info">
                    <strong>Moveet</strong><br>
                    moveetrun@gmail.com
                </td>
            </tr>
        </table>
    </div>

    <div class="invoice-title">Factura #{{ str_pad($factura->id, 6, '0', STR_PAD_LEFT) }}</div>

    <table class="details-table">
        <tr>
            <td style="padding-right: 10px;">
                <div class="details-box">
                    <h3>Facturar a</h3>
                    <strong>{{ $factura->nombre_titular }}</strong><br>
                    Email: {{ $factura->email_titular }}<br>
                    Usuario ID: {{ $factura->user_id }}
                </div>
            </td>
            <td style="padding-left: 10px;">
                <div class="details-box">
                    <h3>Detalles del Pago</h3>
                    Fecha: {{ $factura->created_at->format('d/m/Y') }}<br>
                    Método: Tarjeta **** {{ $factura->ultimos_digitos }}<br>
                    Estado: <span style="color: #56C470; font-weight: bold;">Pagado</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio Unitario</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $factura->concepto }}</td>
                <td class="right">1</td>
                <td class="right">{{ number_format($factura->importe, 2, ',', '.') }} €</td>
                <td class="right">{{ number_format($factura->importe, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td colspan="3" class="right">TOTAL PAGADO</td>
                <td class="right">{{ number_format($factura->importe, 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Gracias por usar Moveet.<br>
        moveetrun@gmail.com
    </div>

</body>
</html>
