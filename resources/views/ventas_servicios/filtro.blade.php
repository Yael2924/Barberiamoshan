@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Ventas de Servicios</strong></h1>

    <form action="{{ route('ventas_servicios.filtro') }}" method="GET">
        <div class="row">
            <div class="col-md-4">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                    value="{{ request('fecha_inicio') }}" required>
            </div>

            <div class="col-md-4">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                    value="{{ request('fecha_fin') }}" required min="{{ request('fecha_inicio') }}">
            </div>

            <div class="col-md-4">
                <label for="servicio_id">Servicio:</label>
                <select id="servicio_id" name="servicio_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($servicios as $servicio)
                        <option value="{{ $servicio->id }}" @selected(request('servicio_id') == $servicio->id)
                            @if($servicio->trashed()) style="color: red;" @endif>
                            {{ $servicio->nombre }}
                            @if($servicio->trashed()) (Eliminado) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-2">
            @if (!$esBarbero) <!-- Mostrar solo si no es un barbero -->
                <div class="col-md-4">
                    <label for="barbero_id">Barbero:</label>
                    <select id="barbero_id" name="barbero_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach ($barberos as $barbero)
                            <option value="{{ $barbero->id }}" 
                                @selected(request('barbero_id') == $barbero->id) 
                                @if($barbero->trashed()) style="color: red;" @endif>
                                {{ $barbero->nombre }} 
                                @if($barbero->trashed()) (Despedido) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-4">
                <label for="precio_min">Precio Unitario Mínimo:</label>
                <input type="number" id="precio_min" name="precio_min" class="form-control" min="0" 
                    placeholder="El precio mínimo debe ser 0."
                    value="{{ request('precio_min') }}">
            </div>

            <div class="col-md-4">
                <label for="precio_max">Precio Unitario Máximo:</label>
                <input type="number" id="precio_max" name="precio_max" class="form-control" min="{{ request('precio_min') }}"
                    placeholder="Debe ser mayor o igual al precio mínimo."
                    value="{{ request('precio_max') }}">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label for="cantidad_min">Cantidad Mínima:</label>
                <input type="number" id="cantidad_min" name="cantidad_min" class="form-control" min="1"
                    placeholder="Debe ser igual o mayor a 1."
                    value="{{ request('cantidad_min') }}">
            </div>

            <div class="col-md-4">
                <label for="cantidad_max">Cantidad Máxima:</label>
                <input type="number" id="cantidad_max" name="cantidad_max" class="form-control" min="{{ request('cantidad_min') }}"
                    placeholder="Debe ser igual o mayor a la cantidad mínima."
                    value="{{ request('cantidad_max') }}">
            </div>

            <div class="col-md-4">
                <label for="total_min">Total Mínimo:</label>
                <input type="number" id="total_min" name="total_min" class="form-control" min="0"
                    placeholder="Debe ser igual o mayor a 0."
                    value="{{ request('total_min') }}">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label for="total_max">Total Máximo:</label>
                <input type="number" id="total_max" name="total_max" class="form-control" min="{{ request('total_min') }}"
                    placeholder="Debe ser igual o mayor al total mínimo."
                    value="{{ request('total_max') }}">
            </div>
        </div>
        
        <br>


        <button type="submit" class="btn btn-primary w-auto">Generar Reporte</button>

        <a href="{{ route('ventas_servicios.index') }}" class="btn btn-secondary w-auto">Volver</a>

    </form>

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('ventas_servicios.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="servicio_id" value="{{ request('servicio_id') }}">
            <input type="hidden" name="barbero_id" value="{{ request('barbero_id') }}">
            <input type="hidden" name="precio_min" value="{{ request('precio_min') }}">
            <input type="hidden" name="precio_max" value="{{ request('precio_max') }}">
            <input type="hidden" name="cantidad_min" value="{{ request('cantidad_min') }}">
            <input type="hidden" name="cantidad_max" value="{{ request('cantidad_max') }}">
            <input type="hidden" name="total_min" value="{{ request('total_min') }}">
            <input type="hidden" name="total_max" value="{{ request('total_max') }}">

            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>

        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Servicio</th>
                    <th>Barbero</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                    <th>Exportar Recibo</th>
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
                    <td>${{ $venta->precio_unitario }}</td>
                    <td>${{ $venta->total }}</td>
                    <td>
                        <a href="{{ route('ventas_servicios.pdf', $venta->id) }}" target="_blank" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> Recibo
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Agregar paginación debajo de la tabla -->
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
    document.addEventListener("DOMContentLoaded", function () {
        function validarCampoMenorMayor(campoMenor, campoMayor) {
            campoMayor.addEventListener("input", function () {
                if (parseFloat(campoMayor.value) < parseFloat(campoMenor.value)) {
                    campoMayor.setCustomValidity("El valor debe ser mayor o igual que " + campoMenor.value);
                } else {
                    campoMayor.setCustomValidity("");
                }
            });

            campoMenor.addEventListener("input", function () {
                campoMayor.min = campoMenor.value;
            });
        }

        // Validar fecha inicio y fecha fin
        let fechaInicio = document.getElementById("fecha_inicio");
        let fechaFin = document.getElementById("fecha_fin");
        if (fechaInicio && fechaFin) {
            fechaFin.addEventListener("input", function () {
                if (fechaFin.value < fechaInicio.value) {
                    fechaFin.setCustomValidity("La fecha fin no puede ser menor que la fecha inicio");
                } else {
                    fechaFin.setCustomValidity("");
                }
            });
        }

        // Validaciones de valores numéricos
        let precioMin = document.getElementById("precio_min");
        let precioMax = document.getElementById("precio_max");
        if (precioMin && precioMax) validarCampoMenorMayor(precioMin, precioMax);

        let cantidadMin = document.getElementById("cantidad_min");
        let cantidadMax = document.getElementById("cantidad_max");
        if (cantidadMin && cantidadMax) validarCampoMenorMayor(cantidadMin, cantidadMax);

        let totalMin = document.getElementById("total_min");
        let totalMax = document.getElementById("total_max");
        if (totalMin && totalMax) validarCampoMenorMayor(totalMin, totalMax);
    });
</script>

@endsection
