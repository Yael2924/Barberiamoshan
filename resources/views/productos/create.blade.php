@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Vista para Crear un Nuevo Producto -->
<section class="crear-producto">
    <h1><strong>Nuevo Producto</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST">
        @csrf

        
        <div class="col-md-6 mb-md-2">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input type="text" class="form-control" id="nombre" name="nombre" 
                value="{{ old('nombre') }}" 
                placeholder="Debe tener entre 5 y 50 caracteres." 
                minlength="5" maxlength="50" 
                pattern=".{5,50}" 
                title="El nombre debe tener entre 5 y 50 caracteres." 
                required>
        </div>
        
        <div class="col-md-6 mb-md-2">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                placeholder="Debe tener entre 15 y 100 caracteres." 
                minlength="15" maxlength="100" 
                pattern=".{15,100}" 
                title="La descripción debe tener entre 15 y 100 caracteres." 
                required>{{ old('descripcion') }}</textarea>
        </div>
        
        <div class="row">
            <div class="col-md-3 mb-md-2">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" 
                    value="{{ old('precio') }}" 
                    placeholder="El precio debe ser mayor a 0." 
                    min="1.00" 
                    title="El precio debe ser igual o mayor a 1." 
                    required>
            </div>

            <div class="col-md-3 mb-md-2" style="margin-left: 13px;">
                <label for="mayoreo" class="form-label">Precio por Mayoreo</label>
                <input type="number" step="0.01" class="form-control" id="mayoreo" name="mayoreo" 
                    value="{{ old('mayoreo') }}" 
                    placeholder="El precio debe ser igual o mayor a 1." 
                    min="1.00" 
                    title="El precio por mayoreo debe ser menor al precio normal." 
                    required>
            </div>
        </div>
                
        <div class="col-md-3 mb-md-2" >
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" 
                value="{{ old('stock') }}" 
                placeholder="El stock debe ser mayor a 0." 
                min="1" 
                title="El stock debe ser igual o mayor a 1." 
                required>
        </div>
        
        <div class="col-md-6 mb-md-2">
            <label for="codigo_barras" class="form-label">Código de Barras</label>
            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" 
                value="{{ old('codigo_barras') }}" 
                placeholder="Debe contener entre 10 y 15 dígitos numéricos." 
                minlength="10" maxlength="15" 
                pattern="[0-9]{10,15}" 
                title="Debe contener entre 10 y 15 dígitos numéricos" 
                required>
        </div>
                
        <br>

        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
    <br>
    <br>
</section>

<script>
    // Validación al enviar el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const precio = parseFloat(document.getElementById('precio').value);
        const precioMayoreo = parseFloat(document.getElementById('mayoreo').value);
        const stock = parseInt(document.getElementById('stock').value);
        let isValid = true;

        // Validación de precios y stock
        if (precio <= 0) {
            mostrarError(document.getElementById('precio'), 'El precio debe ser mayor a 0.');
            isValid = false;
        }
        
        if (precioMayoreo <= 0) {
            mostrarError(document.getElementById('mayoreo'), 'El precio por mayoreo debe ser mayor a 0.');
            isValid = false;
        }
        
        if (stock <= 0) {
            mostrarError(document.getElementById('stock'), 'El stock debe ser mayor a 0.');
            isValid = false;
        }
        
        if (precioMayoreo >= precio) {
            mostrarError(document.getElementById('mayoreo'), 'El precio por mayoreo debe ser menor al precio normal.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }
        return true;
    });

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
            },
            precio: { 
                regex: /^[1-9][0-9]*(\.[0-9]{1,2})?$/, 
                min: 1, 
                mensaje: "Debe ser un minimo 1."
            },
            mayoreo: { 
                regex: /^[1-9][0-9]*(\.[0-9]{1,2})?$/, 
                min: 1, 
                mensaje: "Debe ser un número mayor a 0 y menor al precio normal.",
                compararCon: "precio"
            },
            stock: { 
                regex: /^[1-9][0-9]*$/, 
                min: 1, 
                mensaje: "Debe ser un número entero mayor a 0."
            },
            codigo_barras: { 
                regex: /^[0-9]{10,15}$/, 
                mensaje: "Debe contener entre 10 y 15 dígitos numéricos."
            }
        };

        // Agregar evento de validación en tiempo real
        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => validarCampo(campo, reglas[id]));
                // Validar también cuando pierde el foco
                campo.addEventListener("blur", () => validarCampo(campo, reglas[id]));
            }
        });

        function validarCampo(campo, regla) {
            let mensajeError = "";
            const valor = campo.value.trim();

            if (!valor) {
                mensajeError = "Este campo es obligatorio.";
            } else if (campo.type === 'number') {
                const numValue = parseFloat(valor);
                
                if (isNaN(numValue)) {
                    mensajeError = "Debe ingresar un número válido.";
                } else if (numValue <= 0) {
                    mensajeError = "El precio por mayoreo debe ser minimo 1.";
                } else if (numValue < regla.min) {
                    mensajeError = regla.mensaje;
                } else if (regla.compararCon) {
                    const otroCampo = document.getElementById(regla.compararCon);
                    if (otroCampo && numValue >= parseFloat(otroCampo.value)) {
                        mensajeError = "El precio por mayoreo debe ser menor al precio normal.";
                    }
                }
            } else {
                if (valor.length < regla.min) {
                    mensajeError = regla.mensaje;
                } else if (valor.length > regla.max) {
                    mensajeError = regla.mensaje;
                } else if (!regla.regex.test(valor)) {
                    mensajeError = regla.mensaje;
                }
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
            
            // Resaltar campo con error
            campo.style.borderColor = mensaje ? "red" : "";
        }
    });
</script>

@endsection