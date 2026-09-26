@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section>
    <h1><strong>Ganancia de los Barberos</strong></h1>

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

    <!-- Formulario para actualizar el porcentaje de ganancia -->
    <form action="{{ route('configuracion.guardar') }}" method="POST">
        @csrf
        <div class="col-md-3">
            <label for="porcentaje">Porcentaje de Ganancia de los Barberos:</label>
            <input type="number" id="porcentaje" name="porcentaje" class="form-control" value="{{ old('porcentaje', $configuracion->porcentaje_ganancia ?? 0) }}" min="0" max="100" required>
            @error('porcentaje')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
    </form>
</section>
@endsection
