<?php
// Permitir peticiones desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");

// Métodos HTTP permitidos
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Cabeceras permitidas
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Tipo de contenido de la respuesta
header("Content-Type: application/json; charset=UTF-8");

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../controllers/productoController.php';
require_once '../controllers/usuarioController.php';
require_once '../controllers/lineaPedidoController.php';
require_once '../controllers/pedidoController.php';
require_once '../models/productosDB.php';
require_once '../models/usuariosDB.php';
require_once '../models/linea_pedidosDB.php';
require_once '../models/pedidosDB.php';

//averiguar la url de la peticion
$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//obtener el metodo utilizado en la peticion
$requestMethod = $_SERVER['REQUEST_METHOD'];

//Dividir en segmentos la url (ej: /api/api/productos/1 -> ['', 'api', 'api', 'productos', '1'])
$segmentos = explode('/', trim($requestUrl, '/'));

// El nombre del recurso (endpoint) debería ser el tercer segmento (ej: 'productos' o 'usuarios')
$endpoint = $segmentos[2] ?? null;
$resourceId = $segmentos[3] ?? null;

$database = new Database();
$controller = null;

switch ($endpoint) {
    case 'productos':
        $controller = new ProductoController($database, $requestMethod, $resourceId);
        break;
    case 'usuarios':
        $controller = new UsuarioController($database, $requestMethod, $resourceId);
        break;
    case 'linea_pedidos':
        $controller = new LineaPedidoController($database, $requestMethod, $resourceId);
        break;
    case 'pedidos':
        $controller = new PedidoController($database, $requestMethod, $resourceId);
        break;
    default:
        // Si el endpoint no es válido, enviar error 404
        header('HTTP/1.1 404 Not Found');
        echo json_encode([
            'success' => false,
            'error' => 'Endpoint no encontrado'
        ]);
        exit();
}

if ($controller) {
    $controller->processRequest();
}
$database->close();
