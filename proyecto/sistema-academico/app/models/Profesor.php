<?php
require_once __DIR__ . "/Model.php";

class Profesor extends Model {

    public function listar() {
        $stmt = $this->conexion->query("SELECT * FROM profesores ORDER BY id_profesor DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM profesores WHERE id_profesor = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function columnaDniExiste() {
        try {
            $stmt = $this->conexion->query("SHOW COLUMNS FROM profesores LIKE 'dni'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crear($nombre, $apellido, $dni, $especialidad) {
        if ($this->columnaDniExiste()) {
            $sql  = "INSERT INTO profesores (nombre, apellido, dni, especialidad)
                     VALUES (:nombre, :apellido, :dni, :especialidad)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre",       $nombre);
            $stmt->bindParam(":apellido",     $apellido);
            $stmt->bindParam(":dni",          $dni);
            $stmt->bindParam(":especialidad", $especialidad);
        } else {
            $sql  = "INSERT INTO profesores (nombre, apellido, especialidad)
                     VALUES (:nombre, :apellido, :especialidad)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre",       $nombre);
            $stmt->bindParam(":apellido",     $apellido);
            $stmt->bindParam(":especialidad", $especialidad);
        }
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $apellido, $dni, $especialidad) {
        if ($this->columnaDniExiste()) {
            $sql  = "UPDATE profesores
                     SET nombre = :nombre, apellido = :apellido,
                         dni = :dni, especialidad = :especialidad
                     WHERE id_profesor = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre",       $nombre);
            $stmt->bindParam(":apellido",     $apellido);
            $stmt->bindParam(":dni",          $dni);
            $stmt->bindParam(":especialidad", $especialidad);
            $stmt->bindParam(":id",           $id);
        } else {
            $sql  = "UPDATE profesores
                     SET nombre = :nombre, apellido = :apellido,
                         especialidad = :especialidad
                     WHERE id_profesor = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre",       $nombre);
            $stmt->bindParam(":apellido",     $apellido);
            $stmt->bindParam(":especialidad", $especialidad);
            $stmt->bindParam(":id",           $id);
        }
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql  = "DELETE FROM profesores WHERE id_profesor = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
