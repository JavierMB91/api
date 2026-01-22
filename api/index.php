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
require_once '../models/productoDB.php';

//averiguar la url de la peticion
$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

//obtener el metodo utilizado en la peticion
$requestMethod = $_SERVER['REQUEST_METHOD'];

//Dividir en segmentos la url
$segmentos = explode('/', trim($requestUrl, '/'));

if($segmentos[1] !== 'api' || !isset($segmentos[2]) || $segmentos[2]!== 'productos') {
    $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
    echo json_encode([
        'success' => false,
        'error' => 'Endpoint no encontrado'
    ]);
    exit();
}


$productoId = null;
if(isset($segmentos[3])) {
    $productoId = (int) $segmentos[3];
}

$database = new Database();
$productoController = new ProductoController($database, $requestMethod, $productoId);
$productoController->processRequest();
$database->close();


