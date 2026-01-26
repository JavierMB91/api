<?php

class UsuarioController {
    private $usuariosDB;
    private $requestMethod;
    private $usuarioId;

    public function __construct($db, $requestMethod, $usuarioId = null)
    {
        $this->usuariosDB = new usuariosDB($db);
        $this->requestMethod = $requestMethod;
        $this->usuarioId = $usuarioId;
    }

    public function processRequest() {
        switch($this->requestMethod) {
            case "GET":
                if($this->usuarioId) {
                    $respuesta = $this->getUsuario($this->usuarioId);
                } else {
                    $respuesta = $this->getAllUsuarios();
                }
                break;
            case "POST":
                $respuesta = $this->createUsuario();
                break;
            case "PUT":
                if($this->usuarioId) {
                    $respuesta = $this->updateUsuario($this->usuarioId);
                } else {
                    $respuesta = $this->respuestaNoEncontrada();
                }
                break;
            case "DELETE":
                if($this->usuarioId) {
                    $respuesta = $this->deleteUsuario($this->usuarioId);
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

        return $respuesta;
    }

    private function getUsuario($id) {
        $usuario = $this->usuariosDB->getbyId($id);
        if(!$usuario) {
            return $this->respuestaNoEncontrada();
        }
        // Por seguridad, nunca devolver el hash de la contraseña
        unset($usuario['password']);

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode(['success' => true, 'data' => $usuario]);
        return $respuesta;
    }

    private function getAllUsuarios() {
        $usuarios = $this->usuariosDB->getAll();
        // Por seguridad, nunca devolver los hashes de las contraseñas
        foreach ($usuarios as &$usuario) {
            unset($usuario['password']);
        }

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'data' => $usuarios,
            'count' => count($usuarios)
        ]);
        return $respuesta;
    }

    private function createUsuario() {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['nombre']) || !isset($input['mail']) || !isset($input['password'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: nombre, mail y password son obligatorios');
        }

        if ($this->usuariosDB->createUsuario($input)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 201 Created';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo crear el usuario']);
        }
        return $respuesta;
    }

    private function updateUsuario($id) {
        $usuario = $this->usuariosDB->getbyId($id);
        if (!$usuario) {
            return $this->respuestaNoEncontrada();
        }

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        if (!isset($input['nombre']) || !isset($input['mail'])) {
            return $this->respuestaEntidadNoProcesable('Datos inválidos: nombre y mail son obligatorios');
        }

        if ($this->usuariosDB->updateUsuario($id, $input)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } else {
            $respuesta['status_code_header'] = 'HTTP/1.1 500 Internal Server Error';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'No se pudo actualizar el usuario']);
        }
        return $respuesta;
    }

    private function deleteUsuario($id) {
        if($this->usuariosDB->deleteUsuario($id)) {
            $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
            $respuesta['body'] = json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } else {
            return $this->respuestaNoEncontrada("No se pudo eliminar el usuario o el usuario no existe.");
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