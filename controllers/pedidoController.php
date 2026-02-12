<?php

require_once '../models/pedidosDB.php';
require_once '../models/linea_pedidosDB.php';

class PedidoController {
    private $pedidosDB;
    private $requestMethod;
    private $pedidoId;
    private $db_connection; // Para poder instanciar otros modelos

    public function __construct($db, $requestMethod, $pedidoId = null)
    {
        $this->db_connection = $db;
        $this->pedidosDB = new pedidosDB($db);
        $this->requestMethod = $requestMethod;
        $this->pedidoId = $pedidoId;
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case "GET":
                if($this->pedidoId) {
                    $respuesta = $this->getPedido($this->pedidoId);
                } else {
                    $respuesta = $this->getAllPedidos();
                }
                break;
            case "POST":
                $respuesta = $this->createPedido();
                break;
            case "PUT":
                if($this->pedidoId) {
                    $respuesta = $this->updatePedido($this->pedidoId);
                } else {
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            case "DELETE":
                if($this->pedidoId) {
                    $respuesta = $this->deletePedido($this->pedidoId);
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

    private function getAllPedidos() {
        $pedidos = $this->pedidosDB->getAll();
        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $pedidos,
            'count' => count($pedidos)
        ]);
        return $respuesta;
    }

    private function getPedido($id) {
        $pedido = $this->pedidosDB->getbyId($id);
        if(!$pedido) {
            return $this->respuestaNoEncontrada();
        }

        // ¡Aquí está la clave! Obtenemos las líneas de pedido asociadas
        $lineaPedidosDB = new linea_pedidosDB($this->db_connection);
        $lineas = $lineaPedidosDB->getAllFromByPedidoId($id);

        // Añadimos las líneas al objeto del pedido
        $pedido['lineas'] = $lineas;

        $totalLineas = 0.0;
        foreach ($lineas as $linea) {
            $cantidad = isset($linea['cantidad']) ? (float) $linea['cantidad'] : 0.0;
            $precioUnitario = isset($linea['precio_unitario']) ? (float) $linea['precio_unitario'] : 0.0;
            $totalLineas += $cantidad * $precioUnitario;
        }

        if (!isset($pedido['total']) || (float) $pedido['total'] <= 0) {
            $pedido['total'] = $totalLineas;
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode(['success' => true, 'data' => $pedido]);
        return $respuesta;
    }

    private function createPedido() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['id_usuario']) || !isset($input['total'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: id_usuario y total son obligatorios');
        }

        $pedidoId = $this->pedidosDB->crearPedido($input);

        if ($pedidoId) {
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode([
                'success' => true, 
                'message' => 'Pedido creado correctamente',
                'id_pedido' => $pedidoId // Devolvemos el ID para que se puedan añadir líneas
            ]);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo crear el pedido']);
        }
        return $respuesta;
    }

    private function updatePedido($id) {
        if (!$this->pedidosDB->getbyId($id)) {
            return $this->respuestaNoEncontrada();
        }

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['total'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: total es obligatorio');
        }

        if ($this->pedidosDB->actualizarPedido($id, $input)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Pedido actualizado correctamente']);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo actualizar el pedido']);
        }
        return $respuesta;
    }

    private function deletePedido($id) {
        if($this->pedidosDB->eliminarPedido($id)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Pedido y sus líneas asociadas eliminados correctamente']);
        } else {
            return $this->respuestaNoEncontrada("No se pudo eliminar el pedido o no existe.");
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