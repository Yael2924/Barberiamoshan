@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Vista para Editar un Producto -->
<section class="editar-producto">
    <h1><strong>Editar Producto</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.update', $prod->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="col-md-6 mb-md-2">
            <label for="nombre" class="form-label">Nombre del Producto</label>
            <input value="{{ old('nombre', $prod->nombre) }}" type="text" class="form-control" id="nombre" name="nombre" 
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
                required>{{ old('descripcion', $prod->descripcion) }}</textarea>
        </div>        
        
        <div class="row">
            <div class="col-md-3 mb-md-2">
                <label for="precio" class="form-label">Precio</label>
                <input value="{{ old('precio', $prod->precio) }}" type="number" step="0.01" class="form-control" id="precio" name="precio" 
                    placeholder="El precio debe ser mayor a 0." 
                    min="1.00" 
                    title="El precio debe ser igual o mayor a 1." 
                    required>
            </div>

            <div class="col-md-3 mb-md-2" style="margin-left: 13px;">
                <label for="mayoreo" class="form-label">Precio por Mayoreo</label>
                <input value="{{ old('mayoreo', $prod->mayoreo) }}" type="number" step="0.01" class="form-control" id="mayoreo" name="mayoreo" 
                    placeholder="El precio debe ser igual o mayor a 1." 
                    min="1.00" 
                    title="El precio debe ser igual o mayor a 1." 
                    required>
            </div>
        </div>

        <div class="col-md-6 mb-md-2">
            <label for="codigo_barras" class="form-label">Código de Barras</label>
            <input value="{{ old('codigo_barras', $prod->codigo_barras) }}" type="text" class="form-control" 
                id="codigo_barras" name="codigo_barras" 
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
</section>

<script>
    // En tu formulario HTML/JavaScript
    document.querySelector('form').addEventListener('submit', function(e) {
        const precio = parseFloat(document.getElementById('precio').value);
        const precioMayoreo = parseFloat(document.getElementById('mayoreo').value);
        
        if (precioMayoreo >= precio) {
            e.preventDefault();
            alert('El precio por mayoreo debe ser menor al precio normal');
            return false;
        }
        return true;
    });


    document.addEventListener("DOMContentLoaded", function() {
        const reglas = {
            nombre: { 
                regex: /^[A-Za-zÀ-ÿÑñ\s]{5,50}$/, 
                mensaje: "Debe tener entre 5 y 50 caracteres y al menos una letra.", 
                requerido: true 
            },
            descripcion: { 
                regex: /^[A-Za-zÀ-ÿÑñ\s]{15,100}$/, 
                mensaje: "Debe tener entre 15 y 100 caracteres y al menos una letra.", 
                requerido: true 
            },
            precio: { 
                regex: /^[0-9]+(\.[0-9]{1,2})?$/, 
                mensaje: "Debe ser un número válido mayor o igual a 1.", 
                requerido: true 
            },
            mayoreo: { 
                regex: /^[0-9]+(\.[0-9]{1,2})?$/, 
                mensaje: "Debe ser un número válido mayor o igual a 1 y menor que el precio normal.", 
                requerido: true
            },
            codigo_barras: { 
                regex: /^[0-9]{10,15}$/, 
                mensaje: "Debe contener entre 10 y 15 dígitos numéricos.", 
                requerido: true 
            }
        };

        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => validarCampo(campo, reglas[id]));
            }
        });

        // Validar precio por mayoreo cuando cambia el precio normal
        const precio = document.getElementById("precio");
        const mayoreo = document.getElementById("mayoreo");

        if (precio && mayoreo) {
            precio.addEventListener("input", () => {
                validarCampo(precio, reglas.precio);
                validarCampo(mayoreo, reglas.mayoreo);
            });
            mayoreo.addEventListener("input", () => validarCampo(mayoreo, reglas.mayoreo));
        }

        function validarCampo(campo, regla) {
            let mensajeError = "";
            const valor = campo.value.trim();

            if (regla.requerido && valor === "") {
                mensajeError = "Este campo es obligatorio.";
            } else if (regla.regex && !regla.regex.test(valor)) {
                mensajeError = regla.mensaje;
            } else if (campo.id === "precio" && parseFloat(valor) < 1) {
                mensajeError = "El precio debe ser mínimo 1.";
            } else if (campo.id === "mayoreo") {
                const precioValor = parseFloat(precio.value);
                const mayoreoValor = parseFloat(valor);
                if (!isNaN(mayoreoValor) && mayoreoValor < 1) {
                    mensajeError = "El precio por mayoreo debe ser mínimo 1.";
                } else if (!isNaN(precioValor) && !isNaN(mayoreoValor) && mayoreoValor >= precioValor) {
                    mensajeError = "El precio por mayoreo debe ser menor al precio normal.";
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
        }
    });

</script>
@endsection