<?php
class pedidosDB {
    private $db;
    private $table = 'pedidos';

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    //extraer los pedidos
    public function getAll() {
        $sql = "SELECT p.id, p.id_usuario, p.fecha, p.numero_factura, "
            . "COALESCE(SUM(lp.cantidad * lp.precio_unitario), p.total, 0) AS total "
            . "FROM {$this->table} p "
            . "LEFT JOIN linea_pedidos lp ON lp.id_pedido = p.id "
            . "GROUP BY p.id, p.id_usuario, p.fecha, p.numero_factura, p.total";

        $resultado = $this->db->query($sql);

        if($resultado && $resultado->num_rows > 0) {
            $pedidos = [];

            while($row = $resultado->fetch_assoc()) {
                $pedidos[] = $row;
            }

            return $pedidos;
        }
        return [];
    }

    //extraer pedido por id
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

    //crear un nuevo pedido
    public function crearPedido($input) {
        // Se añade el campo `numero_factura` para evitar el error de duplicado.
        $sql = "INSERT INTO {$this->table} (id_usuario, fecha, total, numero_factura) VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $fecha = date('Y-m-d H:i:s');
            // Generamos un número de pedido/factura único.
            $numero_factura = uniqid('PED-', true);

            $stmt->bind_param("isds", 
                $input['id_usuario'],
                $fecha,
                $input['total'],
                $numero_factura
            );

            if($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                return $id;
            }
            $stmt->close();
        }
        return false;
    }

    //actualizar un pedido
    public function actualizarPedido($id, $input) {
        $sql = "UPDATE {$this->table} SET total = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("di", 
                $input['total'],
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

    //eliminar un pedido
    public function eliminarPedido($id) {
        // Nota: Si la base de datos tiene una restricción de clave foránea (foreign key)
        // en la tabla `linea_pedidos` que apunta a `pedidos`, puede que necesites
        // borrar las líneas de pedido asociadas primero, o configurar la clave foránea
        // con `ON DELETE CASCADE`.
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        if($stmt){
            $stmt->bind_param("i", $id);

            if($stmt->execute() && $stmt->affected_rows > 0) {
                $stmt->close();
                return true;
            }
            $stmt->close();
        }
        return false;
    }
}