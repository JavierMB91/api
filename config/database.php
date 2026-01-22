<?php

require_once 'config.php';

class Database {
    private $host = DB_HOST;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $database = DB_NAME;

    private $conexion;


    public function __construct()
    {
        $this->connect();

    }

    private function connect() {
        $this->conexion = new mysqli($this->host, $this->username, $this->password, $this->database);

        if($this->conexion->connect_error) {
            die("Error de conexion: " . $this->conexion->connect_error);
        }

        $this->conexion->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conexion;
    }

    public function close() {
        $this->conexion->close();
    }
}