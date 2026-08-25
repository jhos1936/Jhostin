<?php
require_once __DIR__ . '/../config/conexion.php';

class Proyecto {
    private $conn;
    private $tabla = "proyectos";
    
    public function __construct() {
        $this->conn = Conexion::getInstance()->getConn();
    }
    
    public function obtenerTodos() {
        $query = "SELECT p.*, c.nombre as cliente_nombre 
                  FROM " . $this->tabla . " p 
                  LEFT JOIN clientes c ON p.cliente_id = c.id 
                  ORDER BY p.id DESC";
        $result = $this->conn->query($query);
        $proyectos = [];
        while($row = $result->fetch_assoc()) {
            $proyectos[] = $row;
        }
        return $proyectos;
    }
    
    public function obtenerPorCliente($cliente_id) {
        $query = "SELECT p.*, c.nombre as cliente_nombre 
                  FROM " . $this->tabla . " p 
                  LEFT JOIN clientes c ON p.cliente_id = c.id 
                  WHERE p.cliente_id = ?
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $proyectos = [];
        while($row = $result->fetch_assoc()) {
            $proyectos[] = $row;
        }
        $stmt->close();
        return $proyectos;
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
        $query = "INSERT INTO " . $this->tabla . " (nombre, descripcion, cliente_id, estado, presupuesto, fecha_inicio, fecha_fin) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $fechaFin = !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null;
        $stmt->bind_param("ssissds", 
            $datos['nombre'], 
            $datos['descripcion'], 
            $datos['cliente_id'], 
            $datos['estado'],
            $datos['presupuesto'],
            $datos['fecha_inicio'],
            $fechaFin
        );
        return $stmt->execute();
    }
    
    public function actualizar($id, $datos) {
        $query = "UPDATE " . $this->tabla . " 
                  SET nombre = ?, descripcion = ?, cliente_id = ?, estado = ?, presupuesto = ?, fecha_inicio = ?, fecha_fin = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $fechaFin = !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null;
        $stmt->bind_param("ssissdsi", 
            $datos['nombre'], 
            $datos['descripcion'], 
            $datos['cliente_id'], 
            $datos['estado'],
            $datos['presupuesto'],
            $datos['fecha_inicio'],
            $fechaFin,
            $id
        );
        return $stmt->execute();
    }
    
    public function eliminar($id) {
        $this->conn->begin_transaction();
        try {
            $this->conn->query("SET FOREIGN_KEY_CHECKS = 0");

            $query = "DELETE FROM " . $this->tabla . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Reordenar IDs
            $this->conn->query("SET @p = 0");
            $this->conn->query("UPDATE " . $this->tabla . " SET id = (@p := @p + 1) ORDER BY id ASC");
            $this->conn->query("ALTER TABLE " . $this->tabla . " AUTO_INCREMENT = 1");

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
