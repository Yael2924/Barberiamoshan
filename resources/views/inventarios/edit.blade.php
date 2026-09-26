@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar el Inventario de un Producto -->
<section class="editar-inventario">
    <h1><strong>Editar Inventario</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventarios.update', $inventario->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-md-2">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <select name="producto_id" id="producto_id" class="form-control" required>
                    @foreach ($productos as $producto)
                        <option value=" {{ $producto->id }}" {{ $inventario->producto_id == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="fecha">Selecciona la fecha que esta surtiendo</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $inventario->fecha }}" required>
            </div>
        </div>
            
            
        <div class="col-md-3 mb-md-2">
            <label for="stock" class="form-label">Stock</label>
            <input value="{{ old('stock', $inventario->stock) }}"  type="number" class="form-control" id="stock" name="stock" 
                placeholder="Ingrese el stock a surtir" 
                min="1" 
                title="El stock debe ser igual o mayor a 1." 
                required>
        </div>       

        <button type="submit" class="btn btn-primary">Guardar Inventario</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>
@endsection