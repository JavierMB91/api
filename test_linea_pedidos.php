<?php
// Configuración para ver errores y caracteres correctamente
ini_set('display_errors', 1);
header("Content-Type: text/html; charset=UTF-8");

require_once 'config/database.php';
require_once 'models/linea_pedidosDB.php';

// 1. Instanciar Base de Datos y Modelo
$database = new Database();
$db = $database->getConnection();
$lineaPedidos = new linea_pedidosDB($database);

echo "<h1>Prueba de Integración: Linea Pedidos</h1>";
echo "<p>Probando funcionalidad CRUD sin depender de la tabla 'pedidos'...</p>";

// --- TRUCO CLAVE ---
// Desactivamos chequeo de claves foráneas para poder usar un id_pedido ficticio
$db->query("SET FOREIGN_KEY_CHECKS=0");

$idPedidoTest = 99999; // ID inventado
$idProductoTest = 1;   // Asumimos que existe producto 1 (de tus inserts anteriores)

// --- 2. PRUEBA CREAR ---
echo "<h3>1. Crear Línea de Pedido</h3>";
$datosCrear = [
    'id_pedido' => $idPedidoTest,
    'id_producto' => $idProductoTest,
    'cantidad' => 2,
    'precio_unitario' => 15.50
];

if ($lineaPedidos->crearLineaPedido($datosCrear)) {
    echo "<p style='color:green'>✓ Línea creada correctamente (Pedido ID: $idPedidoTest).</p>";
} else {
    echo "<p style='color:red'>✗ Error al crear línea.</p>";
}

// --- 3. PRUEBA LEER (GET ALL BY PEDIDO) ---
echo "<h3>2. Leer Líneas del Pedido $idPedidoTest</h3>";
$lineas = $lineaPedidos->getAllFromByPedidoId($idPedidoTest);
$idLineaCreada = null;

if (!empty($lineas)) {
    echo "<p style='color:green'>✓ Se encontraron " . count($lineas) . " líneas.</p>";
    echo "<ul>";
    foreach ($lineas as $linea) {
        echo "<li>ID: <strong>" . $linea['id'] . "</strong> | " .
             "Prod: " . $linea['id_producto'] . " | " .
             "Cant: " . $linea['cantidad'] . " | " .
             "Precio: " . $linea['precio_unitario'] . "</li>";
        
        // Guardamos el ID para las siguientes pruebas
        $idLineaCreada = $linea['id'];
    }
    echo "</ul>";
} else {
    echo "<p style='color:red'>✗ No se encontraron líneas.</p>";
}

// --- 4. PRUEBA ACTUALIZAR ---
if ($idLineaCreada) {
    echo "<h3>3. Actualizar Línea ID $idLineaCreada</h3>";
    $datosActualizar = [
        'cantidad' => 5,
        'precio_unitario' => 12.00
    ];

    if ($lineaPedidos->actualizarLineaPedido($idLineaCreada, $datosActualizar)) {
        echo "<p style='color:green'>✓ Línea actualizada correctamente.</p>";
    } else {
        echo "<p style='color:red'>✗ Error al actualizar línea.</p>";
    }
}

// --- 5. PRUEBA ELIMINAR ---
if ($idLineaCreada) {
    echo "<h3>4. Eliminar Línea ID $idLineaCreada</h3>";
    if ($lineaPedidos->eliminarLineaPedido($idLineaCreada)) {
        echo "<p style='color:green'>✓ Línea eliminada correctamente.</p>";
    } else {
        echo "<p style='color:red'>✗ Error al eliminar línea.</p>";
    }
}

// --- LIMPIEZA ---
// Reactivar claves foráneas es buena práctica
$db->query("SET FOREIGN_KEY_CHECKS=1");
$database->close();
?>