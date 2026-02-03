<?php
require_once 'conexion.php';

// Verificar si se ha recibido el ID del producto por la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Llamar a la API con el método DELETE
    $respuesta = callAPI('DELETE', 'productos/' . $id);
    
    // Opcional: Aquí podrías verificar $respuesta['success'] 
    // para guardar un mensaje de error/éxito en sesión si quisieras.
    if (!$respuesta['success']) {
        // Error al eliminar (podrías loguearlo)
    }
}

// Redirigir siempre al listado de productos
header('Location: productos.php');
exit;
?>