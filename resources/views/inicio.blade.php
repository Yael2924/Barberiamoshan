@extends('layouts.base')

@section('inicio')
<link rel="stylesheet" href="{{ asset('css/inicio.css') }}">
<link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

@guest

<!-- Sección de Bienvenida con Fade-in -->
<section class="hero fade-in">
    <div class="hero-content">
        <h1 class="bienvenida">¡Bienvenido a Moshan Premium Barber Shop!</h1>
        <br><br><br><br>
        <a href="#agenda-cita" class="btn-custom"><i class="bi bi-whatsapp"></i>  AGENDAR CITA</a>
    </div>
</section>


<!-- Logo en tarjeta con Fade-in -->
<section class="logo-card fade-in section-bg-light">
    <div class="card">
        <img src="{{ asset('img/logo_animado.gif') }}" alt="Logotipo de la Barbería" class="card-img-top logo-img">
    </div>
</section>

<!-- Galería con efectos de Fade-in -->
<section id="medio" class="shopify-section shopify-section--rich-text section-bg-dark">
    <div class="prose text-center fade-in">
        <h1 class="titulo-blanco">Galería</h1>

        <!-- Separador personalizado -->
        <div class="separator"></div>
        
    </div>
    <div class="gallery-container">
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar6.jpg') }}" alt="Imagen 1">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar7.jpg') }}" alt="Imagen 2">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar3.jpg') }}" alt="Imagen 3">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/REMPLAZO2.jpg') }}" alt="Imagen 4">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/REMPLAZO4.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar10.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/REMPLAZO1.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/bar12.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/REMPLAZO3.jpg') }}" alt="Imagen 5">
        </div>
        <div class="gallery-card fade-in">
            <img src="{{ asset('img/afuera.jpg') }}" alt="Imagen 5">
        </div>
    </div>
</section>

<!-- ¿Quiénes Somos? con Slide-in -->
<div id="quienes-somos" class="row justify-content-center align-items-center text-center my-5 fade-in section-bg-light">
    <div class="col-md-5 ps-md-5">
        <h1 class="titulo-negro">¿QUIÉNES SOMOS?</h1>

        <!-- Separador personalizado -->
        <div class="separator-negro"></div>
        <br>
        <p class="contenido-negro">En <strong>Moshan Premium Barber Shop</strong>, redefinimos el concepto de la barbería tradicional con un enfoque premium. Desde 2017, 
            nos hemos dedicado a ofrecer un servicio exclusivo para quienes buscan más que un simple corte: una experiencia 
            de lujo, estilo y cuidado personal.
        </p>
        <p class="contenido-negro"> 
            Cada detalle en <strong>Moshan</strong> está pensado para brindarte una atención de primer nivel. Contamos con barberos expertos 
            que combinan técnicas clásicas y modernas, utilizando solo productos de alta calidad para garantizar un resultado 
            impecable. Nuestro ambiente sofisticado y acogedor está diseñado para que disfrutes de un momento de relajación 
            mientras realzamos tu imagen.
        </p>
    </div>
    <div class="col-md-6">
        <img src="{{ asset('img/barberos.jpg') }}" alt="Barbero" class="img-fluid">
    </div>
</div>

<!-- Mapa de Ubicación con Fade-in -->
<section id="ubicacion" class="row justify-content-center my-5 fade-in section-bg-dark">
    <div class="col-12 col-md-5 mx-auto" id="map" ><br><br></div>
    <div class="col-12 col-md-6 align-self-center" id="contacto">
        <h1 class="titulo-blanco text-center">CONTÁCTANOS</h1>
        
        <!-- Separador personalizado -->
        <div class="separator"></div>

        <br>
        <p class="contenido-blanco text-center"><i class="bi bi-telephone"></i> Teléfono: +52 919 146 6815</p>
        <p class="contenido-blanco text-center" style="cursor: pointer;" onclick="window.open('https://wa.me/5219191466815', '_blank');"><i class="bi bi-whatsapp"></i> WhatsApp: +52 919 146 6815</p>
        <p class="contenido-blanco text-center"><i class="bi bi-geo-alt"></i> Dirección: Av. Primera Nte. Ote. 73-49, Norte,</p>
        <p class="contenido-blanco text-center"> 29950 Ocosingo, Chis.</p>
        <div class="contenido-blanco text-center">
            <br><br>
            <button onclick="abrirGoogleMaps()" class="btn-custom"><i class="bi bi-geo-alt"></i> CÓMO LLEGAR</button>
        </div>
    </div>
</section>

<!-- Agendar Cita con Slide-in -->
<div id="agenda-cita" class="row justify-content-center align-items-center text-center my-5 fade-in section-bg-light">
    <div class="col-md-5 ps-md-5">
        <h1 class="titulo-negro">Agenda tu cita</h1>

        <!-- Separador personalizado -->
        <div class="separator-negro"></div>
        <br>
        <p class="contenido-negro">
            ¿Quieres lucir increíble? Agenda tu cita de forma rápida y sencilla escribiéndonos directamente por WhatsApp. 
            Nuestro equipo estará listo para atenderte y brindarte el mejor servicio en <strong>Moshan Premium Barber Shop.</strong>
        </p>
        <br>
        <a href="https://api.whatsapp.com/send?phone=529191466815&text=Hola%2C%20%C2%A1Me%20interesa%20agendar%20una%20cita%21" target="_blank" class="btn-custom whatsapp-btn">
            <i class="bi bi-whatsapp"></i> AGENDA POR WHATSAPP
        </a>
        <br><br>
    </div>
    <div class="col-md-6">
        <img src="{{ asset('img/WhatsApp-portrait.png') }}" alt="Imagen WhatsApp" class="img-fluid app-movil-img">
    </div>
</div>


<!-- CSS para el separador -->
<style>
    .separator {
        width: 100px;  /* Ancho de la línea */
        height: 2px;  /* Grosor de la línea */
        background-color: white;  /* Color de la línea */
        margin: 10px auto;  /* Centrar horizontalmente */
    }

    .separator-negro {
        width: 100px;  /* Ancho de la línea */
        height: 2px;  /* Grosor de la línea */
        background-color: black;  /* Color de la línea */
        margin: 10px auto;  /* Centrar horizontalmente */
    }
    
    @media (max-width: 768px) {
        #map {
            height: 300px; /* Reduce el tamaño del mapa en pantallas más pequeñas */
        }

        .contenido-blanco {
            font-size: 1rem; /* Ajusta el tamaño del texto en móviles */
        }

        .btn-custom {
            font-size: 0.9rem; /* Ajusta el tamaño del botón */
        }
    }



    /* ESTILOS DEL BOTON DE AGENDA POR WHATSAPP */
    .whatsapp-btn {
        background-color: #25D366;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .whatsapp-btn:hover {
        background-color: #128C7E;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }
    
    .whatsapp-btn::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.2);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .whatsapp-btn:hover::after {
        opacity: 1;
        transform: scale(1.5);
    }


    /* ESTILOS DEL BOTON FLOTANTE DE WHATSAPP */
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 25px;
        right: 25px;
        background-color: #25D366;
        color: white;
        border-radius: 50%;
        text-align: center;
        font-size: 30px;
        z-index: 1000;
        box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .whatsapp-float:hover {
        background-color: #128C7E;
        transform: scale(1.1);
    }

    /* Botón flotante para subir */
    .scroll-top {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 60px;
        height: 60px;
        background-color: #000;
        color: #fff;
        border-radius: 50%;
        font-size: 32px;
        text-align: center;
        line-height: 60px;
        z-index: 999;
        transition: all 0.3s ease;
        box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
        display: none;
    }

    .scroll-top:hover {
        background-color: #333;
        transform: scale(1.1);
    }

</style>


<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.key') }}&loading=async&callback=initMap" async defer></script>

<script>
    function initMap() {
        const location = { lat: 16.909292, lng: -92.094195 };
        const map = new google.maps.Map(document.getElementById('map'), {
            center: location,
            zoom: 14,
            mapTypeId: 'roadmap'
        });

        const marker = new google.maps.Marker({
            position: location,
            map: map,
            title: "Estamos aquí"
        });

        const mapTypeControlDiv = document.createElement("div");
        mapTypeControlDiv.innerHTML = ` 
            <button onclick="setMapType('roadmap')" class="btn btn-sm btn-secondary">🛣 Normal</button>
            <button onclick="setMapType('satellite')" class="btn btn-sm btn-secondary">🛰 Satélite</button>
        `;
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(mapTypeControlDiv);

        window.setMapType = function (type) {
            map.setMapTypeId(type);
        }
    }

    function abrirGoogleMaps() {
        const destinoLat = 16.909292;  // Tus coordenadas actuales
        const destinoLng = -92.094195;
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    if (isIOS) {
                        // Abre la app de Maps en iOS con ruta desde la ubicación actual
                        window.location.href = `maps://maps.google.com/maps?daddr=${destinoLat},${destinoLng}&saddr=${latitude},${longitude}`;
                    } else {
                        // Comportamiento actual para Android/otros navegadores
                        window.open(`https://www.google.com/maps/dir/?api=1&origin=${latitude},${longitude}&destination=${destinoLat},${destinoLng}&travelmode=driving`, '_blank');
                    }
                },
                (error) => {
                    // Si el usuario rechaza la geolocalización o falla
                    if (isIOS) {
                        window.location.href = `maps://maps.google.com/maps?daddr=${destinoLat},${destinoLng}`;
                    } else {
                        window.open(`https://www.google.com/maps/dir/?api=1&destination=${destinoLat},${destinoLng}&travelmode=driving`, '_blank');
                    }
                }
            );
        } else {
            // Navegadores sin soporte de geolocalización
            if (isIOS) {
                window.location.href = `maps://maps.google.com/maps?daddr=${destinoLat},${destinoLng}`;
            } else {
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${destinoLat},${destinoLng}&travelmode=driving`, '_blank');
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const fadeElements = document.querySelectorAll('.fade-in');
        const slideElements = document.querySelectorAll('.slide-in');
        const galleryCards = document.querySelectorAll('.gallery-card');

        // Configuración del Intersection Observer
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                } else {
                    entry.target.classList.remove('visible');
                }
            });
        }, { threshold: 0.5 });

        // Observar los elementos con las clases deseadas
        fadeElements.forEach(el => observer.observe(el));
        slideElements.forEach(el => observer.observe(el));
        galleryCards.forEach(el => observer.observe(el));
    });
    
</script>

@endguest


<!-- Botón de WhatsApp flotante -->
<a href="https://api.whatsapp.com/send?phone=529191466815&text=Hola%2C%20%C2%A1Me%20interesa%20agendar%20una%20cita%21" 
   class="whatsapp-float" target="_blank" aria-label="Agendar por WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<!-- Botón flotante para volver arriba -->
<a href="#" class="scroll-top" title="Volver arriba">
    <i class="bi bi-arrow-up-short"></i>
</a>

<script>
    // Mostrar el botón al hacer scroll
    window.addEventListener("scroll", function () {
        const scrollBtn = document.querySelector(".scroll-top");
        if (window.scrollY > 300) {
            scrollBtn.style.display = "block";
        } else {
            scrollBtn.style.display = "none";
        }
    });

    // Scroll suave al hacer clic
    document.querySelector(".scroll-top").addEventListener("click", function (e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
</script>


@endsection
