@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Inventario de Productos por Filtro</strong></h1>

    <form action="{{ route('inventarios.filtro') }}" method="GET">
        <div class="col-md-3">
            <label for="producto_id">Producto:</label>
            <select id="producto_id" name="producto_id" class="form-control">
                <option value="">Seleccionar Producto</option>
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
    
        <br>
        
        <div class="row">
            <div class="col-md-3">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
            </div>
        
            <div class="col-md-3">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
            </div>
        </div>
        
        <br>

        <div class="row">
            <div class="col-md-3">
                <label for="stock_min">Stock Mínimo:</label>
                <input type="number" id="stock_min" name="stock_min" class="form-control" value="{{ request('stock_min') }}" min="0">
            </div>
        
            <div class="col-md-3">
                <label for="stock_max">Stock Máximo:</label>
                <input type="number" id="stock_max" name="stock_max" class="form-control" value="{{ request('stock_max') }}" min="0">
            </div>
        </div>
        
        <br>
    
        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">Volver</a>
    </form>
    

    @if (!empty($inventarios) && $inventarios->count() > 0)
        <form action="{{ route('inventarios.exportarPDF') }}" method="GET" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="producto_id" value="{{ request('producto_id') }}">
            <input type="hidden" name="stock_min" value="{{ request('stock_min') }}">
            <input type="hidden" name="stock_max" value="{{ request('stock_max') }}">

            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>

        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Fecha</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventarios as $inventario)
                <tr>
                    <td>{{ $inventario->producto->nombre ?? 'Sin producto' }}</td>
                    <td>{{ $inventario->fecha }}</td>
                    <td>{{ $inventario->stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Agregar paginación debajo de la tabla -->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $inventarios->appends(request()->query())->links() }}
        </div>

        <h3>Total de Inventarios: {{ $totalStock }}</h3>

    @else
        <div class="alert alert-warning mt-3">
            No se encontraron registros de inventario en el rango seleccionado.
        </div>
    @endif
</section>

<script>
    document.getElementById('stock_min').addEventListener('input', function () {
        let stockMin = parseInt(this.value);
        let stockMaxInput = document.getElementById('stock_max');

        if (stockMaxInput.value !== "" && parseInt(stockMaxInput.value) < stockMin) {
            stockMaxInput.value = stockMin; // Ajusta el valor para que no sea menor
        }

        stockMaxInput.min = stockMin; // Ajusta el mínimo dinámicamente
    });
</script>

@endsection
