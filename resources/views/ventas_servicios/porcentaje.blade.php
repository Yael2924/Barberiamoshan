@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Ventas de Servicios por Barbero</strong></h1>

    <form action="{{ route('ventas_servicios.porcentaje') }}" method="GET">
        <div class="row">
            <div class="col-md-3">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control" required>
                @error('fecha_inicio')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
    
            <div class="col-md-3">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}" required>
                @error('fecha_fin')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
            </div>        
        </div>

        <br>

        <div class="col-md-4">
            <label for="barbero_id">Barbero:</label>
            <select id="barbero_id" name="barbero_id" class="form-control" required>
                <option value="">Selecciona un Barbero</option>
                @foreach ($barberos as $barbero)
                    <option value="{{ $barbero->id }}" 
                        @selected(request('barbero_id') == $barbero->id)
                        @if($barbero->trashed()) style="color: red;" @endif>
                        {{ $barbero->nombre }} 
                        @if($barbero->trashed()) 
                            (Despedido)
                        @endif
                    </option>
                @endforeach
            </select>
            @error('barbero_id')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <br>
        
        <div class="col-md-4">
            <label for="porcentaje">Porcentaje de Ganancia (%):</label>
            <input type="number" id="porcentaje" name="porcentaje" class="form-control" value="{{ request('porcentaje', $porcentajeGanancia) }}" min="0" max="100" required readonly>
            <small class="text-danger">
                Para modificar el porcentaje de ganancia, dirígete a 
                <a href="/configuracion" class="text-danger">Ganancia de Barbero</a>.
            </small>            
            @error('porcentaje')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Generar Reporte</button>
        <a href="{{ route('ventas_servicios.index') }}" class="btn btn-secondary">Volver</a>
    </form>

    @if (isset($ventas) && count($ventas) > 0 && request('fecha_inicio') 
        && request('fecha_fin') && request('barbero_id') && request('porcentaje'))
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
                </tr>
            </thead>
            <tbody>
                @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->fecha_hora }}</td>
                    <td>{{ $venta->servicio->nombre }}</td>
                    <td>{{ $venta->barbero->nombre }}</td>
                    <td>{{ $venta->cantidad }}</td>
                    <td>${{ $venta->precio_unitario }}</td>
                    <td>${{ $venta->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Agregar paginación debajo de la tabla -->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $ventas->appends(request()->query())->links() }}
        </div>
        

        <h3>Total de Ventas: ${{ $totalVentas }}</h3>
        <h3>Ganancia del Barbero: ${{ $ganancia }}</h3>

    @else
        <div class="alert alert-warning mt-3">
            No se encontraron ventas en el rango seleccionado.
        </div>
    @endif
</section>

@endsection
