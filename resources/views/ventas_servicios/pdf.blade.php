<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas de Servicios</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
        }

        /* Contenedor del logo alineado a la izquierda */
        .logo-container {
            position: absolute; /* Para fijarlo en la parte superior */
            top: 10px;  /* Ajusta la distancia desde arriba */
            left: 10px; /* Ajusta la distancia desde la izquierda */
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
            margin: 0 auto;
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

        /* Footer */
        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #4A4A4A;
            /* background-color: #f9f9f9; */
            padding: 10px 0;
        }
    </style>
</head>
<body>

        <!-- Espacio para el logo alineado a la izquierda -->
    <div class="logo-container">
        <img src="img/logo_barberia_sin_fondo.png" alt="Logo de la empresa">
    </div>
    
    <br>
    <br>

    <h1>Reporte de Ventas de Servicios</h1>

    <!-- Fechas -->
    <p><strong>Fecha Inicio:</strong> {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}</p>
    <p><strong>Fecha Fin:</strong> {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>

    <!-- Tabla de ventas -->
    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha</th>
                <th>Servicio</th>
                <th>Barbero</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
            <tr>
                <td>{{ $venta->id }}</td>
                <td>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y g:i a') }}</td>
                <td>{{ $venta->servicio->nombre }}</td>
                <td>{{ $venta->barbero->nombre }}</td>
                <td>{{ $venta->cantidad }}</td>
                <td>${{ number_format($venta->precio_unitario, 2) }}</td>
                <td>${{ number_format($venta->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total de ventas -->
    <h3>Total de Ventas: ${{ number_format($totalVentas, 2) }}</h3>

    <!-- Footer con usuario y fecha de generación -->
    <footer>
        <p>Generado el {{ now()->format('d/m/Y') }} por {{ $usuario }}</p>
    </footer>
</body>
</html>
