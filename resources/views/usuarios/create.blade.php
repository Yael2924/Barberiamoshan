@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Vista para Crear un Nuevo Usuario -->
<section class="crear-usuario">
    <h1><strong>Nuevo Usuario</strong></h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                    placeholder="Debe tener entre 10 y 50 caracteres y solo letras." 
                    minlength="10" maxlength="50" 
                    pattern=".{10,50}" 
                    title="El nombre debe tener entre 10 y 50 caracteres." 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="{{ old('nombre_usuario') }}" 
                    placeholder="Debe tener entre 5 y 20 caracteres (sin espacios)" 
                    minlength="5" maxlength="20" 
                    pattern="^[^\s]+$" 
                    title="El nombre de usuario debe tener entre 5 y 20 caracteres sin espacios" 
                    required>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" 
                placeholder="ejemplo@correo.com." value="{{ old('email') }}" required maxlength="100">
            </div>

            <div class="col-md-4 mb-md-2">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control"  
                           placeholder="Ingrese contraseña segura mínimo 6 caracteres." aria-describedby="passwordHelp" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-md-4 mb-md-2">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <div class="input-group mb-1"> <!-- Agregamos mb-1 para espacio antes del mensaje -->
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                           placeholder="La contraseña debe coincidir." aria-describedby="password_confirmationHelp" required>
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <!-- El mensaje de error aparecerá aquí automáticamente -->
            </div>

            <div class="col-md-4 mb-md-2">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" 
                placeholder="Debe contener 10 dígitos." value="{{ old('telefono') }}"
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required>
            </div>
    
            <div class="col-md-4 mb-md-4">
                <label for="rol" class="form-label">Rol</label>
                <select name="rol" id="rol" class="form-control" required>
                    <option value="">Selecciona un rol</option>
                    <option value="Administrador" {{ old('rol') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Barbero" {{ old('rol') == 'Barbero' ? 'selected' : '' }}>Barbero</option>
                    {{-- <option value="Cliente" {{ old('rol') == 'Cliente' ? 'selected' : '' }}>Cliente</option> --}}
                </select>
            </div>
        </div>
        
        <br>

        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
        <button type="reset" class="btn btn-warning">Limpiar Formulario</button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>

        <br>
        <br>
    </form>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const reglas = {
            nombre: { 
                regex: /^[A-Za-zÀ-ÿÑñ\s]{10,50}$/, 
                mensaje: "Debe tener entre 10 y 50 caracteres y solo letras.", 
                requerido: true 
            },
            nombre_usuario: { 
                regex: /^[A-Za-z0-9_]{5,20}$/,
                mensaje: "Debe tener entre 5 y 20 caracteres sin espacios (solo letras, números y _)", 
                requerido: true 
            },
            email: { 
                regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 
                mensaje: "Correo inválido (ejemplo@correo.com).", 
                requerido: true 
            },
            password: { 
                regex: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/, 
                mensaje: "Mínimo 6 caracteres, una mayúscula, una minúscula, un número y un carácter especial.", 
                opcional: true 
            },
            password_confirmation: { 
                confirmar: "password", 
                mensaje: "Las contraseñas no coinciden.", 
                opcional: true 
            },
            telefono: { 
                regex: /^[0-9]{10}$/, 
                mensaje: "Debe contener 10 dígitos y solo números.", 
                requerido: true 
            },
            rol: { 
                valores: ["Administrador", "Barbero", "Cliente"], 
                mensaje: "Rol inválido.", 
                requerido: true 
            }
        };
        
        // Agrega esto después del DOMContentLoaded
        const nombreUsuarioInput = document.getElementById('nombre_usuario');
        if (nombreUsuarioInput) {
            nombreUsuarioInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\s/g, '');
            });
        }
    
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
    
    // Función para mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
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