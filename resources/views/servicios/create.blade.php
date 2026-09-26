@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear un Nuevo Servicio -->
<section class="crear-servicio">
    <h1><strong>Nuevo Servicio</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('servicios.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre del Servicio</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                    placeholder="Debe tener entre 5 y 50 caracteres." 
                    minlength="5" maxlength="50" 
                    pattern=".{5,50}" 
                    title="El nombre debe tener entre 5 y 50 caracteres." 
                    required>
            </div>

            <div class="col-md-3 mb-md-2" style="margin-left: 9px;">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="{{ old('precio') }}" 
                    placeholder="El precio debe ser mayor o igual a 20." 
                    min="20.00" 
                    title="El precio debe ser igual o mayor a 20." 
                    required>
            </div>        
    
            {{-- <div class="col-md-4 mb-md-2">
                <label for="disponibilidad" class="form-label">Disponibilidad</label>
                <select class="form-control" name="disponibilidad" id="disponibilidad" aria-describedby="disponibilidadHelp" >
                    <option value="1">Disponible</option>
                    <option value="0">No Disponible</option>
                </select>
            </div> --}}
            
        </div>
        
        
        <div class="col-md-7 mb-md-2">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                placeholder="La descripción debe tener entre 15 y 100 caracteres." 
                minlength="15" maxlength="100" 
                pattern=".{15,100}" 
                title="La descripción debe tener entre 15 y 100 caracteres." 
                required>{{ old('descripcion') }}</textarea>
        </div>
        
        <div class="col-md-4 mb-md-2">
            <label for="duracion" class="form-label">Duración</label>
            <input type="number" step="1" class="form-control" id="duracion" name="duracion" value="{{ old('duracion') }}" 
                placeholder="Ingrese la duración en minutos, entre 10 y 180 minutos." 
                min="10"
                max="180" 
                title="La duración debe ser entre 10 y 180" 
                required>
        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Guardar Servicio</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>

<script>
    // Evento al enviar el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const precio = parseFloat(document.getElementById('precio').value);
        const duracion = parseInt(document.getElementById('duracion').value);

        // Validación de la duración (debe ser un número entre 10 y 180)
        if (isNaN(duracion) || duracion < 10 || duracion > 180) {
            e.preventDefault();
            alert('La duración debe ser un número entre 10 y 180 minutos.');
            return false;
        }

        // Validación del precio (debe ser un número mayor o igual a 20)
        if (isNaN(precio) || precio < 20) {
            e.preventDefault();
            alert('El precio debe ser un valor numérico mayor o igual a 20.');
            return false;
        }

        // Validación de campos de texto (nombre, descripción)
        const nombre = document.getElementById('nombre').value.trim();
        const descripcion = document.getElementById('descripcion').value.trim();

        // Validación del campo nombre
        if (nombre.length < 5 || nombre.length > 50 || !/[a-zA-ZÀ-ÿ]/.test(nombre)) {
            e.preventDefault();
            alert("El nombre debe tener entre 5 y 50 caracteres y contener al menos una letra.");
            return false;
        }

        // Validación del campo descripción
        if (descripcion.length < 15 || descripcion.length > 100 || !/[a-zA-ZÀ-ÿ]/.test(descripcion)) {
            e.preventDefault();
            alert("La descripción debe tener entre 15 y 100 caracteres y contener al menos una letra.");
            return false;
        }

        return true;
    });

    // Validaciones en tiempo real
    document.addEventListener("DOMContentLoaded", function() {
        const reglas = {
            nombre: { 
                regex: /[a-zA-ZÀ-ÿ]/, 
                min: 5, 
                max: 50, 
                mensaje: "Debe tener entre 5 y 50 caracteres y contener al menos una letra."
            },
            descripcion: { 
                regex: /[a-zA-ZÀ-ÿ]/, 
                min: 15, 
                max: 100, 
                mensaje: "Debe tener entre 15 y 100 caracteres y contener al menos una letra."
            }
        };

        // Validación especial para precio (número)
        document.getElementById('precio').addEventListener('input', function() {
            const valor = parseFloat(this.value);
            let mensaje = "";
            
            if (isNaN(valor)) {
                mensaje = "Debe ingresar un número válido";
            } else if (valor < 20) {
                mensaje = "El precio debe ser mayor o igual a 20";
            }
            
            mostrarError(this, mensaje);
        });

        // Validación especial para duración (número)
        document.getElementById('duracion').addEventListener('input', function() {
            const valor = parseInt(this.value);
            let mensaje = "";
            
            if (isNaN(valor)) {
                mensaje = "Debe ingresar un número válido";
            } else if (valor < 10 || valor > 180) {
                mensaje = "La duración debe ser entre 10 y 180 minutos";
            }
            
            mostrarError(this, mensaje);
        });

        // Agregar evento de validación en tiempo real para campos de texto
        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => validarCampoTexto(campo, reglas[id]));
            }
        });

        function validarCampoTexto(campo, regla) {
            let mensajeError = "";
            const valor = campo.value.trim();

            if (!valor) {
                mensajeError = "Este campo es obligatorio.";
            } else if (valor.length < regla.min) {
                mensajeError = regla.mensaje;
            } else if (valor.length > regla.max) {
                mensajeError = regla.mensaje;
            } else if (!regla.regex.test(valor)) {
                mensajeError = regla.mensaje;
            }

            mostrarError(campo, mensajeError);
        }

        function mostrarError(campo, mensaje) {
            let errorElement = campo.nextElementSibling;

            if (!errorElement || !errorElement.classList.contains("error-text")) {
                errorElement = document.createElement("small");
                errorElement.classList.add("error-text");
                errorElement.style.color = "red";
                campo.parentNode.appendChild(errorElement);
            }

            errorElement.textContent = mensaje;
            errorElement.style.display = mensaje ? "block" : "none";
        }
    });
</script>
@endsection
