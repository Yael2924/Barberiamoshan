@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<section class="crear-inventario">
    <h1><strong>{{ $producto_id ? 'Agregar Inventario' : 'Nuevo Inventario' }}</strong></h1>
    <div id="error-inventario-live" class="alert alert-danger mb-3" style="display: none;"></div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="formulario-inventario" action="{{ route('inventarios.store') }}" method="POST">
        @csrf
        
        <div class="row">
            @if($producto_id)
                <input type="hidden" name="productos[0][id]" value="{{ $producto_id }}">
                <div class="col-md-3">
                    <label class="form-label">Producto:</label>
                    <input type="text" class="form-control" value="{{ $producto->nombre }}" readonly>
                </div>
            @endif

            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control" required>
            </div>
        </div>
        <br>

        @if(!$producto_id)
        <div class="col-md-3" style="max-width: 310px;">
            <label for="producto_nombre" class="form-label">Buscar Producto</label>
            <div style="position: relative;">
                <input type="text" class="form-control" id="producto_nombre" 
                       placeholder="Escribe el nombre del producto" onkeyup="buscarProductos()">
                <ul id="sugerencias_productos" style="display: none; position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-top: none; list-style: none; padding: 0; margin: 0;"></ul>
            </div>
        </div>

        <br>

        <!-- Añade este campo justo después de tu campo de búsqueda por nombre -->
        <div class="col-md-3" style="max-width: 310px;">
            <label for="producto_codigo" class="form-label">Buscar por Código</label>
            <div style="position: relative;">
                <input type="text" class="form-control" id="producto_codigo" 
                    placeholder="Escribe el código de barras" 
                    onkeyup="buscarPorCodigo()" 
                    onkeypress="agregarPorCodigo(event)">
                <ul id="sugerencias_codigo" style="display: none; position: absolute; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto; background: white; border: 1px solid #ddd; border-top: none; list-style: none; padding: 0; margin: 0;"></ul>
            </div>
        </div>

        <br>

        <div class="table-responsive mb-3">
            <table class="table table-bordered" id="tabla_productos_inventario">
                <thead>
                    <tr>
                        <th>Codigo de Barras</th>
                        <th>Producto</th>
                        <th>Stock a Agregar</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Productos se agregarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>
        @else
        <div class="col-md-3" style="max-width: 310px;">
            <label for="stock" class="form-label">Cantidad a Agregar</label>
            <input type="number" name="productos[0][stock]" class="form-control" min="1" required>
        </div>
        @endif

        <br>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('inventarios.index', ['producto_id' => $producto_id ?? null]) }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</section>

@if(!$producto_id)
<script>
    const productos = @json($productos ?? []);
    let productosSeleccionados = [];

    // Añade estas funciones al final de tu script
    function agregarPorCodigo(event) {
        // Verificar si se presionó Enter (código 13)
        if (event.keyCode === 13) {
            event.preventDefault(); // Prevenir comportamiento por defecto
            
            const codigo = document.getElementById('producto_codigo').value.trim();
            if (!codigo) return;
            
            // Buscar producto por código exacto
            const producto = productos.find(p => p.codigo_barras && p.codigo_barras.toString() === codigo);
            
            if (producto) {
                // Verificar si ya fue agregado
                if (productosSeleccionados.some(p => p.id === producto.id)) {
                    alert('Este producto ya fue agregado');
                    return;
                }
                
                // Agregar a la tabla incluyendo el código de barras
                agregarProductoInventario(producto);
                document.getElementById('producto_codigo').value = '';
            } else {
                alert('No se encontró un producto con ese código de barras');
            }
        }
    }

    // Función original (solo cambia el nombre para claridad)
    function buscarProductos() {
        const input = document.getElementById('producto_nombre');
        const listaSugerencias = document.getElementById('sugerencias_productos');
        buscarPorCampo(input, listaSugerencias, 'nombre');
    }

    // Nueva función para búsqueda por código
    function buscarPorCodigo() {
        const input = document.getElementById('producto_codigo');
        const listaSugerencias = document.getElementById('sugerencias_codigo');
        listaSugerencias.style.display = 'none'; // Ocultar sugerencias para código
    }

    // Función principal de búsqueda (común para ambos casos)
    function buscarPorCampo(input, listaSugerencias, tipoBusqueda) {
        listaSugerencias.innerHTML = '';

        if (input.value.length > 0) {
            const inputValue = input.value.toLowerCase();
            const coincidencias = productos.filter(p => {
                if (tipoBusqueda === 'nombre') {
                    return p.nombre.toLowerCase().includes(inputValue) &&
                        !productosSeleccionados.some(ps => ps.id === p.id);
                } else {
                    return p.codigo_barras && p.codigo_barras.toString().includes(inputValue) &&
                        !productosSeleccionados.some(ps => ps.id === p.id);
                }
            });

            if (coincidencias.length > 0) {
                listaSugerencias.style.display = 'block';
                coincidencias.forEach(producto => {
                    const li = document.createElement('li');
                    li.style.padding = '8px 12px';
                    li.style.cursor = 'pointer';
                    li.style.borderBottom = '1px solid #eee';
                    li.textContent = tipoBusqueda === 'nombre' ? producto.nombre : `${producto.codigo_barras} - ${producto.nombre}`;
                    li.onclick = () => {
                        agregarProductoInventario(producto);
                        listaSugerencias.style.display = 'none';
                        document.getElementById('producto_nombre').value = '';
                        document.getElementById('producto_codigo').value = '';
                    };
                    li.onmouseover = () => li.style.backgroundColor = '#f5f5f5';
                    li.onmouseout = () => li.style.backgroundColor = 'white';
                    listaSugerencias.appendChild(li);
                });
            } else {
                listaSugerencias.style.display = 'none';
            }
        } else {
            listaSugerencias.style.display = 'none';
        }
    }
  

    function agregarProductoInventario(producto) {
        if (productosSeleccionados.some(p => p.id === producto.id)) {
            alert('Este producto ya fue agregado');
            return;
        }

        productosSeleccionados.push({
            id: producto.id,
            nombre: producto.nombre,
            codigo_barras: producto.codigo_barras || '', // Asegúrate de incluir el código de barras
            stock: 1
        });

        actualizarTablaInventario();
        document.getElementById('producto_nombre').value = '';
        document.getElementById('producto_codigo').value = '';
    }

    function actualizarTablaInventario() {
        const tbody = document.querySelector('#tabla_productos_inventario tbody');
        tbody.innerHTML = '';

        productosSeleccionados.forEach((producto, index) => {
            const tr = document.createElement('tr');
            tr.id = `prod_inv_${producto.id}`;
            
            tr.innerHTML = `
                <td>${producto.codigo_barras || ''}</td>
                <td>${producto.nombre}</td>
                <td>
                    <input type="number" class="form-control" 
                        name="productos[${index}][stock]" 
                        value="${producto.stock}" 
                        min="1" required
                        onchange="actualizarCantidad(${producto.id}, this.value)">
                    <input type="hidden" name="productos[${index}][id]" value="${producto.id}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" 
                            onclick="eliminarProducto(${producto.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(tr);
        });
    }

    function actualizarCantidad(id, cantidad) {
        const producto = productosSeleccionados.find(p => p.id === id);
        if (producto) {
            producto.stock = parseInt(cantidad) || 1;
        }
    }

    function eliminarProducto(id) {
        productosSeleccionados = productosSeleccionados.filter(p => p.id !== id);
        actualizarTablaInventario();
    }


    
    document.addEventListener("DOMContentLoaded", function() {
        const formulario = document.getElementById("formulario-inventario");

        // Validación al enviar el formulario
        formulario.addEventListener("submit", function(event) {
            let valido = true;

            // Obtener los campos
            const fecha = document.getElementById("fecha");
            const stock = document.getElementById("stock");

            // Validar fecha
            if (!fecha.value) {
                mostrarError(fecha, "La fecha es obligatoria.");
                valido = false;
            } else {
                ocultarError(fecha);
            }

            // Validación de productos múltiples
            const productosSeleccionados = document.querySelectorAll('[name^="productos["]');
            if (productosSeleccionados.length > 0) {
                productosSeleccionados.forEach((producto, index) => {
                    const cantidadStock = producto.querySelector("input[name^='productos[" + index + "]stock']");
                    if (!cantidadStock.value || parseInt(cantidadStock.value) < 1) {
                        mostrarError(cantidadStock, "El stock debe ser un valor mayor a 0.");
                        valido = false;
                    } else {
                        ocultarError(cantidadStock);
                    }
                });
            }

            // Validación de un solo producto (si no se están enviando productos)
            if (productosSeleccionados.length === 0) {
                if (!stock.value || parseInt(stock.value) < 1) {
                    mostrarError(stock, "La cantidad debe ser mayor a 0.");
                    valido = false;
                } else {
                    ocultarError(stock);
                }
            }

            if (!valido) {
                event.preventDefault();
            }
        });

        // Función para mostrar el mensaje de error
        function mostrarError(campo, mensaje) {
            let errorElement = campo.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains("error-text")) {
                errorElement = document.createElement("small");
                errorElement.classList.add("error-text");
                errorElement.style.color = "red";
                campo.parentNode.appendChild(errorElement);
            }

            errorElement.textContent = mensaje;
            errorElement.style.display = "block";
        }

        // Función para ocultar el mensaje de error
        function ocultarError(campo) {
            let errorElement = campo.nextElementSibling;
            if (errorElement && errorElement.classList.contains("error-text")) {
                errorElement.style.display = "none";
            }
        }
    });

    // Función para mostrar errores de inventario
    function mostrarErrorInventario(mensaje) {
        const errorDiv = document.getElementById('error-inventario-live');
        errorDiv.textContent = mensaje;
        errorDiv.style.display = 'block';
        setTimeout(() => errorDiv.style.display = 'none', 5000);
    }

    // Función modificada para agregar por código (sustituye solo el alert)
    function agregarPorCodigo(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            const codigo = document.getElementById('producto_codigo').value.trim();
            if (!codigo) {
                mostrarErrorInventario('Ingrese un código de barras');
                return;
            }
            
            const producto = productos.find(p => p.codigo_barras && p.codigo_barras.toString() === codigo);
            
            if (producto) {
                if (productosSeleccionados.some(p => p.id === producto.id)) {
                    mostrarErrorInventario('Este producto ya está agregado');
                    return;
                }
                agregarProductoInventario(producto);
                document.getElementById('producto_codigo').value = '';
            } else {
                mostrarErrorInventario('Producto no existe - Verifique el código');
            }
        }
    }

    // Validación mejorada al enviar (sustituye solo el alert)
    document.getElementById('formulario-inventario').addEventListener('submit', function(e) {
        if (productosSeleccionados.length === 0 && !document.querySelector('[name="producto_id"]')) {
            e.preventDefault();
            mostrarErrorInventario('Acción requerida: Agregue al menos 1 producto');
        }
    });
</script>
@endif
@endsection