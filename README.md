# Documentación API del Proyecto

Este repositorio contiene la definición y visualización de la API REST del proyecto, utilizando la especificación **OpenAPI 3.0** y **Swagger UI**.

La documentación es estática y no requiere dependencias de Node.js ni compilación, por lo que es ideal para desplegarse rápidamente en servidores como Apache (XAMPP).

## 📋 Requisitos

*   Un servidor web (Apache, Nginx, etc.) o un entorno local como **XAMPP**, WAMP o MAMP.
*   Navegador web moderno.

## 🚀 Instalación y Despliegue

1.  **Ubicación de archivos:**
    Asegúrate de que la carpeta del proyecto esté dentro del directorio público de tu servidor web.
    *   En XAMPP: `C:\xampp\htdocs\api\`

2.  **Estructura de archivos:**
    El directorio debe contener al menos estos dos archivos:
    *   `openapi.yaml`: Contiene la definición técnica de todos los endpoints, esquemas y seguridad.
    *   `index.html`: La interfaz gráfica que carga Swagger UI desde un CDN y lee el archivo YAML.

## 📖 Cómo ver la documentación

1.  Inicia el servicio **Apache** desde el panel de control de XAMPP.
2.  Abre tu navegador web.
3.  Navega a la siguiente URL:

    ```
    http://localhost/api/
    ```

    *Nota: Si el archivo no carga automáticamente, intenta con `http://localhost/api/index.html`.*

## 🔑 Características de la API

La documentación cubre los siguientes módulos del sistema:

### Autenticación (Auth)
*   **POST /auth/login**: Permite obtener un token JWT (Bearer Token) para acceder a rutas protegidas.

### Usuarios
Gestión completa de usuarios (CRUD).
*   **GET /usuarios**: Listar todos los usuarios (Requiere Token).
*   **POST /usuarios**: Crear un nuevo usuario (Requiere Token).
*   **GET /usuarios/{id}**: Ver detalle de un usuario.
*   **PUT /usuarios/{id}**: Actualizar información.
*   **DELETE /usuarios/{id}**: Eliminar usuario.

### Productos
Catálogo de productos.
*   **GET /productos**: Listado público de productos.
*   **POST /productos**: Crear producto (Requiere Token).
*   **GET, PUT, DELETE /productos/{id}**: Operaciones sobre un producto específico.

## 🛠️ Edición

Para modificar la documentación, edita únicamente el archivo `openapi.yaml`. Los cambios se reflejarán automáticamente al recargar la página en el navegador.