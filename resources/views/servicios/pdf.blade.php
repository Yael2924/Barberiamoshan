<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios Disponibles</title>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
            text-align: center;
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
    
    <h1>Servicios Disponibles</h1>

    <table>
        <thead>
            <tr>
                <th>Nombre del Servicio</th>
                <th>Descripción</th>
                <th>Duración</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lista as $servi)
            <tr>
                <td>{{ $servi->nombre }}</td>
                <td>{{ $servi->descripcion }}</td>
                <td>{{ $servi->duracion }} min</td>
                <td>${{ number_format($servi->precio, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        <p>Generado el {{ now()->format('d/m/Y') }} por {{ $usuario }}</p>
    </footer>

</body>
</html>
