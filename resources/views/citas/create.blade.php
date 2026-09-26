@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<!-- Vista para Crear una Nueva Cita -->
<section class="crear-cita">
    <h1><strong>Agendar Cita</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('citas.store') }}" method="POST">
        @csrf

        <!-- Cliente -->
        <div class="row mt-2">
            <div class="col-md-4 position-relative">
                <label for="nombre_cli" class="form-label">Nombre del Cliente</label>
                <input type="text" class="form-control" id="nombre_cli" name="nombre_cli" value="{{ old('nombre_cli') }}"
                    placeholder="Debe tener entre 5 y 50 caracteres y solo letras." 
                    minlength="5" maxlength="50" 
                    pattern=".{5,50}" 
                    title="El nombre debe tener entre 5 y 50 caracteres." 
                    autocomplete="off"
                    required>
            </div>

            <div class="col-md-4">
                <label for="telefono" class="form-label">Teléfono del Cliente <a style="color: red;">(OPCIONAL)</a></label>
                <input type="tel" name="telefono" id="telefono" class="form-control" 
                placeholder="Debe contener 10 dígitos." value="{{ old('telefono') }}"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    autocomplete="off">
            </div>

            <div class="col-md-4">
                <label for="barbero_id">Barbero</label>
                <select name="barbero_id" id="barbero_id" class="form-control" required>
                    <option value="">Seleccione un barbero</option>
                    @foreach($barberos as $barbero)
                        <option value="{{ $barbero->id }}" {{ old('barbero_id') == $barbero->id ? 'selected' : '' }}>{{ $barbero->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label for="fecha">Selecciona la fecha de la Cita</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>            
        
            <div class="col-md-4">
                <label for="hora">Seleccione la hora de la cita</label>
                <select name="hora" id="hora" class="form-control" required>
                    <option value="">Elige una de las horas disponibles</option>
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
    // Obtener las horas disponibles cuando se seleccione el barbero y la fecha
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
                horaSelect.innerHTML = '<option value="">Elige una de las horas disponibles</option>'; // Limpiar las opciones

                data.forEach(horario => {
                    let option = document.createElement('option');
                    option.value = horario.hora;

                    // Convertir la hora a formato AM/PM (ajustando la zona horaria)
                    let [hours, minutes] = horario.hora.split(':');
                    let date = new Date();
                    date.setHours(parseInt(hours), parseInt(minutes), 0, 0); // Usar la hora y minuto directamente

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


    //         fetch(`/get-horas-disponibles?fecha=${fecha}&barbero_id=${barberoId}`)
    // .then(response => response.json())
    // .then(data => {
    //     console.log(data); // Verifica la estructura de los datos
    //     if (Array.isArray(data)) {
    //         data.forEach(item => {
    //             console.log(item);
    //         });
    //     } else {
    //         console.error('La respuesta no es un arreglo:', data);
    //     }
    // })
    // .catch(error => {
    //     console.error('Error fetching horas disponibles:', error);
    // });

    document.querySelector('form').addEventListener('submit', function(e) {
        const nombreCliente = document.getElementById('cliente_buscador').value.trim();
        const usuarioId = document.getElementById('usuario_id').value;
        
        // Verificar si hay un ID pero el nombre no coincide con ningún cliente
        if (usuarioId) {
            const clienteSeleccionado = todosClientes.find(c => c.id == usuarioId);
            if (!clienteSeleccionado || clienteSeleccionado.nombre !== nombreCliente) {
                e.preventDefault();
                alert('ERROR: El nombre del cliente no coincide con un cliente válido. Por favor, seleccione un cliente de la lista.');
                document.getElementById('cliente_buscador').value = '';
                document.getElementById('usuario_id').value = '';
                document.getElementById('cliente_buscador').focus();
                return false;
            }
        } else if (nombreCliente) {
            e.preventDefault();
            alert('ERROR: Debe seleccionar un cliente de la lista, no escribir manualmente.');
            document.getElementById('cliente_buscador').value = '';
            document.getElementById('cliente_buscador').focus();
            return false;
        }
    });

    // Limpiar el ID cuando se modifica manualmente el input
    document.getElementById('cliente_buscador').addEventListener('input', function() {
        const input = this.value;
        if (!input) {
            document.getElementById('usuario_id').value = '';
        } else {
            // Verificar si el texto actual coincide con algún cliente
            const clienteValido = todosClientes.some(c => c.nombre === input);
            if (!clienteValido) {
                document.getElementById('usuario_id').value = '';
            }
        }
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