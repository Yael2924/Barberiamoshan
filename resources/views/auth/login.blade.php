<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Moshan Premium Barber Shop</title>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('../img/fondo.jpg'); /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        /* Overlay oscuro para el fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6); /* Fondo oscuro semi-transparente */
            z-index: 1;
        }

        .login-container {
            background-color: rgba(42, 42, 42, 0.9); /* Fondo semi-transparente */
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            z-index: 2;
            margin: 1rem; /* Margen adicional para móviles */
        }

        .login-container h1 {
            font-family: 'Georgia', serif;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #E6E0D0;
            text-transform: uppercase;
        }

        .logo {
            width: 100%; /* El logo ocupa el 100% del contenedor */
            max-width: 350px; /* Tamaño máximo del logo */
            height: auto;
            filter: brightness(0) invert(1); /* Convierte el logo a blanco */
            margin-bottom: 1.5rem;
        }

        .login-container label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #E6E0D0;
            text-align: left;
        }

        .login-container input {
            width: calc(100% - 1.5rem); /* Ajuste para igualar el espacio en ambos lados */
            padding: 0.75rem;
            margin: 0 0 1rem 0; /* Eliminamos márgenes laterales */
            border: 1px solid #444;
            border-radius: 5px;
            background-color: rgba(51, 51, 51, 0.8); /* Fondo semi-transparente */
            color: #fff;
            font-size: 1rem;
        }

        .login-container input:focus {
            border-color: #E6E0D0;
            outline: none;
        }

        .login-container button {
            width: 100%;
            padding: 0.75rem;
            background-color: #E6E0D0;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #d6c7b8;
        }

        .login-container .error-message {
            color: #ff4d4d;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: left;
        }

        .login-container .forgot-password {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #E6E0D0;
            text-decoration: none;
            display: inline-block;
        }

        .login-container .forgot-password:hover {
            text-decoration: underline;
        }

        /* Botón Volver al Inicio */
        .back-button {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 0.5rem 1rem;
            background-color: rgba(51, 51, 51, 0.8);
            color: #E6E0D0;
            border: 1px solid #E6E0D0;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background-color: rgba(230, 224, 208, 0.2);
            color: #fff;
        }

        /* Estilos responsivos */
        @media (max-width: 480px) {
            .login-container {
                padding: 1rem; /* Reducir el padding en móviles */
                margin: 0.5rem; /* Reducir el margen en móviles */
            }

            .login-container h1 {
                font-size: 1.5rem; /* Tamaño de fuente más pequeño en móviles */
            }

            .logo {
                max-width: 250px; /* Tamaño del logo ajustado para móviles */
            }

            .login-container input {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .login-container button {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .blocked-form {
                opacity: 0.7;
            }

            .blocked-form input,
            .blocked-form button {
                pointer-events: none;
                background-color: rgba(51, 51, 51, 0.5) !important;
                border-color: #ff4d4d !important;
            }
            
            .blocked-form button {
                background-color: rgba(230, 224, 208, 0.5) !important;
                cursor: not-allowed !important;
            }
        }

        .caps-warning {
            color: #ffcc00;
            font-size: 0.8rem;
            margin-top: -0.5rem;
            margin-bottom: 0.5rem;
            text-align: left;
            display: none; /* Oculto por defecto */
        }
        
       /* Botón flotante */
      .floating-button {
    position: fixed;
    top: 15px;
    left: 15px;
    width: auto;
    height: auto;
    background: linear-gradient(145deg, rgb(255, 255, 255), rgb(255, 255, 255));
    border-radius: 30px;
    display: flex;
    align-items: center;
    padding: 10px 20px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    text-decoration: none;
    transition: all 0.3s ease;
    z-index: 1000;
    overflow: hidden;
    gap: 10px;
}

/* Imagen del ícono */
.floating-button img {
    width: 30px;
    height: 30px;
    transition: transform 0.3s ease;
}

/* Texto "Inicio" */
.floating-button span {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    font-family: 'Arial', sans-serif;
}

/* Efecto hover */
.floating-button:hover {
    background: linear-gradient(145deg, #f0e6d8, rgb(255, 255, 255));
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
}

.floating-button:hover img {
    transform: scale(1.1);
}

/* Responsividad */
@media (max-width: 768px) {
    .floating-button {
        padding: 8px 15px;
    }

    .floating-button img {
        width: 25px;
        height: 25px;
    }

    .floating-button span {
        font-size: 16px;
    }
}

/* Estilo mejorado para el checkbox de Recordar sesión */
input[type="checkbox"]:checked {
    background-color: #E6E0D0;
}

input[type="checkbox"]:checked::after {
    content: "✓";
    position: absolute;
    color: #E6E0D0;
    font-size: 18px;
    font-weight: bold;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
}

input[type="checkbox"]:hover {
    border-color: #d6c7b8;
}

input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(230, 224, 208, 0.3);
}
    </style>
</head>
<body>
    
   <!-- Botón flotante con ícono e identificación clara -->
<a href="/" class="floating-button">
    <img src="../img/home.png" alt="Inicio">
    <span>Inicio</span>
</a>

    <div class="login-container">
        <h1>Moshan Premium Barber Shop</h1>
        <br>
        <img src="../img/logo_barberia_sin_fondo.png" alt="Logo" class="logo"> <!-- Ruta del logo -->

        <!-- Mensaje único de bloqueo/intentos -->
        @if(isset($isBlocked) && $isBlocked)
            <div class="error-message" id="blockCounter">
                Demasiados intentos. Intente nuevamente en <span id="countdown-minutes">{{ $blockTime }}</span> minutos.
            </div>
        @elseif(isset($remainingAttempts) && $remainingAttempts < 5 && !($errors->has('nombre_usuario')))
            <div style="color: #ffcc00; margin-bottom: 1rem;">
                Intentos restantes: {{ $remainingAttempts }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" @if(isset($isBlocked) && $isBlocked && $blockTime > 0) class="blocked-form" @endif>
            @csrf

            
            <!-- Nombre de Usuario -->
            <div>
                <label for="nombre_usuario">Usuario</label>
                <input id="nombre_usuario" type="text" name="nombre_usuario" value="{{ old('nombre_usuario') }}" required autofocus autocomplete="off" autocapitalize="off" spellcheck="false"
                    @if(isset($isBlocked) && $isBlocked) readonly @endif>
                <div id="caps-warning-user" class="caps-warning">⚠️ Mayúsculas activadas</div>
                @if($errors->has('nombre_usuario') && !(isset($isBlocked) && $isBlocked))
                    <div class="error-message">{{ $errors->first('nombre_usuario') }}</div>
                @endif
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    @if(isset($isBlocked) && $isBlocked) readonly @endif>
                <div id="caps-warning" class="caps-warning">⚠️ Mayúsculas activadas</div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                <input id="remember" name="remember" type="checkbox" 
                       style="width: 18px; height: 18px; cursor: pointer; margin: 0; appearance: none;
                              -webkit-appearance: none; background-color: rgba(51, 51, 51, 0.8);
                              border: 1px solid #E6E0D0; border-radius: 3px;
                              transition: all 0.2s ease; position: relative;">
                <label for="remember" style="font-size: 0.9rem; color: #E6E0D0; cursor: pointer; user-select: none;">
                    Recordar sesión
                </label>
            </div>                                 

            <br>
            <!-- Botón de Iniciar Sesión -->
            <div>
                <button type="submit" @if(isset($isBlocked) && $isBlocked) disabled @endif>Iniciar Sesión</button>
            </div>
            <br>
            <!--div>
                <!-- Botón de Volver al Inicio - Posicionado arriba del logo -->
                <!--button href="{ route('inicio') }}" class="back-button">Volver al inicio</button>
            </div-->

            

            <!-- Enlace para recuperar contraseña -->
            <!-- Enlace de recuperación (añádelo donde prefieras) -->
            <!--div class="text-center mt-3">
                <a href="{ route('password.request') }}" class="forgot-password">
                    ¿Olvidaste tu contraseña?
                </a>
            </div-->
        </form>
    </div>

    <script>
       // Contador regresivo mejorado
        document.addEventListener('DOMContentLoaded', function() {
            const minutesElement = document.getElementById('countdown-minutes');
            if (!minutesElement) return;

            let minutesLeft = parseInt(minutesElement.textContent);
            
            if (minutesLeft <= 0) {
                removeBlockedState();
                initializeCapsLockDetection();
                return;
            }

            const interval = setInterval(() => {
                minutesLeft--;
                minutesElement.textContent = minutesLeft;
                
                if (minutesLeft <= 0) {
                    clearInterval(interval);
                    removeBlockedState();
                    // Forzar recarga con parámetro para evitar caché
                    setTimeout(() => {
                        window.location.href = window.location.pathname + '?unblocked=true&ts=' + Date.now();
                    }, 500);
                }
            }, 60000);

            function removeBlockedState() {
                const form = document.querySelector('form');
                if (form) form.classList.remove('blocked-form');
                
                document.querySelectorAll('input').forEach(input => {
                    input.readOnly = false;
                });
                
                const button = document.querySelector('button[type="submit"]');
                if (button) button.disabled = false;
                
                const blockMessage = document.getElementById('blockCounter');
                if (blockMessage) blockMessage.style.display = 'none';
            }

            // Inicializar detección de mayúsculas solo si no hay bloqueo
            @if(!(isset($isBlocked) && $isBlocked))
            initializeCapsLockDetection();
            @endif
        });
        
        // Función para inicializar detección de mayúsculas
        function initializeCapsLockDetection() {
                // Función mejorada para detectar mayúsculas
                function checkCapsLock(field) {
                    try {
                        const event = new KeyboardEvent('keytest', {
                            key: 'CapsLock',
                            keyCode: 20,
                            which: 20,
                            code: 'CapsLock'
                        });
                        return event.getModifierState('CapsLock');
                    } catch (e) {
                        console.error("Error al detectar mayúsculas:", e);
                        return false;
                    }
                }

                // Mostrar/ocultar advertencia
                function toggleCapsWarning(field, show) {
                    const warningId = field.id === 'nombre_usuario' ? 'caps-warning-user' : 'caps-warning';
                    const warning = document.getElementById(warningId);
                    if (warning) warning.style.display = show ? 'block' : 'none';
                }

                // Configurar eventos para un campo
                function setupField(field) {
                    if (!field) return;
                    
                    // Al enfocar
                    field.addEventListener('focus', function() {
                        toggleCapsWarning(this, checkCapsLock(this));
                    });
                    
                    // Al escribir
                    field.addEventListener('keyup', function(e) {
                        toggleCapsWarning(this, e.getModifierState('CapsLock'));
                    });
                    
                    // Al perder foco
                    field.addEventListener('blur', function() {
                        toggleCapsWarning(this, false);
                    });
                }

                // Inicializar campos
                const userField = document.getElementById('nombre_usuario');
                const passField = document.getElementById('password');
                
                if (userField) setupField(userField);
                if (passField) setupField(passField);

                // Verificación INMEDIATA después de recarga
                if (new URLSearchParams(window.location.search).has('unblocked')) {
                    setTimeout(() => {
                        const activeField = document.activeElement;
                        if (activeField && (activeField.id === 'nombre_usuario' || activeField.id === 'password')) {
                            toggleCapsWarning(activeField, checkCapsLock(activeField));
                        }
                    }, 300);
                }
            }

        // Solo ejecuta si NO hay bloqueo por intentos
        @if(!(isset($isBlocked) && $isBlocked))
            document.addEventListener('DOMContentLoaded', function() {
                const nombreUsuario = document.getElementById('nombre_usuario');
                const password = document.getElementById('password');
                
                // Limpia TODOS los mensajes de error al escribir
                function clearErrors() {
                    const errorMessages = document.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => msg.remove());
                    
                    // Opcional: Remueve el mensaje de intentos restantes si se escribe
                    const attemptsMsg = document.querySelector('.attempts-msg');
                    if (attemptsMsg) attemptsMsg.remove();
                }
                
                // Eventos para ambos campos
                if (nombreUsuario) nombreUsuario.addEventListener('input', clearErrors);
                if (password) password.addEventListener('input', clearErrors);
                
                // Limpiar errores al hacer clic en los campos (opcional)
                [nombreUsuario, password].forEach(field => {
                    if (field) field.addEventListener('click', clearErrors);
                });
            });
        @endif

        // Detección de mayúsculas mejorada - Versión final
        @if(!(isset($isBlocked) && $isBlocked))
        document.addEventListener('DOMContentLoaded', function() {
            // Función principal mejorada
            function checkCapsLock(field, event = null) {
                const warningId = field.id === 'nombre_usuario' ? 'caps-warning-user' : 'caps-warning';
                const warningElement = document.getElementById(warningId);
                if (!warningElement) return;
                
                const isCapsOn = event ? event.getModifierState('CapsLock') : 
                                    (new KeyboardEvent('keydown')).getModifierState('CapsLock');
                warningElement.style.display = isCapsOn ? 'block' : 'none';
            }

            // Configuración de eventos
            function setupFieldEvents(field) {
                if (!field) return;
                
                // Verificar al escribir
                field.addEventListener('keyup', (e) => checkCapsLock(field, e));
                
                // Verificar al enfocar
                field.addEventListener('focus', () => checkCapsLock(field));
                
                // Ocultar al perder foco
                field.addEventListener('blur', () => {
                    const warningId = field.id === 'nombre_usuario' ? 'caps-warning-user' : 'caps-warning';
                    const warningElement = document.getElementById(warningId);
                    if (warningElement) warningElement.style.display = 'none';
                });
            }

            // Configurar ambos campos
            const userField = document.getElementById('nombre_usuario');
            const passField = document.getElementById('password');
            
            setupFieldEvents(userField);
            setupFieldEvents(passField);

            // Verificación INMEDIATA si el campo ya está enfocado
            if (document.activeElement === userField || document.activeElement === passField) {
                checkCapsLock(document.activeElement);
            }

            // Doble verificación después de 300ms (para casos extremos)
            setTimeout(() => {
                if (document.activeElement === userField || document.activeElement === passField) {
                    checkCapsLock(document.activeElement);
                }
            }, 300);
        });
        @endif


        // Guarda la URL de inicio (cámbiala si es necesario)
        const inicioURL = "/"; 

        // Agrega un estado en el historial
        history.pushState(null, null, location.href);

        window.onpopstate = function () {
            // Cuando presionan atrás, redirige al inicio y limpia datos
            location.href = inicioURL;
        };

        // Opcional: Si quieres que tampoco puedan ir hacia adelante
        history.pushState(null, null, location.href);

    </script>
</body>
</html>