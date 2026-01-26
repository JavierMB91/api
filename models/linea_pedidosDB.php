<?php

class linea_pedidosDB {
    private $db;
    private $table = 'linea_pedidos';

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    //extraer todas las lineas de producto
    public function getAllFromByPedidoId($pedidoId) {
        // Hacemos un JOIN con la tabla productos para traer el nombre, código e imagen
        $sql = "SELECT lp.*, p.nombre as nombre_producto, p.codigo, p.imagen 
                FROM {$this->table} lp
                INNER JOIN productos p ON lp.id_producto = p.id
                WHERE lp.id_pedido = ?";

        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $pedidoId);
            $stmt->execute();
            $resultado = $stmt->get_result();

            $lineas = [];
            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $lineas[] = $row;
                }
            }
            $stmt->close();
            return $lineas;
        }
        return [];
    }

    //crear una nueva linea de producto
    public function crearLineaPedido($input) {
        $sql = "INSERT INTO {$this->table} (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            // Asumimos los tipos: id_pedido (int), id_producto (int), cantidad (int), precio (double)
            $stmt->bind_param("iiid",
                $input['id_pedido'],
                $input['id_producto'],
                $input['cantidad'],
                $input['precio_unitario']
            );

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }

    //actualizar una linea de producto
    public function actualizarLineaPedido($lineaId, $input) {
        $sql = "UPDATE {$this->table} SET cantidad = ?, precio_unitario = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            // Asumimos los tipos: cantidad (int), precio (double), id (int)
            $stmt->bind_param("idi",
                $input['cantidad'],
                $input['precio_unitario'],
                $lineaId
            );

            if ($stmt->execute()) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }

    //eliminar una linea de producto
    public function eliminarLineaPedido($lineaId) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $lineaId);

            // Se ejecuta y se comprueba si alguna fila fue afectada
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }
}