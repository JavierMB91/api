# Ejemplos de uso de la API

Este documento contiene ejemplos practicos para consumir la API con curl.

## Login (obtener token)

```bash
curl -X POST http://localhost/api/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"mail":"usuario@correo.com","password":"123456"}'
```

Respuesta (ejemplo):

```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 86400
}
```

## Crear producto (requiere token)

```bash
curl -X POST http://localhost/api/api/productos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"codigo":"SKU-1","nombre":"Producto X","precio":19.99}'
```

## Listar productos (publico)

```bash
curl -X GET http://localhost/api/api/productos
```

## Listar usuarios (requiere token)

```bash
curl -X GET http://localhost/api/api/usuarios \
  -H "Authorization: Bearer <token>"
```

## Crear pedido (requiere token)

```bash
curl -X POST http://localhost/api/api/pedidos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"id_usuario":1,"total":49.99}'
```

## Crear linea de pedido (requiere token)

```bash
curl -X POST http://localhost/api/api/linea_pedidos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"id_pedido":1,"id_producto":2,"cantidad":1,"precio_unitario":49.99}'
```

## Actualizar usuario (requiere token)

```bash
curl -X PUT http://localhost/api/api/usuarios/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"nombre":"Nuevo Nombre","mail":"nuevo@correo.com"}'
```

## Eliminar producto (requiere token)

```bash
curl -X DELETE http://localhost/api/api/productos/1 \
  -H "Authorization: Bearer <token>"
```
