<?php

class productoController {
    private $productoDB;
    private $requestMethod;
    private $productoId;


    public function __construct($db, $requestMethod, $productoId = null)
    {
        $this->productoDB = new ProductoDB($db);
        $this->requestMethod = $requestMethod;
        $this->productoId = $productoId;
    }

    public function processRequest() {
        $method = $this->requestMethod;
        //comprobar el metodo de la llamada
        switch($method) {
            case "GET":
                if($this->productoId) {
                    $respuesta = $this->getProducto($this->productoId);
                }else {
                    $respuesta = $this->getAllProductos();
                }
                break;
            default:
                $respuesta = $this->respuestaNoEncontrada();
        }
        //Se le envia al cliente la cabecera y el cuerpo de la respuesta
        header($respuesta['status_code_header']);
        if($respuesta['body']) {
            echo $respuesta['body'];
        }

        return $respuesta;
    }

    private function getProducto($id) {
        $producto = $this->productoDB->getbyId($id);

        if(!$producto) {
            return $this->respuestaNoEncontrada();
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $producto
        ]);
        return $respuesta;
    }

    private function getAllProductos() {
        $productos = $this->productoDB->getAll();

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $productos,
            'count' => count($productos)
        ]);
        return $respuesta;

    }

    private function respuestaNoEncontrada() {
        $productos = $this->productoDB->getAll();

        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode([
            'success' => false,
            'error' => 'Producto no encontrado'
        ]);
        return $respuesta;
    }
}