<?php
require_once __DIR__ . "/Model.php";

class Estudiante extends Model {

    public function listar() {
        $stmt = $this->conexion->query("SELECT * FROM alumnos ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM alumnos WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $apellido, $correo, $grado) {
        $sql  = "INSERT INTO alumnos (nombre, apellido, correo, grado)
                 VALUES (:nombre, :apellido, :correo, :grado)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre",   $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":correo",   $correo);
        $stmt->bindParam(":grado",    $grado);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $apellido, $correo, $grado) {
        $sql  = "UPDATE alumnos
                 SET nombre = :nombre, apellido = :apellido,
                     correo = :correo, grado = :grado
                 WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre",   $nombre);
        $stmt->bindParam(":apellido", $apellido);
        $stmt->bindParam(":correo",   $correo);
        $stmt->bindParam(":grado",    $grado);
        $stmt->bindParam(":id",       $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql  = "DELETE FROM alumnos WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
