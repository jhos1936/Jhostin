<?php
require_once __DIR__ . '/../config/conexion.php';

class Cliente {
    private $conn;
    private $tabla = "clientes";
    
    public function __construct() {
        $this->conn = Conexion::getInstance()->getConn();
    }
    
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->tabla . " ORDER BY id DESC";
        $result = $this->conn->query($query);
        $clientes = [];
        while($row = $result->fetch_assoc()) {
            $clientes[] = $row;
        }
        return $clientes;
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $datos['nombre'], $datos['email'], $datos['telefono'], $datos['direccion']);
        return $stmt->execute();
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE " . $this->tabla . " SET nombre = ?, email = ?, telefono = ?, direccion = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $datos['nombre'], $datos['email'], $datos['telefono'], $datos['direccion'], $id);
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        $this->conn->begin_transaction();
        try {
            // Desactivar verificacion de claves foraneas
            $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

            // Eliminar proyectos asociados al cliente
            $queryProyectos = "DELETE FROM proyectos WHERE cliente_id = ?";
            $stmt1 = $this->conn->prepare($queryProyectos);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            $stmt1->close();

            // Eliminar el cliente
            $queryCliente = "DELETE FROM " . $this->tabla . " WHERE id = ?";
            $stmt2 = $this->conn->prepare($queryCliente);
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $stmt2->close();

            // Reordenar IDs de clientes
            $this->conn->query("SET @c = 0");
            $this->conn->query("UPDATE " . $this->tabla . " SET id = (@c := @c + 1) ORDER BY id ASC");
            $this->conn->query("ALTER TABLE " . $this->tabla . " AUTO_INCREMENT = 1");

            // Reordenar IDs de proyectos tambien
            $this->conn->query("SET @p = 0");
            $this->conn->query("UPDATE proyectos SET id = (@p := @p + 1) ORDER BY id ASC");
            $this->conn->query("ALTER TABLE proyectos AUTO_INCREMENT = 1");

            // Reactivar verificacion de claves foraneas
            $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->query("SET FOREIGN_KEY_CHECKS = 1");
            $this->conn->rollback();
            return false;
        }
    }
}
?>
