@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Sección de Usuarios -->
<section class="usuarios">
    <h1><strong>Gestión de Usuarios</strong></h1>

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
    <form action="{{ route('usuarios.index') }}" method="GET">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-6">  <!-- Cambié para que sea responsivo sin afectar el texto -->
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar usuarios por nombre de usuario, rol...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </form>
    <br>
    
    <!-- Botón para crear un nuevo usuario -->
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Agregar Usuario
    </a>    

    <a href="{{ route('usuarios.exportarPDF', ['busqueda' => $busqueda]) }}" class="btn btn-danger mb-3" target="_blank">
        <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
    </a>

    <br><br>
    
    <!-- Tabla de Usuarios con responsividad -->
    <div class="table-responsive">  <!-- Agregado para hacer la tabla responsiva sin cambiar el tamaño del texto -->
        <table class="table">
            <thead>
                <tr>
                    <!--th class="texto-tabla1">ID</th-->
                    <th class="texto-tabla1">Nombre Completo</th>
                    <th class="texto-tabla1">Nombre de Usuario</th>
                    <th class="texto-tabla1">Email</th>
                    <th class="texto-tabla1">Teléfono</th>
                    <th class="texto-tabla1">Rol</th>
                    <th class="texto-tabla">Editar</th>
                    <th class="texto-tabla">Eliminar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $usuario)
                <tr>
                    <!--td class="texto-tabla1">{ $usuario->id }}</td-->
                    <td class="texto-tabla1">{{ $usuario->nombre }}</td>
                    <td class="texto-tabla1">{{ $usuario->nombre_usuario }}</td>
                    <td class="texto-tabla1">{{ $usuario->email }}</td>
                    <td class="texto-tabla1">{{ $usuario->telefono }}</td>
                    <td class="texto-tabla1">{{ ucfirst ($usuario->rol) }}</td>
                    <td class="texto-tabla">
                        <!-- Botón de editar con ícono -->
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i>  <!-- Icono de editar -->
                        </a>
                    </td>
                    <td class="texto-tabla">
                        <!-- Verificar si el usuario tiene barberos asociados -->
                        {{-- @if ($usuario->barberos()->exists())
                            <button class="btn btn-danger" disabled>
                                <i class="bi bi-trash"></i> Registro en uso
                            </button>
                        @else --}}
                            <!-- Botón de eliminar habilitado si no tiene barberos asociados -->
                            <form id="borrar{{$usuario->id}}" style="display: inline" action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="#" class="btn btn-danger" onclick="borrar({{ $usuario->id }}, '{{ $usuario->nombre_usuario }}')">
                                    <i class="bi bi-trash"></i> <!-- Icono de eliminar -->
                                </a>
                            </form>
                        {{-- @endif --}}
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
        var confirmar = confirm('¿Deseas borrar el usuario ' + nombre +'?');
        if( confirmar ) {
            var formulario = document.getElementById('borrar'+id);
            formulario.submit();
        }
    }
</script>
@endsection