<?php

require_once 'config/database.php';
require_once 'models/productoDB.php';

// Crear instancia de base de datos
$database = new Database();
$db = new ProductoDB($database);

// Datos del producto que se borró
$resultado = $db->insert(
    'LIB001',
    'Alas de Sangre',
    22.90,
    'El fenómeno de fantasía de Rebecca Yarros.',
    'img/alas_de_sangre.jpg',
    1  // id específico
);

if($resultado) {
    echo "✓ Producto 'Alas de Sangre' insertado correctamente con id 1\n";
} else {
    echo "✗ Error al insertar el producto\n";
}

$database->close();
?>
