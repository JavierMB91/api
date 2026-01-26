<?php

require_once 'config/database.php';
require_once 'models/productosDB.php';

// Crear instancia de base de datos
$database = new Database();
$db = $database->getConnection();

// Array de productos a insertar
$productos = [
    [
        'codigo' => 'LIB001',
        'nombre' => 'Alas de Sangre',
        'precio' => 22.90,
        'descripcion' => 'El fenómeno de fantasía de Rebecca Yarros.',
        'imagen' => 'img/alas_de_sangre.jpg'
    ],
    [
        'codigo' => 'LIB002',
        'nombre' => 'Hábitos Atómicos',
        'precio' => 19.95,
        'descripcion' => 'Cambios pequeños, resultados extraordinarios de James Clear.',
        'imagen' => 'img/habitos_atomicos.jpg'
    ],
    [
        'codigo' => 'COM001',
        'nombre' => 'One Piece Vol. 105',
        'precio' => 8.50,
        'descripcion' => 'El sueño de Luffy continúa en Wano.',
        'imagen' => 'img/one_piece_105.jpg'
    ],
    [
        'codigo' => 'LIB003',
        'nombre' => 'El problema de los 3 cuerpos',
        'precio' => 21.90,
        'descripcion' => 'La aclamada novela de ciencia ficción de Cixin Liu.',
        'imagen' => 'img/tres_cuerpos.jpg'
    ],
    [
        'codigo' => 'LIB004',
        'nombre' => 'Blackwater I: La riada',
        'precio' => 9.90,
        'descripcion' => 'La saga gótica de Michael McDowell que arrasa.',
        'imagen' => 'img/blackwater_1.jpg'
    ],
    [
        'codigo' => 'LIB005',
        'nombre' => 'La armadura de la luz',
        'precio' => 24.90,
        'descripcion' => 'El regreso a Kingsbridge de Ken Follett.',
        'imagen' => 'img/armadura_luz.jpg'
    ],
    [
        'codigo' => 'COM002',
        'nombre' => 'Heartstopper 5',
        'precio' => 15.95,
        'descripcion' => 'La novela gráfica romántica de Alice Oseman.',
        'imagen' => 'img/heartstopper_5.jpg'
    ],
    [
        'codigo' => 'ENC001',
        'nombre' => 'Enciclopedia Marvel',
        'precio' => 45.00,
        'descripcion' => 'La guía definitiva del Universo Marvel actualizada.',
        'imagen' => 'img/enciclopedia_marvel.jpg'
    ],
    [
        'codigo' => 'LIB006',
        'nombre' => 'El infinito en un junco',
        'precio' => 21.90,
        'descripcion' => 'La invención de los libros por Irene Vallejo.',
        'imagen' => 'img/infinito_junco.jpg'
    ],
    [
        'codigo' => 'COM003',
        'nombre' => 'Jujutsu Kaisen 0',
        'precio' => 8.00,
        'descripcion' => 'La precuela del exitoso manga de Gege Akutami.',
        'imagen' => 'img/jujutsu_kaisen_0.jpg'
    ]
];

// Insertar productos
$insertados = 0;
$errores = 0;

foreach($productos as $producto) {
    $sql = "INSERT INTO productos (codigo, nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    
    if($stmt) {
        $stmt->bind_param(
            "ssdss",
            $producto['codigo'],
            $producto['nombre'],
            $producto['precio'],
            $producto['descripcion'],
            $producto['imagen']
        );
        
        if($stmt->execute()) {
            $insertados++;
            echo "✓ Producto insertado: {$producto['nombre']}\n";
        } else {
            $errores++;
            echo "✗ Error al insertar {$producto['nombre']}: " . $stmt->error . "\n";
        }
        
        $stmt->close();
    } else {
        $errores++;
        echo "✗ Error en la preparación de la consulta: " . $db->error . "\n";
    }
}

// Resumen
echo "\n=== RESUMEN ===\n";
echo "Productos insertados: $insertados\n";
echo "Errores: $errores\n";

$database->close();

?>
