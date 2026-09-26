@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Resumen General de Ventas</strong></h1>

    <form action="{{ route('ventas.resumen') }}" method="GET">
        <div class="row">
            <div class="col-md-3 mb-md-2">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}" required>
            </div>

            <div class="col-md-3 mb-md-2">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}" required>
            </div>
        </div>

        <!-- Agregar este nuevo campo para filtrar por tipo -->
        <div class="col-md-3 mb-md-2">
            <label for="tipo">Filtrar por:</label>
            <select id="tipo" name="tipo" class="form-control">
                <option value="">Todos</option>
                <option value="Producto" {{ request('tipo') == 'Producto' ? 'selected' : '' }}>Productos</option>
                <option value="Servicio" {{ request('tipo') == 'Servicio' ? 'selected' : '' }}>Servicios</option>
            </select>
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">Volver</a>
    </form>

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('ventas.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="tipo" value="{{ request('tipo') }}">
            
            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>
    @endif



    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
    <h2 class="mt-4">Resultados 
        @if(request('tipo'))
            ({{ request('tipo') }}s)
        @else
            (Todos)
        @endif
    </h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Total</th>
                    <!--th class=>Recibo/Detalles</th-->
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y g:i a') }}</td>
                    <td>{{ $venta->tipo }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                    <!--td class="texto-tabla1">
                        <!-- Botón de Recibo -->
                        <!--a href="{ route('ventas_productos.pdf', $venta->id) }}" target="_blank" class="btn btn-danger ">
                            <i class="bi bi-file-earmark-pdf"></i> Recibo
                        </a>
                        
                        <!-- Botón de detalles -->
                        <!--a href="{ route('ventas_productos.detalles', $venta->id) }}" class="btn btn-warning ">
                            <i class="bi bi-eye"></i> Ver
                        </a-->
                    </td-->
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Agregar paginación debajo de la tabla -->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $ventas->appends(request()->query())->links() }}
        </div>

        <h3>Total de Ventas: ${{ number_format($totalVentas, 2) }}</h3>
       

    @else
        <div class="alert alert-warning mt-3">
            No se encontraron ventas en el rango seleccionado.
        </div>
    @endif
</section>

<!-- Evita que los resultados se mantengan en caché al regresar -->
<script>
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

@endsection
