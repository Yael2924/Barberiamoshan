<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
        }
        .logo-container {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .logo-container img {
            max-width: 200px;
            height: auto;
        }
        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .recibo-container {
            border: 2px solid black;
            padding: 20px;
            width: 60%;
            margin: auto;
        }
        .info {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: black;
            color: white;
        }
        .total {
            text-align: right;
            font-weight: bold;
        }
        footer {
            text-align: center;
            font-size: 12px;
            margin-top: 20px;
            color: #4A4A4A;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="img/logo_barberia_sin_fondo.png" alt="Logo de la empresa">
    </div>
    
    <h1>Recibo de Venta</h1>
    
    <div class="recibo-container">
        <p class="info"><strong>Folio:</strong> {{ $venta->id }}</p>
        <p class="info"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}</p>
        <p class="info"><strong>Hora:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i:s') }}</p>
        {{-- <p class="info"><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'No especificado' }}</p> --}}
        <p class="info"><strong>Barbero:</strong> {{ $venta->barbero->nombre }}</p>
        
        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $venta->servicio->nombre }}</td>
                    <td>{{ $venta->cantidad }}</td>
                    <td>${{ number_format($venta->precio_unitario, 2) }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <p class="total">Total a pagar: ${{ number_format($venta->total, 2) }}</p>
    </div>
    
    <footer>
        <p>Generado el {{ now()->format('d/m/Y') }} por {{ $usuario }}</p>
    </footer>
</body>
</html>
