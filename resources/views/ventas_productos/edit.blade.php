@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<style>
    .mayoreo-toggle-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .mayoreo-toggle-label {
        font-weight: bold;
        font-size: 1.1rem;
        color: #343a40;
        margin-right: 15px;
    }
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: #28a745;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
    .mayoreo-status {
        display: inline-block;
        margin-left: 15px;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: bold;
        background-color: #6c757d;
        color: white;
    }
    input:checked ~ .mayoreo-status {
        background-color: #28a745;
    }
    .mayoreo-info {
        margin-top: 10px;
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>

<section class="editar-venta_productos">
    <h1><strong>Editar Venta de Productos #{{ $venta->id }}</strong></h1>

    @if( sizeof($errors) > 0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ventas_productos.update', $venta->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Fila para buscar por código de barras y nombre del producto -->
        <div class="row mb-3">
            <!-- Entrada para buscar por código de barras -->
            <div class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="codigo_barras" class="form-label">Código de Barras</label>
                <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" 
                       placeholder="Ingrese el código de barras del producto" 
                       autocomplete="off" onkeypress="buscarProducto(event)">
            </div>

            <!-- Entrada para buscar por nombre del producto -->
            <div class="col-12 col-md-6">
                <label for="producto_nombre" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="producto_nombre" name="producto_nombre" 
                       placeholder="Buscar producto por nombre" onkeyup="buscarPorNombre()">
                <ul id="sugerencias_nombre" class="list-group" style="display: none; position: absolute; z-index: 10;"></ul>
            </div>            
        </div>

        <!-- Interruptor para activar/desactivar mayoreo -->
        <div class="mayoreo-toggle-container">
            <div class="d-flex align-items-center">
                <span class="mayoreo-toggle-label">Precios por mayoreo:</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggleMayoreo" onchange="toggleMayoreoChanged()" {{ $venta->mayoreo_habilitado ? 'checked' : '' }}>
                    <span class="toggle-slider"></span>
                </label>
                <span id="mayoreoStatus" class="mayoreo-status">{{ $venta->mayoreo_habilitado ? 'ACTIVO' : 'INACTIVO' }}</span>
            </div>
            <div class="mayoreo-info">
                Cuando está activo, productos con 6+ unidades aplican precio especial por mayoreo.
            </div>
        </div>

        <!-- Tabla para productos seleccionados -->
        <table class="table table-bordered" id="tabla_productos">
            <thead>
                <tr>
                    <th>Código de Barras</th>
                    <th>Nombre del Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->productos as $producto)
                <tr id="fila_{{ $producto->id }}" 
                    data-precio-normal="{{ $producto->precio }}" 
                    data-precio-mayoreo="{{ $producto->mayoreo }}" 
                    data-stock="{{ $producto->stock + $producto->pivot->cantidad }}">
                    <td>{{ $producto->codigo_barras }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>
                        <input type="number" class="form-control" id="cantidad_{{ $producto->id }}" 
                               name="productos[{{ $producto->id }}][cantidad]" 
                               min="1" max="{{ $producto->stock + $producto->pivot->cantidad }}" 
                               value="{{ $producto->pivot->cantidad }}" 
                               oninput="actualizarSubtotal({{ $producto->id }})">
                    </td>
                    <td id="precio_unitario_{{ $producto->id }}">
                        @php
                            $precio = ($venta->mayoreo_habilitado && $producto->pivot->cantidad >= 6) ? $producto->mayoreo : $producto->precio;
                            echo number_format($precio, 2);
                        @endphp
                    </td>
                    <td>
                        <input type="number" class="form-control subtotal" id="subtotal_{{ $producto->id }}" 
                               name="productos[{{ $producto->id }}][subtotal]" 
                               value="{{ number_format($producto->pivot->cantidad * $precio, 2) }}" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger" onclick="eliminarProducto({{ $producto->id }})">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total general -->
        <div class="mb-3 total-general-container">
            <label for="total_general" class="form-label total-label">Total General</label>
            <input type="number" step="0.01" class="total-input" id="total_general" name="total_general" 
                   value="{{ number_format($venta->total, 2) }}" readonly>
        </div>

        <input type="hidden" id="mayoreo_habilitado" name="mayoreo_habilitado" value="{{ $venta->mayoreo_habilitado ? '1' : '0' }}">

        <button type="submit" class="btn btn-primary">Actualizar Venta</button>
        <a href="{{ route('ventas_productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</section>

<script>
    const productos = @json($productos); // Todos los productos disponibles
    const productosEnVenta = @json($venta->productos); // Productos actuales en la venta
    let totalGeneral = parseFloat(document.getElementById('total_general').value);
    let mayoreoActivado = {{ $venta->mayoreo_habilitado ? 'true' : 'false' }};

    // Inicializar el total general
    actualizarTotalGeneral();

    // Buscar producto por código de barras
    function buscarProducto(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const codigoBarras = document.getElementById('codigo_barras').value.trim();
            const producto = productos.find(p => p.codigo_barras === codigoBarras);

            if (producto) {
                agregarProductoTabla(producto);
                document.getElementById('codigo_barras').value = '';
            } else {
                alert('Producto no encontrado.');
            }
        }
    }

    // Buscar productos por nombre
    function buscarPorNombre() {
        const inputNombre = document.getElementById('producto_nombre').value.toLowerCase();
        const listaProductos = document.getElementById('sugerencias_nombre');
        listaProductos.innerHTML = '';

        if (inputNombre.length > 0) {
            const coincidencias = productos.filter(p => 
                p.nombre.toLowerCase().includes(inputNombre) && 
                !document.getElementById(`fila_${p.id}`)
            );

            if (coincidencias.length > 0) {
                listaProductos.style.display = 'block';
                coincidencias.forEach(producto => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.textContent = producto.nombre;
                    li.setAttribute('data-id', producto.id);
                    li.setAttribute('data-codigo', producto.codigo_barras);
                    li.setAttribute('data-precio', producto.precio);
                    li.setAttribute('data-mayoreo', producto.mayoreo);
                    li.setAttribute('data-stock', producto.stock);
                    li.onclick = function() {
                        seleccionarProducto(producto.id);
                    };
                    listaProductos.appendChild(li);
                });
            } else {
                listaProductos.style.display = 'none';
            }
        } else {
            listaProductos.style.display = 'none';
        }
    }

    // Seleccionar producto de las sugerencias
    function seleccionarProducto(productoId) {
        const producto = productos.find(p => p.id === productoId);
        if (producto) {
            agregarProductoTabla(producto);
            document.getElementById('producto_nombre').value = '';
            document.getElementById('sugerencias_nombre').style.display = 'none';
        }
    }

    // Agregar producto a la tabla
    function agregarProductoTabla(producto) {
        if (producto.stock === 0) {
            alert(`El producto "${producto.nombre}" no tiene stock disponible.`);
            return;
        }

        const tablaBody = document.querySelector('#tabla_productos tbody');

        if (document.getElementById(`fila_${producto.id}`)) {
            alert('El producto ya fue agregado.');
            return;
        }

        const fila = document.createElement('tr');
        fila.id = `fila_${producto.id}`;
        fila.dataset.precioNormal = producto.precio;
        fila.dataset.precioMayoreo = producto.mayoreo;
        fila.dataset.stock = producto.stock;

        const cantidadInicial = 1;
        const precioInicial = mayoreoActivado && cantidadInicial >= 6 ? producto.mayoreo : producto.precio;
        const subtotalInicial = cantidadInicial * precioInicial;

        fila.innerHTML = `
            <td>${producto.codigo_barras}</td>
            <td>${producto.nombre}</td>
            <td>
                <input type="number" class="form-control" id="cantidad_${producto.id}" 
                       name="productos[${producto.id}][cantidad]" 
                       min="1" max="${producto.stock}" value="${cantidadInicial}" 
                       oninput="actualizarSubtotal(${producto.id})">
            </td>
            <td id="precio_unitario_${producto.id}">${Number(precioInicial).toFixed(2)}</td>
            <td>
                <input type="number" class="form-control subtotal" id="subtotal_${producto.id}" 
                       name="productos[${producto.id}][subtotal]" 
                       value="${Number(subtotalInicial).toFixed(2)}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="eliminarProducto(${producto.id})">Eliminar</button>
            </td>
        `;

        tablaBody.appendChild(fila);
        actualizarTotalGeneral();
    }

    // Función cuando cambia el toggle de mayoreo
    function toggleMayoreoChanged() {
        mayoreoActivado = document.getElementById('toggleMayoreo').checked;
        document.getElementById('mayoreo_habilitado').value = mayoreoActivado ? '1' : '0';
        
        // Actualizar el estado visual
        const statusElement = document.getElementById('mayoreoStatus');
        statusElement.textContent = mayoreoActivado ? 'ACTIVO' : 'INACTIVO';
        statusElement.style.backgroundColor = mayoreoActivado ? '#28a745' : '#6c757d';
        
        // Actualizar todos los productos en la tabla
        actualizarTodosLosPrecios();
    }

    // Actualizar todos los precios en la tabla
    function actualizarTodosLosPrecios() {
        const filas = document.querySelectorAll('#tabla_productos tbody tr');
        
        filas.forEach(fila => {
            const productoId = fila.id.split('_')[1];
            actualizarSubtotal(productoId);
        });
    }

    // Actualizar subtotal al cambiar cantidad o estado de mayoreo
    function actualizarSubtotal(productoId) {
        const fila = document.getElementById(`fila_${productoId}`);
        if (!fila) return;
        
        const cantidadInput = document.getElementById(`cantidad_${productoId}`);
        const subtotalInput = document.getElementById(`subtotal_${productoId}`);
        const precioUnitarioCell = document.getElementById(`precio_unitario_${productoId}`);
        
        const precioNormal = parseFloat(fila.dataset.precioNormal);
        const precioMayoreo = parseFloat(fila.dataset.precioMayoreo);
        const stockDisponible = parseInt(fila.dataset.stock);
        
        let cantidad = parseInt(cantidadInput.value) || 0;

        // Validar stock
        if (cantidad > stockDisponible) {
            alert(`Solo hay ${stockDisponible} unidades disponibles.`);
            cantidadInput.value = stockDisponible;
            cantidad = stockDisponible;
        }

        // Validar cantidad mínima
        if (cantidad < 1) {
            cantidadInput.value = 1;
            cantidad = 1;
        }

        // Calcular precio según condiciones
        const precioFinal = mayoreoActivado && cantidad >= 6 ? precioMayoreo : precioNormal;

        // Actualizar visualización
        precioUnitarioCell.textContent = precioFinal.toFixed(2);
        subtotalInput.value = (cantidad * precioFinal).toFixed(2);

        actualizarTotalGeneral();
    }

    // Eliminar producto de la tabla
    function eliminarProducto(productoId) {
        const fila = document.getElementById(`fila_${productoId}`);
        fila.remove();
        actualizarTotalGeneral();
    }

    // Actualizar total general
    function actualizarTotalGeneral() {
        const subtotales = document.querySelectorAll('.subtotal');
        totalGeneral = 0;

        subtotales.forEach(input => {
            totalGeneral += parseFloat(input.value) || 0;
        });

        document.getElementById('total_general').value = totalGeneral.toFixed(2);
    }
</script>
@endsection