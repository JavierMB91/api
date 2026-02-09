<?php

// Definimos la URL base de la API.
// Según tu index.php ($segmentos[2]), la estructura esperada es: http://dominio/carpeta/api/recurso
define('API_URL', 'http://localhost/api/api');

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
    
    // Manejo de errores de conexión
    if (!$respuesta) {
        die("Error de conexión con la API: " . curl_error($curl));
    }
    
    // Devolver la respuesta decodificada (convertir JSON a Array PHP)
    return json_decode($respuesta, true);
}
?>