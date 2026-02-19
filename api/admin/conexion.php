<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Definimos la URL base de la API.
// Según tu index.php ($segmentos[2]), la estructura esperada es: http://dominio/carpeta/api/recurso
define('API_URL', 'http://localhost/api/api');

function requireAdminAuth() {
    $current = basename($_SERVER['PHP_SELF']);
    $isLogin = $current === 'login.php';

    if ($isLogin) {
        return;
    }

    if (empty($_SESSION['jwt_token'])) {
        header('Location: login.php');
        exit;
    }
}

if (empty($skipAuth)) {
    requireAdminAuth();
}

/**
 * Función para consumir la API REST
 * 
 * @param string $metodo  Método HTTP: 'GET', 'POST', 'PUT', 'DELETE'
 * @param string $endpoint Recurso a solicitar (ej: 'productos', 'usuarios/1')
 * @param array  $datos   (Opcional) Array asociativo con los datos a enviar
 * @return array          Respuesta de la API decodificada en array
 */
function callAPI($metodo, $endpoint, $datos = []) {
    $url = API_URL . '/' . $endpoint;
    
    $curl = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    if (!empty($_SESSION['jwt_token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['jwt_token'];
    }

    // Configuración básica de cURL
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    // Configuración según el método HTTP
    switch (strtoupper($metodo)) {
        case 'POST':
            curl_setopt($curl, CURLOPT_POST, true);
            if (!empty($datos)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
            }
            break;
            
        case 'PUT':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if (!empty($datos)) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
            }
            break;
            
        case 'DELETE':
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            break;
            
        case 'GET':
        default:
            if (!empty($datos)) {
                $url = sprintf("%s?%s", $url, http_build_query($datos));
                curl_setopt($curl, CURLOPT_URL, $url);
            }
            break;
    }

    // Ejecutar la petición
    $respuesta = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    // Manejo de errores de conexión
    if (!$respuesta) {
        die("Error de conexión con la API: " . curl_error($curl));
    }
    
    // Devolver la respuesta decodificada (convertir JSON a Array PHP)
    $data = json_decode($respuesta, true);
    if (!is_array($data)) {
        $data = ['success' => false, 'error' => 'Respuesta no valida de la API.'];
    }
    $data['_http_code'] = $httpCode;

    if ($httpCode === 401 && basename($_SERVER['PHP_SELF']) !== 'login.php') {
        $_SESSION['jwt_token'] = '';
        header('Location: login.php?expired=1');
        exit;
    }
    return $data;
}
?>