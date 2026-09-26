@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="inventarios">
    <h1><strong>Inventario</strong></h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    @if($producto_id)
        <h2>Inventario para el Producto: {{ $lista->first()->producto->nombre ?? 'No encontrado' }}</h2>
        <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">Ver todos</a>
    @endif

    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>

    <!-- Formulario de Búsqueda -->
    <form action="{{ route('inventarios.index') }}" method="GET" class="my-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre, codigo, fecha...">
                    <input type="hidden" name="producto_id" value="{{ $producto_id }}">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>


    <!-- Botón para agregar inventario -->
    <a href="{{ route('inventarios.create', ['producto_id' => $producto_id ?? null]) }}" 
        class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Agregar Inventario
    </a>
    <!-- Exportar PDF -->
    <a href="{{ route('inventarios.exportarPDF', ['producto_id' => $producto_id, 'busqueda' => $busqueda]) }}" 
       class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>

    @if (Auth::check() && Auth::user()->rol === 'Administrador' && !$producto_id)
    <a href="{{ route('inventarios.filtro') }}" class="btn btn-info mb-3">
        <i class="bi bi-file-earmark-text"></i> Reportes Por Filtro
    </a>
    @endif

    <!-- Tabla de Inventarios -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Codigo de Barra</th>
                    <th>Producto</th>
                    <th>Fecha</th>
                    <th>Stock</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $inventario)
                <tr>
                    <td>{{ $inventario->producto->codigo_barras }}</td>
                    <td>{{ $inventario->producto->nombre }}</td>
                    <td class="texto-tabla1">{{ \Carbon\Carbon::parse($inventario->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $inventario->stock }}</td>
                    <td class="texto-tabla">
                        <form id="borrar{{ $inventario->id }}" style="display: inline" 
                              action="{{ route('inventarios.destroy', $inventario->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger" 
                               onclick="borrar({{ $inventario->id }}, '{{ $inventario->producto->nombre }}')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="paginacion-container d-flex justify-content-center">
        {{ $lista->appends(['busqueda' => $busqueda])->links() }}
    </div>
</section>

<script>
    function borrar(id, nombre) {
        var confirmar = confirm('¿Deseas borrar el registro ' + nombre + '?');
        if (confirmar) {
            document.getElementById('borrar' + id).submit();
        }
    }
</script>
@endsection