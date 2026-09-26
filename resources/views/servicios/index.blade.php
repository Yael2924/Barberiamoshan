@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Servicios -->
<section class="servicios">
    <h1><strong>Servicios Disponibles</strong></h1>

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

    
    <br>
    <form action="{{ route('servicios.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar servicios por nombre...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>
    <br>
    
    <!-- Botón para crear un nuevo servicio -->
    <a href="{{ route('servicios.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Servicio
    </a>   
    
    <a href="{{ route('servicios.exportarPDF', ['busqueda' => $busqueda]) }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>    

    <br><br>
    
    <!-- Tabla de Servicios con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <!--th class="texto-tabla">ID</th-->
                    <th class="texto-tabla1">Nombre del Servicio</th>
                    <th class="texto-tabla1">Descripción</th>
                    <th class="texto-tabla1">Duración</th>
                    <th class="texto-tabla1">Precio</th>
                    {{-- <th class="texto-tabla">Disponibilidad</th> --}}
                    <th class="texto-tabla">Editar</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $servi)
                <tr>
                    <!--td class="texto-tabla">{ $servi->id }}</td-->
                    <td class="texto-tabla1">{{ $servi->nombre }}</td>
                    <td class="texto-tabla1">{{ $servi->descripcion }}</td>
                    <td class="texto-tabla1">{{ $servi->duracion }} min</td>
                    <td class="texto-tabla1">${{ $servi->precio }}</td>
                    {{-- <td class="texto-tabla">{{ $servi->disponibilidad == 1 ? 'Disponible' : 'No Disponible' }}</td> --}}
                    <td class="texto-tabla">
                        <!-- Botón de editar con ícono -->
                        <a href="{{ route('servicios.edit', $servi->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                        </a>
                    </td>
                    <td class="texto-tabla">
                        <!-- Botón de eliminar con ícono -->
                        <form id="borrar{{$servi->id}}" style="display: inline" action="{{ route('servicios.destroy', $servi->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger" onclick="borrar({{ $servi->id }}, '{{ $servi->nombre }}')">
                                <i class="bi bi-trash"></i>  <!-- Icono de eliminar -->
                            </a>
                        </form>
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

<script type="text/javascript">
    function borrar(id, nombre) {
        var confirmar = confirm('¿Deseas borrar el servicio ' + nombre +'?');
        if( confirmar ) {
            var formulario = document.getElementById('borrar'+id);
            formulario.submit();
        }
    }
</script>
@endsection
