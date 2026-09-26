@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Servicios -->
<section class="ventas">
    <h1><strong>Ventas de Productos</strong></h1>

    
    <br>
    <form action="{{ route('ventas_productos.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar venta de producto por folio...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>

    <br>
    
    <!-- Botón para crear un nuevo servicio -->
    <a href="{{ route('ventas_productos.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Venta de Productos
    </a>

    <!-- Botón para el resumen de ventas de productos por filtro -->
    <a href="{{ route('ventas_productos.filtro') }}" class="btn btn-danger mb-3">
        <i class="bi bi-file-earmark-text"></i> Ventas Por Filtro
    </a>

    <br><br>
    
    <!-- Tabla de Servicios con responsividad -->
    <div class="table-responsive"> <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <th class="texto-tabla1">Folio</th>
                    <th class="texto-tabla1">Fecha</th>
                    <th class="texto-tabla1">Total</th>
                    <th class="texto-tablaNO">Editar</th>
                    <th class="texto-tabla1">Recibo/Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $venpro)
                <tr>
                    <td class="texto-tabla1">{{ $venpro->id }}</td>
                    <td class="texto-tablaNO">{{ \Carbon\Carbon::parse($venpro->fecha_hora)->format('d/m/Y g:i:a') }}</td>
                    <td class="texto-tabla1">${{ number_format($venpro->total, 2) }}</td>
                    <td class="texto-tablaNO">
                        <!-- Botón de editar con ícono -->
                        <a href="{{ route('ventas_productos.edit', $venpro->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                        </a>
                    </td>
                    <td class="texto-tabla1">
                        <!-- Botón de Recibo -->
                        <a href="{{ route('ventas_productos.pdf', $venpro->id) }}" target="_blank" class="btn btn-danger ">
                            <i class="bi bi-file-earmark-pdf"></i> Recibo
                        </a>
                        
                        <!-- Botón de detalles -->
                        <a href="{{ route('ventas_productos.detalles', $venpro->id) }}" class="btn btn-warning ">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Agregar paginación debajo de la tabla -->
<div class="paginacion-container d-flex justify-content-center">
    {{ $lista->appends(['busqueda' => $busqueda])->links() }}
</div>
</section>

@endsection
