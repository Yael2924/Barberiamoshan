<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="En Moshan Premium Barber Shop ofrecemos cortes y cuidado personal con un enfoque premium desde 2017. Vive una experiencia de lujo y estilo.">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Moshan Premium Barber Shop</title>
    
    <!-- Precarga de fuentes -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=PT+Serif:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=PT+Serif:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </noscript>
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Hojas de estilo -->
    <link rel="stylesheet" href="{{asset('css/estilos.css')}}">
    <link rel="stylesheet" href="{{asset('css/barberia.css')}}">
    <link rel="stylesheet" href="{{asset('css/all.min.css')}}">
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" type="image/x-icon" href="{{asset('favicon.ico')}}">
    
    <!-- Estilos de fuentes con fallback -->
    <style>
        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', "Arial Narrow", Arial, sans-serif;
            font-display: swap;
        }
        
        .titulo-blanco,
        .titulo-negro,
        .bienvenida,
        .contenido-blanco,
        .contenido-negro {
            font-family: 'PT Serif', Georgia, "Times New Roman", serif;
            font-display: swap;
        }
    </style>
</head>

<style>
    /* Estilo para el menú de usuario */
    .dropdown-menu {
        min-width: 220px;
    }
    
    /* Submenú de configuración */
    .dropdown-submenu .dropdown-menu {
        position: static !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        display: none;
        padding-left: 20px;
    }
    
    /* Ajustar posición de los elementos */
    .dropdown-divider.configuracion-divider {
        display: none;
    }
    
    /* Mostrar el submenú cuando está activo */
    .dropdown-submenu.active .dropdown-menu {
        display: block;
    }
</style>

<body>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <nav class="navbar navbar-expand-lg navbar-dark bg-black">
        <div class="container-fluid">
            <a href="/" class="navbar-brand">
                <img src="{{asset('img/logo_barberia_sin_fondo.png')}}" class="" style="filter: brightness(0) invert(1); width: 220px; height: 60px; border-radius: 5px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menú de navegación a la izquierda -->
                <ul class="navbar-nav me-auto">
                    @if (Auth::check() && Auth::user()->rol === 'Administrador')
                        {{-- <li class="nav-item"><a href="/" class="nav-link"><i class="bi bi-house-fill px-3"></i> <strong>INICIO</strong></a></li> --}}
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="ventasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-cart-fill px-3"></i> <strong>VENTAS</strong>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="ventasDropdown">
                                <li><a class="dropdown-item" href="/ventas_servicios"><i class="bi bi-scissors px-3"></i>Ventas de Servicios</a></li>
                                <li><a class="dropdown-item" href="/ventas_productos"><i class="bi bi-box-seam px-3"></i>Ventas de Productos</a></li>
                                <li><a class="dropdown-item" href="/ventas"><i class="bi bi-currency-dollar px-3"></i>Ventas Totales</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a href="/barberos" class="nav-link"><i class="bi bi-people-fill px-3"></i> <strong>BARBEROS</strong></a></li>
                        <li class="nav-item"><a href="/productos" class="nav-link"><i class="bi bi-box-seam px-3"></i> <strong>PRODUCTOS</strong></a></li>
                        <li class="nav-item"><a href="/servicios" class="nav-link"><i class="bi bi-scissors px-3"></i> <strong>SERVICIOS</strong></a></li>
                        <li class="nav-item"><a href="/usuarios" class="nav-link"><i class="bi bi-person-fill-add px-3"></i> <strong>USUARIOS</strong></a></li>
                        <li class="nav-item"><a href="/citas" class="nav-link"><i class="bi bi-calendar px-3"></i> <strong>CITAS</strong></a></li>
                    @endif

                    @if (Auth::check() && Auth::user()->rol === 'Barbero')
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="ventasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-cart-fill px-3"></i> <strong>VENTAS</strong>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="ventasDropdown">
                                <li><a class="dropdown-item" href="/ventas_servicios"><i class="bi bi-scissors px-3"></i>Ventas de Servicios</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>

                <!-- Sección derecha con usuario -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <a href="{{ route('login') }}" class="btn-custom">
                            <i class="bi bi-box-arrow-in-right me-2"></i> INICIAR SESIÓN
                        </a>                    
                    @endguest
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle userx" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill px-2 " style="font-size: 20px; color:white;"></i><strong>{{ Auth::user()->nombre }}</strong>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" style="text-transform: uppercase">
                                        <i class="bi bi-person-check px-2 " style="font-size: 25px; color:black;"></i><strong>{{ Auth::user()->rol }}</strong>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('usuarios.edit_sinrol', Auth::user()->id) }}">
                                        <i class="bi bi-person-circle me-2"></i>Editar Perfil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>

                                @if (Auth::check() && Auth::user()->rol === 'Administrador')
                                <li>
                                    <form action="{{ route('backup.create') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-database-fill me-2"></i>Respaldar Base de Datos
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">
                                        <i class="bi bi-gear-fill me-2"></i>Configuración
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="/configuracion">
                                                <i class="bi bi-cash-coin me-2"></i>Ganancia del Barbero
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="/horarios">
                                                <i class="bi bi-clock-fill me-2"></i>Horario
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-fluid mt-0" style="--bs-gutter-x: 0;">
        @yield('inicio')
    </div>

    <div class="container mt-4">
        @yield('contenido')
    </div>

    

    <br>
    <br>

    <br>
    <br>
    

    <footer class="bg-black text-center text-white py-3 mt-auto">
        <div class="container">
            <p class="mb-0">© 2025 <strong>Moshan Premium Barber Shop.</strong> Todos los derechos reservados.</p>
            <div class="social-icons">
                <a href="https://www.facebook.com/p/Moshan-Barberia-Tradicional-100063674163898/" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.tiktok.com/@moshan.premium.ba" target="_blank" aria-label="Tik Tok"><i class="bi bi-tiktok"></i></a>
                <a href="https://wa.me/5219191466815" target="_blank" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Control del submenú de configuración
        document.querySelectorAll('.dropdown-submenu > a').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.parentElement.classList.toggle('active');
                
                // Mover el divisor después del submenú
                const divider = this.closest('.dropdown-menu').querySelector('.dropdown-divider.configuracion-divider');
                const submenu = this.nextElementSibling;
                const logoutItem = this.closest('.dropdown-menu').querySelector('form[action*="logout"]').parentElement;
                
                if (this.parentElement.classList.contains('active')) {
                    // Insertar el divisor y logout después del submenú
                    submenu.insertAdjacentElement('afterend', divider);
                    submenu.insertAdjacentElement('afterend', logoutItem);
                    divider.style.display = 'block';
                } else {
                    // Volver a la posición original (opcional)
                    // Puedes omitir esta parte si quieres que se quede abajo
                }
            });
        });
        
        // Cerrar al hacer clic fuera
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown-submenu').forEach(function(item) {
                item.classList.remove('active');
            });
        });
    });
</script>