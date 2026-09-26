# Barbería Moshan

## Descripción del proyecto
Barbería Moshan es una aplicación web desarrollada para facilitar la administración y gestión de una barbería.
El sistema permite centralizar diferentes procesos del negocio, como la gestión de barberos, servicios, productos, inventario y ventas, además de permitir la generación de reportes en formato PDF.
La aplicación cuenta con una página principal de acceso público donde los visitantes pueden conocer información sobre la barbería, los servicios y los barberos y la ubicación del establecimiento.
También se integraron diferentes servicios externos, como Google Maps, Google Calendar, con el propósito de ampliar las funcionalidades de la aplicación.


# Funciones principales
## Página principal
La aplicación cuenta con una sección pública donde los visitantes pueden consultar información de Barbería Moshan.
Entre sus funciones se encuentran:
* Información general de la barbería.
* Presentación de la empresa.
* Información sobre los servicios.
* Información de contacto.
* Ubicación de la barbería mediante Google Maps.

## Gestión de barberos
Permite administrar la información de los barberos de la empresa.
Funciones:
* Registrar barberos.
* Consultar información.
* Editar registros.
* Eliminar registros.
* Administrar la información del personal.

## Gestión de servicios
Permite administrar los servicios ofrecidos por la barbería.
Funciones:
* Registrar servicios.
* Consultar servicios.
* Modificar servicios.
* Eliminar servicios.
* Administrar precios e información de los servicios.

## Gestión de productos
Permite administrar los productos comercializados por la barbería.
Funciones:
* Registrar productos.
* Consultar productos.
* Modificar productos.
* Eliminar productos.
* Administrar información de los productos.

## Gestión de inventario
Permite llevar un control de los productos disponibles en la barbería.
Funciones:
* Consultar productos.
* Controlar existencias.
* Actualizar cantidades.
* Dar seguimiento a los productos.
* Relacionar las existencias con las ventas realizadas.
* Generar reportes.

## Gestión de ventas
El sistema permite registrar las ventas realizadas en la barbería.
### Ventas de productos
Permite registrar los productos vendidos y las cantidades correspondientes.
### Ventas de servicios
Permite registrar los servicios realizados y su importe.
Además, el sistema permite:
* Consultar las ventas.
* Visualizar el detalle de una venta.
* Generar recibos.
* Generar reportes de ventas.

## Generación de reportes
El sistema permite generar reportes en formato PDF a partir de la información registrada.
Los reportes pueden utilizarse para consultar, almacenar o imprimir información relacionada con los diferentes módulos de la aplicación.

## Google Maps
Se integró Google Maps para mostrar la ubicación de Barbería Moshan dentro de la aplicación.
Esto permite que los visitantes puedan identificar fácilmente la ubicación del establecimiento.

## Google Calendar
La aplicación cuenta con integración con Google Calendar para trabajar con información relacionada con la programación de citas y eventos.


# Tecnologías implementadas
* **Laravel** 11.31 Framework principal para el desarrollo de la aplicación
* **PHP** ^8.2 Lenguaje principal del backend  
* **MariaDB/MySQL** --  Sistema gestor de base de datos
* **Blade**  Laravel 11.31  Motor de plantillas para las vistas 
* **Tailwind CSS** ^3.1 Diseño y estilos de la interfaz 
* **JavaScript** --  Interactividad del lado del cliente 
* **Alpine.js** ^3.4.2 Componentes e interacciones del frontend
* **Vite** ^6.0 Compilación y administración de recursos frontend 
* **Axios** ^1.7.4  Solicitudes HTTP desde el frontend
* **LaravelSanctum** ^4.0 Autenticación y protección de la aplicación 
* **GoogleAPIClient** ^2.18 Integración con servicios de Google 
* **LaravelDomPDF** --  Generación de documentos PDF 
* **SpatieLaravelBackup** --  Gestión de respaldos 
* **Git** --  Control de versiones  
* **GitHub** -- Repositorio y colaboración  
### Arquitectura
El proyecto utiliza la arquitectura MVC (Modelo-Vista-Controlador) proporcionada por Laravel.
* Modelos: gestionan la información y comunicación con la base de datos.
* Vistas: presentan la información mediante Blade.
* Controladores: procesan las solicitudes y contienen la lógica correspondiente a cada funcionalidad.


# Requerimientos para la instalación
Para ejecutar Barbería Moshan de manera local se requiere:
* PHP 8.2 o superior
* Composer
* MariaDB o MySQL
* Node.js
* NPM
* Git
* Servidor local como XAMPP o equivalente.
* Navegador web actualizado.
Se recomienda utilizar una versión de Node.js compatible con Vite 6.
Para verificar las herramientas instaladas:
```bash
php -v
```
```bash
composer -V
```
```bash
node -v
```
```bash
npm -v
```
```bash
git --version
```


# Instalación
## 1. Clonar el repositorio
Clonar el repositorio desde GitHub:
```bash
git clone URL_DEL_REPOSITORIO
```
Ingresar a la carpeta del proyecto:
```bash
cd Barberia-Moshan
```

## 2. Instalar las dependencias de PHP
Ejecutar:
```bash
composer install
```
Esto instalará las dependencias especificadas en `composer.json`.
---

## 3. Instalar las dependencias del frontend
Ejecutar:
```bash
npm install
```
Esto instalará las dependencias especificadas en `package.json`.

## 4. Crear el archivo `.env`
Copiar el archivo `.env.example`:

### Windows
```bash
copy .env.example .env
```

### Linux / macOS
```bash
cp .env.example .env
```

Después, abrir el archivo `.env` y configurar los datos de la aplicación.

## 5. Configurar la base de datos
Crear una base de datos en MariaDB/MySQL.
Por ejemplo:
```text
barberia_moshan
```
Después, configurar la conexión en el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=barberia_moshan
DB_USERNAME=root
DB_PASSWORD=
```
> Los datos de usuario y contraseña deben corresponder a la configuración de MariaDB/MySQL utilizada en el equipo.

## 6. Generar la clave de Laravel
Ejecutar:
```bash
php artisan key:generate
```

## 7. Ejecutar las migraciones
Crear las tablas necesarias de la base de datos:
```bash
php artisan migrate
```
Si el proyecto cuenta con información inicial mediante seeders:
```bash
php artisan migrate --seed
```

## 8. Configurar las APIs y servicios externos
Para utilizar todas las funcionalidades de Barbería Moshan es necesario configurar las credenciales correspondientes a los servicios externos utilizados por la aplicación.
Los principales servicios que requieren configuración son:
Google Maps
Google Calendar
Las credenciales deben almacenarse en el archivo .env y no deben publicarse directamente en el repositorio.

### Obtener credenciales para Google Maps
Para utilizar Google Maps es necesario obtener una API Key desde Google Cloud Console: 

* Ingresar a Google Cloud Console.
* Crear un nuevo proyecto o seleccionar un proyecto existente.
* Ir a APIs y servicios → Biblioteca.
* Buscar y habilitar las APIs necesarias para Google Maps.
* Ir a APIs y servicios → Credenciales.
* Seleccionar Crear credenciales → Clave de API.
* Copiar la API Key generada.
* Configurar la clave en el archivo .env.

Ejemplo:
GOOGLE_MAPS_API_KEY=TU_API_KEY
Se recomienda restringir la API Key al proyecto y a los servicios que realmente utiliza la aplicación para evitar un uso no autorizado.

### Obtener credenciales para Google Calendar
Para utilizar la integración con Google Calendar se requiere configurar un proyecto en Google Cloud Console y crear credenciales de OAuth 2.0.
* Ingresar a Google Cloud Console.
* Crear o seleccionar el proyecto utilizado para Barbería Moshan.
* Ir a APIs y servicios → Biblioteca.
* Buscar Google Calendar API.
* Habilitar la Google Calendar API.
* Ir a APIs y servicios → Pantalla de consentimiento de OAuth.
* Configurar la información solicitada de la aplicación.
* Ir a APIs y servicios → Credenciales.
* Seleccionar Crear credenciales → ID de cliente de OAuth.
* Seleccionar el tipo de aplicación correspondiente.
* Configurar las URI de redireccionamiento autorizadas de acuerdo con la configuración del proyecto.
* Crear las credenciales y obtener el Client ID y Client Secret.
* Configurar estos valores en el archivo .env.
Ejemplo:
GOOGLE_CLIENT_ID=TU_CLIENT_ID
GOOGLE_CLIENT_SECRET=TU_CLIENT_SECRET
GOOGLE_REDIRECT_URI=TU_REDIRECT_URI

La aplicación utiliza estas credenciales para realizar el proceso de autenticación y permitir la interacción con Google Calendar.

### Seguridad de las credenciales
Las credenciales y claves privadas utilizadas por estos servicios no deben incluirse directamente en el código fuente ni publicarse en GitHub.
El archivo .env debe mantenerse fuera del repositorio y únicamente se debe proporcionar un archivo .env.example con los nombres de las variables necesarias, pero sin sus valores reales.
Ejemplo:
GOOGLE_MAPS_API_KEY=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

## 9. Compilar los recursos frontend
Para generar los archivos necesarios para producción:
```bash
npm run build
```
Durante el desarrollo se puede utilizar:
```bash
npm run dev
```
## 10. Iniciar la aplicación
Ejecutar:
```bash
php artisan serve
```
La aplicación estará disponible normalmente en:
```text
http://127.0.0.1:8000
```
Abrir la dirección desde un navegador para acceder a Barbería Moshan.
```