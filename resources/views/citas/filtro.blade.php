@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="reporte">
    <h1><strong>Reporte de Citas</strong></h1>

    <form action="{{ route('citas.filtro') }}" method="GET">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="fecha_inicio">Fecha Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                    value="{{ request('fecha_inicio') }}" required>
            </div>

            <div class="col-md-4">
                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                    value="{{ request('fecha_fin') }}" required min="{{ request('fecha_inicio') }}">
            </div>

            <!-- Input de búsqueda de cliente - Versión corregida -->
            <div class="col-md-4 position-relative">
                <label for="cliente_buscador" class="form-label">Cliente</label>
                <input type="text" class="form-control" id="cliente_buscador" 
                       placeholder="Buscar cliente..." 
                       autocomplete="off" oninput="buscarCliente()"
                       value="{{ $clienteSeleccionado->nombre ?? '' }}">
                
                <!-- Lista de sugerencias -->
                <ul id="sugerencias_clientes" class="list-group shadow-sm" 
                    style="display: none; position: absolute; width: 100%; z-index: 1000; max-height: 300px; overflow-y: auto;">
                </ul>
                
                <!-- Campo oculto para el ID del cliente - IMPORTANTE: mismo name que usabas con el select -->
                <input type="hidden" name="usuario_id" id="usuario_id" value="{{ request('usuario_id') }}">
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-md-4">
                <label for="barbero_id">Barbero:</label>
                <select id="barbero_id" name="barbero_id" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($barberos as $barbero)
                        <option value="{{ $barbero->id }}" 
                            @selected(request('barbero_id') == $barbero->id) 
                            @if($barbero->trashed()) style="color: red;" @endif>
                            {{ $barbero->nombre }} 
                            @if($barbero->trashed()) (Despedido) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="Completado" {{ request('estado') == 'Completado' ? 'selected' : '' }}>Completado</option>
                    <option value="Pendiente" {{ request('estado') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                </select>
            </div>
        </div>
        
        <br>


        <button type="submit" class="btn btn-primary w-auto">Generar Reporte</button>

        <a href="{{ route('citas.index') }}" class="btn btn-secondary w-auto">Volver</a>

    </form>

    @if (isset($citas) && count($citas) > 0 && request('fecha_inicio') && request('fecha_fin'))
        <form action="{{ route('citas.exportarPDF') }}" method="POST" target="_blank">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
            <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
            <input type="hidden" name="usuario_id" value="{{ request('usuario_id') }}">
            <input type="hidden" name="barbero_id" value="{{ request('barbero_id') }}">
            <input type="hidden" name="estado" value="{{ request('estado') }}">

            <button type="submit" class="btn btn-danger mt-3">
                <i class="bi bi-file-earmark-pdf"></i> Exportar a PDF
            </button>
        </form>

        <h2 class="mt-4">Resultados</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Barbero</th>
                    <th>Estado</th>
                    <th>Reagendar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                <tr>
                    <td>{{ $cita->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->hora)->format('g:i a') }}</td>
                    <td>{{ $cita->usuario->nombre }}</td>
                    <td>{{ $cita->usuario->telefono }}</td>
                    <td>{{ $cita->usuario->email }}</td>
                    <td>{{ $cita->barbero->nombre }}</td>
                    <td>{{ $cita->estado }}</td>
                    <td class="texto-tabla">
                        @if ($cita->estado === 'Completado')
                        <button class="btn btn-success" disabled>
                            <i class="bi bi-clock"></i> Cita completada
                        </button>
                        @else
                            <!-- Botón de editar con ícono -->
                            <a href="{{ route('citas.edit', $cita->id) }}" class="btn btn-warning">
                                <i class="bi bi-clock"></i>  <!-- Icono de editar -->
                            </a>
                        @endif          
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Agregar paginación debajo de la tabla -->
        <div class="paginacion-container d-flex justify-content-center">
            {{ $citas->appends(request()->query())->links() }}
        </div>

        {{-- <h3>Total de Citas: ${{ $totalVentas }}</h3> --}}
    
    @else
        <div class="alert alert-warning mt-3">
            No se encontraron citas en el rango seleccionado.
        </div>
    @endif
</section>
<script>
    // Datos de clientes desde PHP
    const todosClientes = @json($usuarios);

    function buscarCliente() {
        const input = document.getElementById('cliente_buscador').value.toLowerCase();
        const lista = document.getElementById('sugerencias_clientes');
        lista.innerHTML = '';

        // Si se borra el input, limpiamos el ID oculto
        if (input === '') {
            document.getElementById('usuario_id').value = '';
        }

        // Normalizar el input para ignorar acentos
        const inputNormalizado = input.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        
        if (inputNormalizado.length >= 1) {
            const resultados = todosClientes.filter(cliente => {
                // Normalizar el nombre del cliente para ignorar acentos
                const nombreNormalizado = cliente.nombre.toLowerCase()
                                        .normalize("NFD")
                                        .replace(/[\u0300-\u036f]/g, "");
                return nombreNormalizado.includes(inputNormalizado);
            });

            if (resultados.length > 0) {
                lista.style.display = 'block';
                resultados.forEach(cliente => {
                    const item = document.createElement('li');
                    item.className = 'list-group-item list-group-item-action py-2';
                    
                    // Mostrar si está eliminado
                    const eliminado = cliente.deleted_at ? '(Eliminado)' : '';
                    
                    item.innerHTML = `
                        <div class="fw-bold">${cliente.nombre} ${eliminado}</div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>📞 ${cliente.telefono || 'Sin teléfono'}</span>
                            <span>✉️ ${cliente.email || 'Sin correo'}</span>
                        </div>
                    `;
                    item.onclick = () => {
                        document.getElementById('usuario_id').value = cliente.id;
                        document.getElementById('cliente_buscador').value = cliente.nombre;
                        lista.style.display = 'none';
                    };
                    lista.appendChild(item);
                });
            } else {
                lista.innerHTML = '<li class="list-group-item">No se encontraron clientes</li>';
                lista.style.display = 'block';
            }
        } else {
            lista.style.display = 'none';
        }
    }

    // Cerrar al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#cliente_buscador') && !e.target.closest('#sugerencias_clientes')) {
            document.getElementById('sugerencias_clientes').style.display = 'none';
        }
    });

    // Cargar selección previa si hay un cliente seleccionado
    @if(request('usuario_id'))
    document.addEventListener('DOMContentLoaded', () => {
        const clienteId = {{ request('usuario_id') }};
        const cliente = todosClientes.find(c => c.id == clienteId);
        if (cliente) {
            document.getElementById('cliente_buscador').value = cliente.nombre;
        }
    });
    @endif
</script>
@endsection
