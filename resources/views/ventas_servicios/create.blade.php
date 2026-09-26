@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear una Nueva Venta de Servicio -->
<section class="crear-venta_servicio">
    <h1><strong>Nueva Venta de Servicios</strong></h1>

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas_servicios.store') }}" method="POST">
        @csrf

        <div class="row mb-2">
            <div class="col-md-3" style="width: 350px;">
                <label for="servicio_id" class="form-label">Servicio Realizado</label>
                <select class="form-control" id="servicio_id" name="servicio_id" onchange="calcularTotal()" required>
                    <option value="">Seleccione el Servicio que se realizó</option>
                    @foreach($servicios as $s)
                    <option value="{{$s->id}}" data-precio="{{ $s->precio }}" data-descripcion="{{ $s->descripcion }}">
                        {{ $s->nombre }}
                    </option>                
                    @endforeach
                </select>
            </div>
    
            <div class="col-md-6">
                <label for="descripcion_servicio" class="form-label">Descripción del Servicio</label>
                <span id="descripcion_servicio" class="form-control" style="background-color: #f8f9fa; min-height: 40px; display: flex; align-items: center; padding: 5px;"></span>
            </div>
        </div>
        
        <div class="col-md-3 mb-2">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" step="1" class="form-control" id="cantidad" name="cantidad" placeholder="Debe ser un número entero." 
                min="1" 
                title="La cantidad minima es 1" 
                required oninput="calcularTotal()">
        </div>

        <div class="col-md-3 mb-2">
            <label for="precio_unitario" class="form-label">Precio Unitario</label>
            <input type="number" step="0.01" class="form-control" id="precio_unitario" name="precio_unitario" placeholder="Precio del Servicio" readonly>
        </div>

        <div class="col-md-3">
            <label for="total" class="form-label">Total</label>
            <input type="number" step="0.50" class="form-control" id="total" name="total" placeholder="Total a pagar por el Servicio" readonly>
        </div>
        <br>

        <button type="submit" class="btn btn-primary">Guardar Venta de Servicio</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('ventas_servicios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>
        
    <script>
        // Función para calcular el total y mostrar el precio unitario
        function calcularTotal() {
            var servicioSelect = document.getElementById('servicio_id');
            var cantidadInput = document.getElementById('cantidad');
            var totalInput = document.getElementById('total');
            var precioUnitarioInput = document.getElementById('precio_unitario');
            var descripcionServicio = document.getElementById('descripcion_servicio');

            // Obtener el precio y la descripción del servicio seleccionado
            var precioServicio = servicioSelect.options[servicioSelect.selectedIndex].getAttribute('data-precio');
            var descripcion = servicioSelect.options[servicioSelect.selectedIndex].getAttribute('data-descripcion');
            
            // Mostrar el precio unitario en su campo
            if (precioServicio) {
                precioUnitarioInput.value = parseFloat(precioServicio).toFixed(2);
            } else {
                precioUnitarioInput.value = ''; // Limpiar si no hay selección
            }

            // Mostrar la descripción del servicio
            if (descripcion) {
                descripcionServicio.textContent = descripcion;
            } else {
                descripcionServicio.textContent = 'No hay descripción disponible.';
            }
            
            // Obtener la cantidad
            var cantidad = cantidadInput.value;
    
            // Verificar si ambos valores son válidos
            if (precioServicio && cantidad) {
                // Calcular el total
                var total = precioServicio * cantidad;
                totalInput.value = total.toFixed(2);  // Establecer el total con 2 decimales
            } else {
                totalInput.value = ''; // Si no hay cantidad o servicio, limpiar el total
            }
        }

        // VALIDACIONES EN TIEMPO REAL (NUEVO)
        document.addEventListener("DOMContentLoaded", function() {
            // Validar cantidad mientras se escribe
            const cantidadInput = document.getElementById('cantidad');
            cantidadInput.addEventListener('input', function() {
                const valor = this.value;
                let error = '';
                
                if (!valor) {
                    error = 'Ingrese la cantidad';
                } else if (valor <= 0) {
                    error = 'La cantidad debe ser mayor a 0';
                } else if (!/^\d+$/.test(valor)) {
                    error = 'Solo números enteros';
                }
                
                this.setCustomValidity(error);
                this.style.borderColor = error ? 'red' : '';
                
                // Mostrar error debajo del campo
                let errorElement = this.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains("error-text")) {
                    errorElement = document.createElement("small");
                    errorElement.className = "error-text";
                    errorElement.style.color = "red";
                    this.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = error;
            });

            // Validar selección de servicio
            const servicioSelect = document.getElementById('servicio_id');
            servicioSelect.addEventListener('change', function() {
                const error = this.value ? '' : 'Seleccione un servicio';
                this.setCustomValidity(error);
                this.style.borderColor = error ? 'red' : '';
                
                // Mostrar error debajo del campo
                let errorElement = this.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains("error-text")) {
                    errorElement = document.createElement("small");
                    errorElement.className = "error-text";
                    errorElement.style.color = "red";
                    this.parentNode.appendChild(errorElement);
                }
                errorElement.textContent = error;
            });
        });
    </script>

@endsection