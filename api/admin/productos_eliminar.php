<?php
require_once 'conexion.php';

$error = '';

// Verificar si se ha recibido el ID del producto por la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Llamar a la API con el método DELETE
    $respuesta = callAPI('DELETE', 'productos/' . $id);
    
    // Capturar error si lo hay
    if (!isset($respuesta['success']) || !$respuesta['success']) {
        $error = $respuesta['error'] ?? 'Error al eliminar el producto.';
        $_SESSION['error_mensaje'] = $error;
    } else {
        $_SESSION['exito_mensaje'] = 'Producto eliminado correctamente.';
    }
}

// Redirigir siempre al listado de productos
header('Location: productos_listar.php');
exit;
?>