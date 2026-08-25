<?php
require_once __DIR__ . "/Model.php";

class Curso extends Model {

    public function listar() {
        $stmt = $this->conexion->query(
            "SELECT id_curso, nombre_curso FROM cursos ORDER BY id_curso DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM cursos WHERE id_curso = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre_curso) {
        $sql  = "INSERT INTO cursos (nombre_curso) VALUES (:nombre_curso)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre_curso", $nombre_curso);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre_curso) {
        $sql  = "UPDATE cursos SET nombre_curso = :nombre_curso WHERE id_curso = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre_curso", $nombre_curso);
        $stmt->bindParam(":id",           $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql  = "DELETE FROM cursos WHERE id_curso = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
