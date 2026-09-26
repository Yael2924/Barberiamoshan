@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section>
    <h1><strong>Gestión de Horarios de Trabajo</strong></h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Formulario para agregar un nuevo horario -->
    <form id="form-agregar-horario" action="{{ route('horarios.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-12 col-md-3 mb-3">
                <label for="dia">Día:</label>
                <select name="dia" id="dia" class="form-control" required>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                    <option value="Sábado">Sábado</option>
                    <option value="Domingo">Domingo</option>
                </select>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <label for="hora">Hora:</label>
                <input type="time" name="hora" id="hora" class="form-control" step="3600" required>
            </div>
            <div class="col-12 col-md-2 mb-3">
                <button type="submit" class="btn btn-primary mt-md-4 w-100">Agregar Horario</button>
            </div>
        </div>
    </form>
    
    <!-- Lista de horarios agrupados por día -->
    <div class="row">
        @foreach($horariosPorDia as $dia => $horarios)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ $dia }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Habilitado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($horarios as $horario)
                                <tr>
                                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($horario->hora)->format('g:i a') }}</td>
                                    <td>
                                        <input type="checkbox" 
                                            class="habilitar-horario" 
                                            data-id="{{ $horario->id }}" 
                                            {{ is_null($horario->deleted_at) ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Script JavaScript directamente en la vista -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Validación del formulario para agregar un nuevo horario
        const form = document.getElementById('form-agregar-horario');
        if (form) {
            const horaInput = document.getElementById('hora');

            form.addEventListener('submit', function(event) {
                const hora = horaInput.value;
                const minutos = hora.split(':')[1];

                // Verificar que la hora sea en punto
                if (minutos !== '00') {
                    alert('La hora debe ser en punto (por ejemplo, 8:00, 9:00, etc.).');
                    event.preventDefault(); // Evitar que se envíe el formulario
                    return;
                }

                const dia = document.getElementById('dia').value;

                // Verificar si el horario ya existe
                fetch(`/horarios/verificar?dia=${dia}&hora=${hora}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.existe) {
                            alert('El horario para este día y hora ya está registrado.');
                            event.preventDefault(); // Evitar que se envíe el formulario
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        }

        // 2. Manejo de los checkboxes para habilitar/deshabilitar horarios
        document.querySelectorAll('.habilitar-horario').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const horarioId = this.getAttribute('data-id');
                const habilitado = this.checked;

                // Determinar la acción (restaurar o eliminar)
                const url = habilitado 
                    ? `/horarios/${horarioId}/restore`  // Restaurar (habilitar)
                    : `/horarios/${horarioId}/delete`;  // Eliminar (deshabilitar)

                // Enviar una solicitud AJAX para actualizar el estado del horario
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Horario actualizado correctamente');
                        alert('Horario actualizado correctamente.');
                        // Recargar la página para reflejar los cambios
                        window.location.reload();
                    } else {
                        console.error('Error al actualizar el horario');
                        alert('Error al actualizar el horario.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ocurrió un error al procesar la solicitud.');
                });
            });
        });
    });
</script>
@endsection