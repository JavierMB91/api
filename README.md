# API REST con JWT

API REST sencilla con autenticacion JWT (Bearer) para proteger operaciones de creacion, modificacion y borrado. Incluye documentacion OpenAPI 3.0 con Swagger UI y panel de administracion.

## Requisitos

- Servidor web (Apache, Nginx, etc.) o entorno local como XAMPP/WAMP/MAMP.
- PHP 7.4+ con extensiones mysqli y curl.
- MySQL/MariaDB con las tablas del proyecto.

## Inicio rapido

1. Importa `api.sql` en MySQL.
2. Configura credenciales en [config/config.php](config/config.php) y cambia `JWT_SECRET`.
3. Inicia Apache y MySQL desde XAMPP.
4. Panel admin: http://localhost/api/api/admin/login.php (usuario: `admin@correo.com` / `admin1234`)
5. Swagger UI: http://localhost/api/

## Documentacion

- [JWT (detalle tecnico)](docs/README_JWT.md)
- [Ejemplos de uso con curl](docs/README_EJEMPLOS.md)