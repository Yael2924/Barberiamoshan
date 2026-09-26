@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Ventas de Productos</strong></h1>

    <form action="{{ route('ventas_productos.filtro') }}" method="GET">
        <div class="row">
            <div class="col-md-3 mb-md-2">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                       value="{{ request('fecha_inicio') }}" required>
            </div>
    
            <div class="col-md-3 mb-md-2" style="margin-left: 10px;">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                       value="{{ request('fecha_fin') }}" required>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <label for="producto_id">Producto:</label>
            <select name="producto_id" id="producto_id" class="form-control">
                <option value="">Todos los productos</option>
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="total_minimo">Total mínimo:</label>
                <input type="number" name="total_minimo" id="total_minimo" 
                    class="form-control" min="1" step="0.01"
                    value="{{ request('total_minimo') }}" placeholder="Mínimo $1">
            </div>

            <div class="col-md-3 mb-3" style="margin-left: 10px;">
                <label for="total_maximo">Total máximo:</label>
                <input type="number" name="total_maximo" id="total_maximo" 
                    class="form-control" min="0" step="0.01"
                    value="{{ request('total_maximo') }}" placeholder="Sin límite">
            </div>
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="{{ route('ventas_productos.index') }}" class="btn btn-secondary">Volver</a>
    </form>

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('ventas_productos.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="producto_id" value="{{ request('producto_id') }}">
            <input type="hidden" name="total_minimo" value="{{ request('total_minimo') }}">
            <input type="hidden" name="total_maximo" value="{{ request('total_maximo') }}">
            
            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>
    @endif

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th class="texto-tabla1">Recibo/Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y g:i a') }}</td>
                    <td>${{ $venta->total }}</td>
                    <td class="texto-tabla1">
                        <a href="{{ route('ventas_productos.pdf', $venta->id) }}" target="_blank" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Recibo
                        </a>
                        <a href="{{ route('ventas_productos.detalles', $venta->id) }}" class="btn btn-warning">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación y total de ventas-->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $ventas->appends(request()->query())->links() }}
        </div>
        <h3>Total de Ventas: ${{ $totalVentas }}</h3>
    
    @else
        <div class="alert alert-warning mt-3">
            No se encontraron ventas en el rango seleccionado.
        </div>
    @endif
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalMinimo = document.getElementById('total_minimo');
        const totalMaximo = document.getElementById('total_maximo');
    
        // Actualizar el mínimo permitido para el máximo cuando cambia el mínimo
        totalMinimo.addEventListener('input', function() {
            if (this.value && parseFloat(this.value) >= 1) {
                totalMaximo.min = this.value;
            } else {
                totalMaximo.removeAttribute('min');
            }
        });
    });
</script>
@endsection
