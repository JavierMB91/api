# JWT en la API

Este documento explica la implementacion sencilla de JWT (Bearer) usada para proteger la API.

## Objetivo

- Autenticar usuarios con mail y password.
- Emitir un token firmado que el cliente enviara en cada request.
- Bloquear endpoints protegidos si falta el token o si es invalido.

## Componentes clave

- [api/control.php](../api/control.php): router y middleware JWT.
- [controllers/authController.php](../controllers/authController.php): login y emision del token.
- [src/JWT.php](../src/JWT.php): helper JWT HS256.
- [config/config.php](../config/config.php): configuracion del secreto y expiracion.
- [models/usuariosDB.php](../models/usuariosDB.php): validacion de credenciales.

## Configuracion

En [config/config.php](../config/config.php):

```php
define('JWT_SECRET', 'cambia_esto_por_un_secreto_largo');
define('JWT_ISSUER', 'api-local');
define('JWT_TTL', 3600); // 1 hora
```

- `JWT_SECRET`: clave privada para firmar y verificar el token.
- `JWT_ISSUER`: identificador del emisor.
- `JWT_TTL`: tiempo de vida del token en segundos.

## Flujo de autenticacion

1) El cliente llama a `POST /auth/login` con `mail` y `password`.
2) Si son validos, el servidor emite un JWT con `iat`, `exp`, `sub`, `mail`, `nombre`.
3) El cliente incluye el token en `Authorization: Bearer <token>`.
4) El middleware valida firma y expiracion antes de ejecutar el controlador.

## Rutas publicas y protegidas

- Publicas:
  - `POST /auth/login`
  - `GET /productos`

- Protegidas:
  - Todas las operaciones sobre `usuarios`, `pedidos` y `linea_pedidos`.
  - `POST`, `PUT`, `DELETE` en `productos`.

## Validacion del token

El middleware en [api/control.php](../api/control.php):

- Extrae el header `Authorization` y busca el prefijo `Bearer`.
- Verifica la firma HMAC SHA-256.
- Verifica el claim `exp` para expiracion.
- Si falla, responde `401 Unauthorized`.

## Payload del token

El payload emitido contiene:

- `sub`: id del usuario.
- `mail`: correo del usuario.
- `nombre`: nombre del usuario.
- `iat`: fecha de emision (epoch).
- `exp`: fecha de expiracion (epoch).
- `iss`: emisor configurado.

## Seguridad

- Las contrasenas se almacenan con `password_hash` (bcrypt).
- La validacion usa `password_verify`.
- El token se firma con HMAC SHA-256 (HS256).

## Errores comunes

- **401 Token requerido**: falta el header `Authorization`.
- **401 Token invalido**: firma incorrecta o token expirado.
- **422**: faltan `mail` o `password` en el login.
