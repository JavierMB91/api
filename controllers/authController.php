<?php

require_once '../models/usuariosDB.php';
require_once '../src/JWT.php';

class AuthController
{
    private $usuariosDB;
    private $requestMethod;
    private $action;

    public function __construct($db, $requestMethod, $action = null)
    {
        $this->usuariosDB = new usuariosDB($db);
        $this->requestMethod = $requestMethod;
        $this->action = $action;
    }

    public function processRequest()
    {
        if ($this->requestMethod !== 'POST' || $this->action !== 'login') {
            $respuesta = $this->respuestaNoEncontrada();
        } else {
            $respuesta = $this->login();
        }

        header($respuesta['status_code_header']);
        if ($respuesta['body']) {
            echo $respuesta['body'];
        }

        return $respuesta;
    }

    private function login()
    {
        $input = (array) json_decode(file_get_contents('php://input'), true);

        if (!isset($input['mail']) || !isset($input['password'])) {
            return $this->respuestaEntidadNoProcesable('Datos invalidos: mail y password son obligatorios');
        }

        $usuario = $this->usuariosDB->validateCredentials($input['mail'], $input['password']);
        if (!$usuario) {
            $respuesta['status_code_header'] = 'HTTP/1.1 401 Unauthorized';
            $respuesta['body'] = json_encode(['success' => false, 'error' => 'Credenciales invalidas']);
            return $respuesta;
        }

        $now = time();
        $payload = [
            'sub' => (int) $usuario['id'],
            'mail' => $usuario['mail'],
            'nombre' => $usuario['nombre'],
            'rol' => $usuario['rol'] ?? 'usuario',
            'iat' => $now,
            'exp' => $now + JWT_TTL,
            'iss' => JWT_ISSUER
        ];

        $token = JWT::encode($payload, JWT_SECRET);

        $respuesta['status_code_header'] = 'HTTP/1.1 200 OK';
        $respuesta['body'] = json_encode([
            'success' => true,
            'token' => $token,
            'expires_in' => JWT_TTL
        ]);
        return $respuesta;
    }

    private function respuestaEntidadNoProcesable($mensaje)
    {
        $respuesta['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $respuesta['body'] = json_encode(['success' => false, 'error' => $mensaje]);
        return $respuesta;
    }

    private function respuestaNoEncontrada($mensaje = 'Recurso no encontrado')
    {
        $respuesta['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $respuesta['body'] = json_encode(['success' => false, 'error' => $mensaje]);
        return $respuesta;
    }
}
