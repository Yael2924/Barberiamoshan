@extends('layouts.base')

@section('contenido')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

<!-- Vista para Editar un Usuario -->
<section class="editar-usuario">
    <h1><strong>Editar Usuario</strong></h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if( sizeof($errors)>0 )
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuarios.update',$usuario->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-md-2">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $usuario->nombre) }}" 
                    placeholder="Debe tener entre 10 y 50 caracteres y solo letras." 
                    minlength="10" maxlength="50" 
                    pattern=".{10,50}" 
                    title="El nombre debe tener entre 10 y 50 caracteres." 
                    required>
            </div>
    
            <!-- Campo Nombre de Usuario -->
            <div class="col-md-4 mb-md-2">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" 
                       value="{{ old('nombre_usuario', $usuario->nombre_usuario) }}" 
                       placeholder="Debe tener entre 5 y 20 caracteres (sin espacios)" 
                       minlength="5" maxlength="20" 
                       pattern="^[A-Za-z0-9_]{5,20}$" 
                       title="El nombre de usuario debe tener entre 5 y 20 caracteres sin espacios (solo letras, números y guiones bajos)" 
                       required>
                <small class="text-danger error-text"></small>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $usuario->email) }}" 
                placeholder="ejemplo@correo.com." required>
            </div>       
    
            <!-- Campo Contraseña Actual -->
            <div class="col-md-4 mb-md-2">
                <label for="current_password" class="form-label">Contraseña Actual</label>
                <div class="input-group">
                    <input type="password" name="current_password" id="current_password" class="form-control"
                           placeholder="Contraseña actual si deseas cambiarla">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-muted">Introduce tu contraseña actual solo si vas a cambiar la contraseña.</small>
                <small class="text-danger error-text"></small>
            </div>
    
            <!-- Campo Nueva Contraseña -->
            <div class="col-md-4 mb-md-2">
                <label for="password" class="form-label">Nueva Contraseña <a style="color: red;">(OPCIONAL)</a></label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Ingrese contraseña segura (mínimo 6 caracteres)">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-danger error-text"></small>
            </div>
    
            <!-- Campo Confirmar Contraseña -->
            <div class="col-md-4 mb-md-2">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                           placeholder="Repita la nueva contraseña">
                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <small class="text-danger error-text"></small>
            </div>
    
            <div class="col-md-4 mb-md-2">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}" 
                    placeholder="Debe contener 10 dígitos."
                    minlength="10"
                    maxlength="10" 
                    pattern="[0-9]{10}" 
                    title="El teléfono debe tener 10 dígitos."
                    required>
            </div>

            <!-- Campo Rol -->
            <div class="col-md-4 mb-md-4">
                <label for="rol" class="form-label">Rol</label>
                <select name="rol" id="rol" class="form-control">
                    <option value="Administrador" {{ $usuario->rol == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Barbero" {{ $usuario->rol == 'Barbero' ? 'selected' : '' }}>Barbero</option>
                    {{-- <option value="Cliente" {{ $usuario->rol == 'Cliente' ? 'selected' : '' }}>Cliente</option> --}}
                </select>
                {{-- @if($usuario->rol == 'Barbero' && $usuario->barberos()->exists())
                    <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                    <small class="text-danger">No puedes cambiar el rol de este usuario porque está registrado como barbero.</small>
                @endif --}}
            </div>
    
            <!-- Campo Rol oculto e inmodificable -->
            {{-- <input type="hidden" name="rol" value="{{ $usuario->rol }}"> --}}
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
    // 1. Validación de nombre de usuario sin espacios
    const nombreUsuarioInput = document.getElementById('nombre_usuario');
    if (nombreUsuarioInput) {
        nombreUsuarioInput.addEventListener('input', function() {
            this.value = this.value.replace(/\s/g, '');
        });
    }
    
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
            current_password: { 
                requeridoSi: "password", 
                mensaje: "Debes ingresar tu contraseña actual si deseas cambiarla." 
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
        };
    
    // 3. Configurar eventos de validación
        Object.keys(reglas).forEach(id => {
            const campo = document.getElementById(id);
            if (campo) {
                campo.addEventListener("input", () => validarCampo(campo, reglas[id]));
                campo.addEventListener("blur", () => validarCampo(campo, reglas[id]));
            }
        });
    
        // 4. Función de validación
        function validarCampo(campo, regla) {
            let mensajeError = "";
            const valor = campo.value.trim();
    
            if (regla.requerido && valor === "") {
                mensajeError = "Este campo es obligatorio.";
            } else if (regla.requeridoSi) {
                const otroCampo = document.getElementById(regla.requeridoSi);
                if (otroCampo && otroCampo.value.trim() !== "" && valor === "") {
                    mensajeError = regla.mensaje;
                }
            } else if (regla.min && valor.length < regla.min) {
                mensajeError = regla.mensaje;
            } else if (regla.max && valor.length > regla.max) {
                mensajeError = regla.mensaje;
            } else if (regla.regex && valor !== "" && !regla.regex.test(valor)) {
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
    
        // 5. Mostrar errores - Versión corregida
        function mostrarError(campo, mensaje) {
            // Subimos dos niveles para llegar al contenedor col-md-4
            let contenedorPadre = campo.closest('.col-md-4');
            if (!contenedorPadre) contenedorPadre = campo.parentElement;
            
            // Buscamos el elemento de error (ahora lo colocaremos después del input-group)
            let errorElement = contenedorPadre.querySelector('.error-text');
            
            if (!errorElement) {
                errorElement = document.createElement('small');
                errorElement.className = 'text-danger error-text';
                
                // Insertamos después del input-group (o del campo si no hay input-group)
                const inputGroup = campo.closest('.input-group');
                if (inputGroup) {
                    inputGroup.insertAdjacentElement('afterend', errorElement);
                } else {
                    campo.insertAdjacentElement('afterend', errorElement);
                }
            }
            
            errorElement.textContent = mensaje || '';
            campo.classList.toggle('is-invalid', !!mensaje);
        }
    
        // 6. Función para mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    
        // 7. Validación al enviar el formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validar todos los campos requeridos
            Object.keys(reglas).forEach(id => {
                const campo = document.getElementById(id);
                if (campo && campo.required && campo.value.trim() === "") {
                    mostrarError(campo, "Este campo es obligatorio.");
                    if (isValid) {
                        campo.focus();
                        isValid = false;
                    }
                }
            });
    
            // Validación especial para contraseña
            const passwordField = document.getElementById('password');
            const currentPasswordField = document.getElementById('current_password');
            
            if (passwordField && passwordField.value.trim() !== "" && 
                currentPasswordField && currentPasswordField.value.trim() === "") {
                mostrarError(currentPasswordField, "Debe ingresar su contraseña actual para cambiarla.");
                if (isValid) {
                    currentPasswordField.focus();
                    isValid = false;
                }
            }
    
            if (!isValid) {
                e.preventDefault();
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
    margin-top: 0.25rem;
    font-size: 0.875em;
}
</style>
    
@endsection