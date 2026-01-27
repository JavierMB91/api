<?php

class Validator {

    /**
     * Sanitiza datos de entrada recursivamente.
     * Elimina espacios en blanco innecesarios y convierte caracteres especiales en entidades HTML
     * para prevenir inyección de código (XSS).
     * 
     * @param mixed $data String o Array a limpiar
     * @return mixed Datos limpios
     */
    public static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }
        // trim: elimina espacios inicio/final
        // strip_tags: elimina etiquetas HTML y PHP
        // htmlspecialchars: convierte caracteres especiales
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida los datos de entrada para un Usuario según las reglas de OpenAPI.
     */
    public static function validateUsuario($data) {
        $errors = [];

        // Validar Nombre: Solo letras y espacios, 2-100 caracteres.
        if (empty($data['nombre']) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,100}$/u', $data['nombre'])) {
            $errors['nombre'] = "El nombre solo puede contener letras y espacios (2-100 caracteres).";
        }

        // Validar Email (format: email, maxLength: 255)
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL) || strlen($data['email']) > 255) {
            $errors['email'] = "El email no es válido o es demasiado largo.";
        }

        return $errors;
    }

    /**
     * Valida los datos de entrada para un Producto.
     */
    public static function validateProducto($data) {
        $errors = [];

        // Validar Nombre: Alfanumérico, espacios, puntos y guiones.
        if (empty($data['nombre']) || !preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-\.]{3,150}$/u', $data['nombre'])) {
            $errors['nombre'] = "El nombre del producto contiene caracteres inválidos.";
        }

        if (!isset($data['precio']) || !filter_var($data['precio'], FILTER_VALIDATE_FLOAT) || $data['precio'] < 0.01) {
            $errors['precio'] = "El precio debe ser un número mayor a 0.";
        }

        return $errors;
    }

    /**
     * Valida los datos de entrada para un Pedido.
     */
    public static function validatePedido($data) {
        $errors = [];

        if (empty($data['usuario_id']) || !filter_var($data['usuario_id'], FILTER_VALIDATE_INT) || $data['usuario_id'] < 1) {
            $errors['usuario_id'] = "El ID de usuario debe ser un entero válido.";
        }

        if (!isset($data['total']) || !filter_var($data['total'], FILTER_VALIDATE_FLOAT) || $data['total'] < 0.01) {
            $errors['total'] = "El total del pedido debe ser mayor a 0.";
        }

        // Validar fecha si existe (opcional)
        if (!empty($data['fecha']) && !strtotime($data['fecha'])) {
             $errors['fecha'] = "La fecha proporcionada no es válida.";
        }

        return $errors;
    }
}