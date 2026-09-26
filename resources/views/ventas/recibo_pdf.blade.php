<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Venta</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
        }

        /* Contenedor del logo alineado a la izquierda */
        .logo-container {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .logo-container img {
            max-width: 230px; /* Ajusta el tamaño según necesites */
            height: auto;
        }

        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* Estilos de la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: black;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            font-size: 18px;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #4A4A4A;
            padding: 10px 0;
        }
    </style>
</head>
<body>

    <!-- Espacio para el logo alineado a la izquierda -->
    <div class="logo-container">
        <img src="img/logo_barberia_sin_fondo.png" alt="Logo de la empresa">
    </div>

    <br><br>

    <h1>Recibo de Venta</h1>

    <div>
        <p><strong>Folio:</strong> {{ $venta->id }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}</p>
        <p><strong>Hora:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->productos as $producto)
            <tr>
                <td>{{ $producto->nombre }}</td>
                <td>{{ $producto->pivot->cantidad }}</td>
                <td>${{ number_format($producto->pivot->precio_unitario, 2) }}</td>
                <td>${{ number_format($producto->subtotal_calculado, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total a pagar: ${{ number_format($venta->total, 2) }}</p>

    <footer>
        <p>Generado el {{ now()->format('d/m/Y') }} por {{ $usuario }}</p>
    </footer>
</body>
</html>
