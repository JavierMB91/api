<?php

class ProductoDB {
    //$db para conexion a la base de datos
    private $db;
    private $table = 'productos';

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    // Obtener todos los productos
    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";

        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0) {
            $productos = [];

            while($row = $resultado->fetch_assoc()) {
                $productos[] = $row;
            }

            return $productos;
        } else {
            return [];
        }
    }

    public function getbyId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("i", $id);
            $stmt->execute();


            $resultado = $stmt->get_result();

            if($resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
            }

            $stmt->close();
        }
        return null;
    }
}