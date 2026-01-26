<?php

header("Content-Type: text/plain; charset=UTF-8");

// Incluir los archivos necesarios
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/usuariosDB.php';

// Crear instancia de la base de datos
$database = new Database();

// Crear instancia del modelo de usuarios
$usuariosDB = new usuariosDB($database);

// Array con los datos de los usuarios a insertar
$usuarios = [
    [
        'nombre' => 'Juan Perez',
        'mail' => 'juan.perez@example.com',
        'password' => 'password123' // La función createUsuario se encargará de hashear esto
    ],
    [
        'nombre' => 'Maria Garcia',
        'mail' => 'maria.garcia@example.com',
        'password' => 'securepass456'
    ]
];

echo "Iniciando inserción de usuarios...\n";
$insertados = 0;

// Iterar sobre el array de usuarios e insertarlos
foreach ($usuarios as $usuario) {
    if ($usuariosDB->createUsuario($usuario)) {
        $insertados++;
        echo "✓ Usuario insertado correctamente: {$usuario['nombre']} ({$usuario['mail']})\n";
    } else {
        echo "✗ Error al insertar el usuario: {$usuario['nombre']}\n";
    }
}

echo "\n>> Proceso finalizado. Total insertados: $insertados\n";

$database->close();
