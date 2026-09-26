@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<section class="citas">
    <h1><strong>Calendario de Citas</strong></h1>

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

    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('citas.index') }}" method="GET">
                <div class="input-group">
                    <input value="{{ $busqueda }}" type="text" class="form-control" name="busqueda" placeholder="Buscar citas por nombre del Cliente...">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('citas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Agendar Cita
            </a>
        </div>
    </div>

    <!-- Filtros por barbero -->
    <div class="mb-3">
        <label>Filtrar por barbero:</label>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary active" data-barbero="all">Todos</button>
            @foreach($barberos as $barbero)
            <button type="button" class="btn btn-outline-secondary" 
                    data-barbero="{{ $barbero->id }}" 
                    style="border-left-color: {{ $barbero->color }}; border-left-width: 5px;">
                {{ $barbero->nombre }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Calendario -->
    <div id="calendar" class="bg-white p-3 rounded shadow"></div>

    <!-- Modal para detalles de cita -->
    <div class="modal fade" id="citaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Folio:</strong> <span id="modalFolio"></span></p>
                    <p><strong>Cliente:</strong> <span id="modalCliente"></span></p>
                    <p><strong>Teléfono:</strong> <span id="modalTelefono"></span></p>
                    <p><strong>Barbero:</strong> <span id="modalBarbero"></span></p>
                    <p><strong>Fecha:</strong> <span id="modalFecha"></span></p>
                    <p><strong>Hora:</strong> <span id="modalHora"></span></p>
                    <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                </div>
                <div class="modal-footer">
                    <a href="#" id="modalEditLink" class="btn btn-warning">
                        <i class="bi bi-clock"></i> Reagendar
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            slotLabelFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
                meridiem: 'short'
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
                meridiem: 'short'
            },
            events: [
                @foreach($citas as $cita)
                {
                    id: '{{ $cita->id }}',
                    title: '{{ $cita->nombre_cli }} - {{ $cita->barbero->nombre }}',
                    start: '{{ $cita->fecha }}T{{ $cita->hora }}',
                    color: '{{ $cita->barbero->color }}',
                    extendedProps: {
                        folio: '{{ $cita->id }}',
                        cliente: '{{ $cita->nombre_cli }}',
                        telefono: '{{ $cita->telefono }}',
                        barbero: '{{ $cita->barbero->nombre }}',
                        fecha: '{{ \Carbon\Carbon::parse($cita->fecha)->format("d/m/Y") }}',
                        hora: '{{ \Carbon\Carbon::parse($cita->hora)->format("g:i A") }}',
                        estado: '{{ $cita->estado }}',
                        editable: {{ $cita->estado === 'Pendiente' ? 'true' : 'false' }}
                    }
                },
                @endforeach
            ],
            eventClick: function(info) {
                const event = info.event;
                const modal = new bootstrap.Modal(document.getElementById('citaModal'));
                
                document.getElementById('modalFolio').textContent = event.extendedProps.folio;
                document.getElementById('modalCliente').textContent = event.extendedProps.cliente;
                document.getElementById('modalTelefono').textContent = event.extendedProps.telefono;
                document.getElementById('modalBarbero').textContent = event.extendedProps.barbero;
                document.getElementById('modalFecha').textContent = event.extendedProps.fecha;
                document.getElementById('modalHora').textContent = event.extendedProps.hora;
                document.getElementById('modalEstado').textContent = event.extendedProps.estado;
                
                // Controlar visibilidad del botón de reagendar
                const editLink = document.getElementById('modalEditLink');
                if (event.extendedProps.editable === true || event.extendedProps.editable === 'true') {
                    editLink.href = `/citas/${event.id}/edit`;
                    editLink.style.display = 'inline-block';
                } else {
                    editLink.style.display = 'none';
                }
                
                modal.show();
            }
        });

        calendar.render();

        // Filtrado por barbero
        document.querySelectorAll('[data-barbero]').forEach(btn => {
            btn.addEventListener('click', function() {
                const barberoId = this.getAttribute('data-barbero');
                
                // Actualizar botones activos
                document.querySelectorAll('[data-barbero]').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filtrar eventos
                calendar.getEvents().forEach(event => {
                    if (barberoId === 'all') {
                        event.setProp('display', 'auto');
                    } else {
                        const eventBarbero = event.title.split(' - ')[1];
                        const barberoName = document.querySelector(`[data-barbero="${barberoId}"]`).textContent.trim();
                        event.setProp('display', eventBarbero === barberoName ? 'auto' : 'none');
                    }
                });
            });
        });
    });
</script>
@endsection