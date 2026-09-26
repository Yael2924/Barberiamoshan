@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear un Nuevo Barbero -->
<section class="crear-barbero">
    <h1><strong>Nuevo Barbero</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    

    <form action="{{ route('barberos.store') }}" method="POST">
        @csrf

        <div class="col-md-6 mb-md-2">
            <label for="usuario_id" class="form-label">Usuario:</label>
            <select name="usuario_id" id="usuario_id" class="form-control" required>
                <option value="">Seleccionar Usuario</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }} 
                        data-nombre="{{ $usuario->nombre }}" data-telefono="{{ $usuario->telefono }}">
                        {{ $usuario->nombre_usuario }} 
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 mb-md-2">
            <label for="nombre" class="form-label">Nombre Completo</label>
            <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                placeholder="Ingrese el nombre completo del barbero" 
                minlength="10" maxlength="50" 
                pattern=".{10,50}" 
                title="El nombre debe tener entre 10 y 50 caracteres." 
                required readonly>
        </div>

        <div class="row">
            <div class="col-md-3 mb-md-2 mx-md-1">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" step="1" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}"
                    placeholder="Ingrese el número de teléfono"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required readonly>
            </div>
    
            <div class="col-md-3 mb-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select name="estado" id="estado" class="form-control" required>
                    <option value="" {{ old('estado') === null ? 'selected' : '' }}>Seleccione el Estado del Barbero</option>
                    <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>            
        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Guardar Barbero</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('barberos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>

    <br>
    <br>

</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("usuario_id").addEventListener("change", function() {
            var selectedOption = this.options[this.selectedIndex];
            var nombre = selectedOption.getAttribute("data-nombre");
            var telefono = selectedOption.getAttribute("data-telefono");

            document.getElementById("nombre").value = nombre || "";
            document.getElementById("telefono").value = telefono || "";
        });
    });
</script>
@endsection