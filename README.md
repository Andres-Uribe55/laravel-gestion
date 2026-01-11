# Sistema de Gestión de Productos y Usuarios con Auditoría

Este es un sistema web robusto desarrollado en **Laravel** que permite la administración integral de productos y usuarios. Incluye un sistema de roles y permisos, carga de imágenes segura y un módulo de auditoría detallado que registra todas las actividades críticas del sistema.

## 🚀 Tecnologías Utilizadas

-   **Backend:** PHP 8.2+, Laravel 11
-   **Frontend Interactivo:** Livewire 3
-   **Estilos:** Materialize CSS / TailwindCSS
-   **Base de Datos:** MySQL
-   **Autenticación:** Laravel Jetstream / Fortify
-   **Roles y Permisos:** Spatie Laravel Permission
-   **Auditoría:** Owen-it Laravel Auditing

## 📋 Requisitos Previos

Para ejecutar este proyecto localmente, necesitas tener instalado:

-   [PHP](https://www.php.net/) >= 8.2
-   [Composer](https://getcomposer.org/)
-   [Node.js](https://nodejs.org/) & NPM
-   [MySQL](https://www.mysql.com/)

## 🛠️ Instalación y Configuración

Sigue estos pasos para clonar y configurar el entorno de trabajo:

1.  **Clonar el repositorio**

    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd Gestion
    ```

2.  **Instalar dependencias de PHP**

    ```bash
    composer install
    ```

3.  **Instalar dependencias de JavaScript**

    ```bash
    npm install
    npm run build
    ```

4.  **Configurar el entorno**
    Copia el archivo de ejemplo y genera la clave de aplicación:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configurar Base de Datos**

    -   Crea una base de datos vacía en MySQL (ej. `gestion_db`).
    -   Abre el archivo `.env` y configura tus credenciales:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=gestion_db
        DB_USERNAME=tu_usuario
        DB_PASSWORD=tu_contraseña
        ```

6.  **Ejecutar Migraciones y Seeders**
    Esto creará las tablas y los usuarios de prueba:

    ```bash
    php artisan migrate --seed
    ```

7.  **Vincular el Storage (¡Importante!)**
    Para que las imágenes de los productos sean visibles públicamente:
    ```bash
    php artisan storage:link
    ```

## ▶️ Ejecución

Inicia el servidor de desarrollo:

```bash
php artisan serve
```

El proyecto estará disponible en: [http://localhost:8000](http://localhost:8000)

## 🔐 Credenciales de Prueba

El comando `db:seed` genera los siguientes usuarios por defecto:

| Rol               | Email               | Contraseña |
| :---------------- | :------------------ | :--------- |
| **Administrador** | `admin@example.com` | `password` |
| **Usuario**       | `user@example.com`  | `password` |

## 📦 Funcionalidades Principales

### 1. Gestión de Productos

-   **CRUD Completo:** Crear, Leer, Actualizar y Eliminar productos.
-   **Imágenes:** Carga de imágenes optimizada usando almacenamiento local (`public/storage`).
-   **Validaciones:** Control estricto de tipos de archivo (imágenes) y tamaño máximo (10MB).

### 2. Gestión de Usuarios (Solo Admin)

-   Administración de cuentas de acceso.
-   Asignación de roles (`admin`, `user`) utilizando **Spatie Permissions**.
-   Protección de rutas para evitar accesos no autorizados.

### 3. Módulo de Auditoría (Logs)

-   Traza inmutable de acciones: Creación, Edición y Eliminación.
-   Modelos Auditados: `Product` y `User`.
-   **Detalles:** Registra QUIÉN hizo el cambio, CUÁNDO, QUÉ valores cambiaron (antes/después) y la IP de origen.
-   Acceso restringido exclusivamente a Administradores.

## 🏗️ Arquitectura y Patrones de Diseño

El proyecto sigue una arquitectura **MVC** potenciada por **Livewire** y el **Patrón de Servicios (Service Pattern)**.

### Patrón de Servicios

La lógica de negocio compleja, como el manejo de archivos, se ha desacoplado de los controladores y componentes.

-   `App\Services\ProductImageService`: Se encarga exclusivamente de la lógica de subida, nombrado único y eliminación de archivos físicos. Esto permite cambiar el sistema de almacenamiento (ej. pasar a S3) sin tocar el código de los componentes.

### Componentes Livewire

La interfaz es reactiva (SPA feel) gracias a Livewire.

-   `ProductManager`: Maneja la lógica de estado de los productos.
-   `AuditLog`: Gestiona la visualización y filtrado de los registros históricos.
-   `UserManager`: Gestión reactiva de usuarios y roles.

### Seguridad

-   **Protección de Rutas:** Middleware de autenticación y verificación de roles.
-   **Sanitización:** Uso de Eloquent ORM para prevenir inyección SQL.
-   **Auditoría:** Implementación de `Auditable` Interface en modelos críticos.
