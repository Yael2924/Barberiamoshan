@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Editar una Cita -->
<section class="editar-cita">
    <h1><strong>Reagendar Cita</strong></h1>

    @if ( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('citas.update', $cita->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Cliente (no editable) -->
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="nombre_cli" class="form-label">Nombre del Cliente</label>
                <input type="text" name="nombre_cli" id="nombre_cli" class="form-control" value="{{ old('nombre_cli', $cita->nombre_cli) }}" 
                    placeholder="Debe tener entre 5 y 50 caracteres y solo letras." 
                    minlength="5" maxlength="50" 
                    pattern=".{5,50}" 
                    title="El nombre debe tener entre 5 y 50 caracteres." 
                    required
                    autocomplete="off">
            </div>

            <div class="col-md-4">
                <label for="telefono" class="form-label">Teléfono del Cliente <a style="color: red;">(OPCIONAL)</a></label>
                <input type="tel" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $cita->telefono) }}" 
                    placeholder="Debe contener 10 dígitos."
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    autocomplete="off">
            </div>

            <!-- Barbero (editable) -->
            <div class="col-md-4">
                <label for="barbero_id">Barbero</label>
                <select name="barbero_id" id="barbero_id" class="form-control" required>
                    @foreach($barberos as $barbero)
                        <option value="{{ $barbero->id }}" 
                            {{ $cita->barbero_id == $barbero->id ? 'selected' : '' }}>
                            {{ $barbero->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

    
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="fecha">Selecciona la fecha de la Cita</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ $cita->fecha }}" min="{{ \Carbon\Carbon::parse($cita->fecha)->addDay()->format('Y-m-d') }}">
            </div>
        
            <div class="col-md-4">
                <label for="hora">Seleccione la hora de la cita</label>
                <select name="hora" id="hora" class="form-control" required>
                    <!-- Aquí precargamos la hora de la cita actual -->
                    <option value="{{ $cita->hora }}" selected>
                        {{ \Carbon\Carbon::parse($cita->hora)->format('g:i a') }}
                    </option>
                    <!-- Las demás opciones se cargarán aquí con JavaScript -->
                </select>
            </div>
        </div>

        <br><br>
    
        <!-- Botones -->
        <button type="submit" class="btn btn-primary">Guardar Cita</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>

<script>
    // Obtenemos las horas disponibles al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        let barberoId = document.getElementById('barbero_id').value;
        let fecha = document.getElementById('fecha').value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    document.getElementById('barbero_id').addEventListener('change', function() {
        let barberoId = this.value;
        let fecha = document.getElementById('fecha').value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    document.getElementById('fecha').addEventListener('change', function() {
        let barberoId = document.getElementById('barbero_id').value;
        let fecha = this.value;

        if (barberoId && fecha) {
            fetchHorasDisponibles(barberoId, fecha);
        }
    });

    function fetchHorasDisponibles(barberoId, fecha) {
        fetch(`/get-horas-disponiblesWeb?fecha=${fecha}&barbero_id=${barberoId}`)
            .then(response => response.json())
            .then(data => {
                let horaSelect = document.getElementById('hora');
                // Limpiar las opciones (excepto la hora precargada)
                horaSelect.innerHTML = '<option value="">Elige una de las horas disponibles</option>'; // Limpiar las opciones;

                data.forEach(horario => {
                    let option = document.createElement('option');
                    option.value = horario.hora;

                    // Convertir la hora a formato AM/PM
                    let [hours, minutes] = horario.hora.split(':');
                    let date = new Date();
                    date.setHours(parseInt(hours), parseInt(minutes), 0, 0); 

                    // Convertir la hora en formato 12 horas AM/PM
                    let horaFormateada = date.toLocaleString('es-MX', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // Mostrar la hora en el formato AM/PM
                    option.textContent = horaFormateada;
                    horaSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching horas disponibles:', error);
            });
    }
    
    //ESTO ES PARA VALIDAR ANTES DE QUE SE ENVIE EL FORMULARIO
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir envío automático
        let barberoId = document.getElementById('barbero_id').value;
        let fecha = document.getElementById('fecha').value;
        let hora = document.getElementById('hora').value;
    
        if (!barberoId || !fecha || !hora) {
            alert("Por favor, seleccione todos los campos.");
            return;
        }
    
        fetch(`/get-horas-disponiblesWeb?fecha=${fecha}&barbero_id=${barberoId}`)
            .then(response => response.json())
            .then(data => {
                let disponible = data.some(h => h.hora === hora);
                if (!disponible) {
                    alert("¡La hora seleccionada ya no está disponible! Elige otra.");
                } else {
                    e.target.submit(); // Enviar el formulario si la hora sigue disponible
                }
            })
            .catch(error => {
                console.error("Error validando la hora:", error);
                alert("Hubo un error verificando la disponibilidad. Inténtelo de nuevo.");
            });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reglas = {
            nombre_cli: { 
                regex: /^[A-Za-zÀ-ÿÑñ\s]{5,50}$/, 
                mensaje: "Debe tener entre 5 y 50 caracteres y solo letras.", 
                requerido: true 
            },
            telefono: { 
                regex: /^[0-9]{10}$/, 
                mensaje: "Debe contener 10 dígitos y solo números.", 
                requerido: false 
            },
        };
         
        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => validarCampo(campo, reglas[id]));
            }
        });
    
        function validarCampo(campo, regla) {
            let mensajeError = "";
            const valor = campo.value.trim();
        
            if (regla.requerido && valor === "") {
                mensajeError = "Este campo es obligatorio.";
            } else if (regla.min && valor.length < regla.min) {
                mensajeError = regla.mensaje;
            } else if (regla.max && valor.length > regla.max) {
                mensajeError = regla.mensaje;
            } else if (regla.regex && !regla.regex.test(valor)) {
                mensajeError = regla.mensaje;
            } else if (regla.confirmar) {
                const campoConfirmar = document.getElementById(regla.confirmar);
                if (campoConfirmar && valor !== campoConfirmar.value) {
                    mensajeError = regla.mensaje;
                }
            } else if (regla.valores && !regla.valores.includes(valor)) {
                mensajeError = regla.mensaje;
            }
        
            mostrarError(campo, mensajeError);
        }
    
        function mostrarError(campo, mensaje) {
            // Primero elimina cualquier mensaje de error existente
            const existingErrors = campo.closest('.col-md-4').querySelectorAll('.error-text');
            existingErrors.forEach(error => error.remove());
            
            // Solo crea y muestra un nuevo mensaje si hay un error
            if (mensaje) {
                const errorElement = document.createElement("small");
                errorElement.classList.add("error-text");
                errorElement.textContent = mensaje;
                
                // Inserta el mensaje después del input-group (o después del campo si no hay input-group)
                const inputGroup = campo.closest('.input-group');
                if (inputGroup) {
                    inputGroup.parentNode.insertBefore(errorElement, inputGroup.nextSibling);
                } else {
                    campo.parentNode.appendChild(errorElement);
                }
        
                // Agrega la clase is-invalid al campo
                campo.classList.add('is-invalid');
            } else {
                // Si no hay mensaje de error, remueve la clase is-invalid
                campo.classList.remove('is-invalid');
            }
        }
    });
</script>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .error-text {
        display: block;
        color: #dc3545;
        margin-top: 0.25rem;
        font-size: 0.875em;
    }
</style>

@endsection
