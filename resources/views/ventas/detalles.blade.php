@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Detalles de Venta -->
<section class="detalles-venta">
    <h1><strong>Detalles de la Venta</strong></h1>



    <div class="mb-3">
        <strong>Folio de Venta:</strong> {{ $venta->id }}
    </div>

    <div class="mb-3">
        <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y') }}
    </div>

    <div class="mb-3">
        <strong>Hora:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i:s') }}
    </div>

    <h3>Productos Vendidos:</h3>

    <!-- Tabla de productos vendidos -->
    <div class="table-responsive">
        <table class="table">
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
                    <td>${{ number_format($producto->subtotal_calculado, 2) }}</td> <!-- Mostrar subtotal calculado -->
                </tr>
                @endforeach
            </tbody>            
        </table>
    </div>   
    
    <div class="mb-3" style="font-size: 250%">
        <strong>Total:</strong> ${{ $venta->total }}
    </div>

    <!-- Botón para Exportar a PDF -->
    <a href="{{ route('ventas_productos.pdf', $venta->id) }}" target="_blank" class="btn btn-danger mt-3">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>

    {{-- Botón para volver a la lista de ventas de productos --}}
    <a href="{{ route('ventas_productos.index') }}" class="btn btn-secondary mt-3">Volver</a>
</section>

@endsection
