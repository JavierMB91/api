<?php

class LineaPedidoController {
    private $lineaPedidosDB;
    private $requestMethod;
    private $lineaId;

    public function __construct($db, $requestMethod, $lineaId = null)
    {
        $this->lineaPedidosDB = new linea_pedidosDB($db);
        $this->requestMethod = $requestMethod;
        $this->lineaId = $lineaId;
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case "GET":
                // El modelo actual está diseñado para obtener líneas por pedido
                $respuesta = $this->getAllLineas();
                break;
            case "POST":
                $respuesta = $this->createLinea();
                break;
            case "PUT":
                if($this->lineaId) {
                    $respuesta = $this->updateLinea($this->lineaId);
                } else {
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            case "DELETE":
                if($this->lineaId) {
                    $respuesta = $this->deleteLinea($this->lineaId);
                } else {
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            default:
                $respuesta = $this->respuestaNoEncontrada();
        }
        
        header($respuesta['status_code_header']);
        if($respuesta['body']) {
            echo $respuesta['body'];
        }
    }

    private function getAllLineas() {
        // Verificamos si existe el parámetro id_pedido en la URL
        if (isset($_GET['id_pedido'])) {
            $pedidoId = $_GET['id_pedido'];
            $lineas = $this->lineaPedidosDB->getAllFromByPedidoId($pedidoId);
            
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode([
                'success' => true,
                'data' => $lineas,
                'count' => count($lineas)
            ]);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 400 Bad Request';
            $respuesta['body'] = json_encode([
                'success' => false,
                'error' => 'Se requiere el parámetro id_pedido para listar las líneas'
            ]);
        }
        return $respuesta;
    }

    private function createLinea() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['id_pedido']) || !isset($input['id_producto']) || !isset($input['cantidad']) || !isset($input['precio_unitario'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: id_pedido, id_producto, cantidad y precio_unitario son obligatorios');
        }

        if ($this->lineaPedidosDB->crearLineaPedido($input)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Línea de pedido creada correctamente']);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo crear la línea de pedido']);
        }
        return $respuesta;
    }

    private function updateLinea($id) {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['cantidad']) || !isset($input['precio_unitario'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: cantidad y precio_unitario son obligatorios');
        }

        if ($this->lineaPedidosDB->actualizarLineaPedido($id, $input)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Línea de pedido actualizada correctamente']);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo actualizar la línea de pedido']);
        }
        return $respuesta;
    }

    private function deleteLinea($id) {
        if($this->lineaPedidosDB->eliminarLineaPedido($id)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Línea de pedido eliminada correctamente']);
        } else {
            return $this->respuestaNoEncontrada("No se pudo eliminar la línea o no existe.");
        }
        return $respuesta;
    }

    private function respuestaEntidadNoProcesable($mensaje) {
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode(['success' => false, 'error' => $mensaje]);
        return $respuesta;
    }

    private function respuestaNoEncontrada($mensaje = 'Recurso no encontrado') {
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode(['success' => false, 'error' => $mensaje]);
        return $respuesta;
    }
}
