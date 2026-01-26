<?php

class usuariosDB {
    private $db;
    private $table = 'usuarios';

    public function __construct($database)
    {
        $this->db = $database->getConnection();
    }

    //extraser todos los usuarios
    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";

        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0) {
            $usuarios = [];

            while($row = $resultado->fetch_assoc()) {
                $usuarios[] = $row;
            }

            return $usuarios;
        } else {
            return [];
        }
    }

    //validacion de usuario para login
    

    //extraer usuario por id
    public function getbyId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if($resultado && $resultado->num_rows > 0) {
                $stmt->close();
                return $resultado->fetch_assoc();
            }
            $stmt->close();
        }
        return null;
    }

    //crear un nuevo usuario
    public function createUsuario($input) {
        // Es crucial hashear la contraseña antes de guardarla por seguridad.
        $password_hash = password_hash($input['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO {$this->table} (nombre, mail, password) VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            // Asumimos que los campos son: nombre (string), mail (string), password (string)
            $stmt->bind_param("sss", 
                $input['nombre'],
                $input['mail'],
                $password_hash
            );

            if($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }

    //actualizar un usuario
    public function updateUsuario($id, $input) {
        // Comprobar si se ha proporcionado una nueva contraseña no vacía
        if (isset($input['password']) && !empty($input['password'])) {
            // Si hay contraseña, la consulta la incluye y la hashea
            $sql = "UPDATE {$this->table} SET nombre = ?, mail = ?, password = ? WHERE id = ?";
            $password_hash = password_hash($input['password'], PASSWORD_BCRYPT);
            
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sssi", 
                    $input['nombre'],
                    $input['mail'],
                    $password_hash,
                    $id
                );
            }
        } else {
            // Si no hay contraseña, la consulta no la actualiza
            $sql = "UPDATE {$this->table} SET nombre = ?, mail = ? WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssi", 
                    $input['nombre'],
                    $input['mail'],
                    $id
                );
            }
        }

        if (isset($stmt) && $stmt && $stmt->execute()) {
            $stmt->close();
            return true;
        }
        // Si $stmt existe, ciérralo antes de retornar false
        if (isset($stmt) && $stmt) $stmt->close();
        return false;
    }

    //eliminar un usuario
    public function deleteUsuario($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("i", $id);

            // Se ejecuta y se comprueba si alguna fila fue afectada
            if($stmt->execute() && $stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }
}