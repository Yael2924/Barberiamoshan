@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Barberos -->
<section class="barberos">
    <h1><strong>Barberos Contratados</strong></h1>

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

    <div class="alert alert-info">
        <strong>Para registrar un nuevo Barbero, ve a la sección de Usuarios, crea un usuario y asignale el Rol de Barbero</strong>
    </div>

    <br>
    <form action="{{ route('barberos.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar barberos por nombre o por estado (activo, inactivo)...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>
    <br>
    
    <!-- Botón para registrar un nuevo barbero -->
    <!-- a href="{{ route('barberos.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Barbero
    </a--> 
    
    <a href="{{ route('barberos.exportarPDF', ['busqueda' => $busqueda]) }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>

    <br><br>
    
    <!-- Tabla de Barberos con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <!--th class="texto-tabla1">ID</th-->
                    <th class="texto-tabla1">Usuario</th>
                    <th class="texto-tabla1">Nombre del Barbero</th>
                    <th class="texto-tabla1">Teléfono</th>
                    <th class="texto-tabla1">Estado</th>
                    <th class="texto-tabla1">Color</th>
                    <th class="texto-tabla">Editar</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $barbe)
                <tr>
                    <!--td class="texto-tabla1">{ $barbe->id }}</td-->
                    <td class="texto-tabla1">{{ $barbe->usuario->nombre_usuario ?? 'N/A' }}</td>
                    <td class="texto-tabla1">{{ $barbe->nombre }}</td>
                    <td class="texto-tabla1">{{ $barbe->telefono }}</td>
                    <td class="texto-tabla1">{{ $barbe->estado == 1 ? 'Activo' : 'Inactivo'}}</td>
                    <td class="texto-tabla1">
                        @if($barbe->color)
                        <span class="badge color-badge" style="background-color: {{ $barbe->color }}; color: {{ in_array($barbe->color, ['#f6bf26', '#e67c73']) ? '#000' : '#fff' }};">
                            {{ $coloresGoogle[$barbe->color] ?? 'Sin definir' }}
                        </span>
                        @else
                        <span class="badge bg-secondary">Sin asignar</span>
                        @endif
                    </td>
                    <td class="texto-tabla">
                        <!-- Botón de editar con ícono -->
                        <a href="{{ route('barberos.edit', $barbe->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                        </a>
                    </td>
                    <td class="texto-tabla">
                        <!-- Botón de eliminar con ícono -->
                        <form id="borrar{{$barbe->id}}" style="display: inline" action="{{ route('barberos.destroy', $barbe->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="btn btn-danger" onclick="borrar({{ $barbe->id }}, '{{ $barbe->nombre }}')">
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

<style>
    .color-badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        min-width: 100px;
        text-align: center;
        display: inline-block;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }

    .color-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    /* Mejora para badges sin color */
    .bg-secondary {
        background-color: #6c757d !important;
        color: white !important;
    }
</style>
<script type="text/javascript">
    function borrar(id, nombre) {
        var confirmar = confirm('¿Deseas borrar el barbero ' + nombre +'?');
        if( confirmar ) {
            var formulario = document.getElementById('borrar'+id);
            formulario.submit();
        }
    }
</script>
@endsection
