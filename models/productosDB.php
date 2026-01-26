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

    public function createProducto($input) {
        return $this->insert(
            $input['codigo'],
            $input['nombre'],
            $input['precio'],
            $input['descripcion'],
            $input['imagen']
        );
    }

    public function updateProducto($id, $input) {
        $sql = "UPDATE {$this->table} SET codigo = ?, nombre = ?, precio = ?, descripcion = ?, imagen = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("ssdssi", 
                $input['codigo'],
                $input['nombre'],
                $input['precio'],
                $input['descripcion'],
                $input['imagen'],
                $id
            );

            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if($stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            }

            $stmt->close();
        }
        return false;
    }

    public function insert($codigo, $nombre, $precio, $descripcion, $imagen, $id = null) {
        if($id) {
            $sql = "INSERT INTO {$this->table} (id, codigo, nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if($stmt){
                $stmt->bind_param("issdss", $id, $codigo, $nombre, $precio, $descripcion, $imagen);
            }
        } else {
            $sql = "INSERT INTO {$this->table} (codigo, nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if($stmt){
                $stmt->bind_param("ssdss", $codigo, $nombre, $precio, $descripcion, $imagen);
            }
        }

        if($stmt){
            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }
}