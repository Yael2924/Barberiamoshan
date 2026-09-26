@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

<section class="crear-venta">
    <h1><strong>Nueva Venta de Servicios</strong></h1>

    <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
        @csrf

        <div class="mb-3">
            <label for="tipo_venta" class="form-label">Tipo de Venta</label>
            <select class="form-control" id="tipo_venta">
                <option value="">Seleccione el tipo de Venta</option>
                <option value="Servicio">Servicio</option>
                <option value="Producto">Producto</option>
            </select>
        </div>

        <div class="mb-3" id="campos_servicio" style="display: none;">
            <label for="servicio" class="form-label">Servicio</label>
            <select class="form-control" id="servicio">
                <option value="">Seleccione el Servicio</option>
                @foreach($servicios as $s)
                <option value="{{$s->nombre}}">{{$s->nombre}}</option>
                @endforeach
            </select>
            <label for="nombre_barbero" class="form-label">Nombre del Barbero</label>
            <select class="form-control" id="nombre_barbero">
                <option value="">Seleccione el Barbero que realizo el Servicio</option>
                @foreach($barberos as $b)
                <option value="{{$b->nombre}}">{{$b->nombre}}</option>
                @endforeach
            </select>
            <label for="precio_servicio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio_servicio" placeholder="Precio del servicio">
            <button type="button" class="btn btn-success mt-2" onclick="agregarFila('servicio')">Añadir Servicio</button>
        </div>

        <div class="mb-3" id="campos_producto" style="display: none;">
            <label for="producto" class="form-label">Producto</label>
            <select class="form-control" id="producto">
                <option value="">Seleccione el Producto</option>
                @foreach($productos as $p)
                <option value="{{$p->nombre}}">{{$p->nombre}}</option>
                @endforeach
            </select>
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="cantidad" placeholder="Cantidad">
            <label for="precio_producto" class="form-label">Precio Unitario</label>
            <input type="number" step="0.01" class="form-control" id="precio_producto" placeholder="Precio unitario">
            <button type="button" class="btn btn-success mt-2" onclick="agregarFila('producto')">Añadir Producto</button>
        </div>

        <!-- Tabla de ventas -->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla_ventas"></tbody>
        </table>

        <h4>Total: $<span id="total">0.00</span></h4>

        <!-- Campo oculto para enviar datos al backend -->
        <input type="hidden" name="detalle_venta" id="detalle_venta">

        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="reset" class="btn btn-warning" onclick="limpiarTabla()">Limpiar</button>
    </form>
</section>

<script>
    const tablaVentas = document.getElementById('tabla_ventas');
    const totalSpan = document.getElementById('total');
    const detalleVentaInput = document.getElementById('detalle_venta');

    document.getElementById('tipo_venta').addEventListener('change', function () {
        const tipoVenta = this.value;
        document.getElementById('campos_servicio').style.display = tipoVenta === 'Servicio' ? 'block' : 'none';
        document.getElementById('campos_producto').style.display = tipoVenta === 'Producto' ? 'block' : 'none';
    });

    function agregarFila(tipo) {
        let nombre, cantidad, precio, subtotal;
        if (tipo === 'servicio') {
            nombre = document.getElementById('Servicio').value;
            cantidad = 1;
            precio = parseFloat(document.getElementById('precio_servicio').value);
            subtotal = precio;
        } else if (tipo === 'producto') {
            nombre = document.getElementById('Producto').value;
            cantidad = parseInt(document.getElementById('cantidad').value);
            precio = parseFloat(document.getElementById('precio_producto').value);
            subtotal = cantidad * precio;
        }

        // Agregar fila a la tabla
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${tipo}</td>
            <td>${nombre}</td>
            <td>${cantidad}</td>
            <td>$${precio.toFixed(2)}</td>
            <td>$${subtotal.toFixed(2)}</td>
            <td><button type="button" class="btn btn-danger" onclick="eliminarFila(this, ${subtotal})">Eliminar</button></td>
        `;
        tablaVentas.appendChild(fila);

        // Actualizar total
        const totalActual = parseFloat(totalSpan.textContent) + subtotal;
        totalSpan.textContent = totalActual.toFixed(2);

        // Actualizar campo oculto
        actualizarDetalleVenta();
    }

    function eliminarFila(boton, subtotal) {
        boton.parentElement.parentElement.remove();
        const totalActual = parseFloat(totalSpan.textContent) - subtotal;
        totalSpan.textContent = totalActual.toFixed(2);
        actualizarDetalleVenta();
    }

    function actualizarDetalleVenta() {
        const filas = tablaVentas.querySelectorAll('tr');
        const detalle = Array.from(filas).map(fila => {
            const celdas = fila.querySelectorAll('td');
            return {
                categoria: celdas[0].textContent,
                nombre: celdas[1].textContent,
                cantidad: parseInt(celdas[2].textContent),
                precio: parseFloat(celdas[3].textContent.replace('$', '')),
                subtotal: parseFloat(celdas[4].textContent.replace('$', '')),
            };
        });
        detalleVentaInput.value = JSON.stringify(detalle);
    }

    function limpiarTabla() {
        tablaVentas.innerHTML = '';
        totalSpan.textContent = '0.00';
        actualizarDetalleVenta();
    }
</script>
@endsection
