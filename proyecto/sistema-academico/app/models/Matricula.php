<?php
require_once __DIR__ . "/Model.php";

class Matricula extends Model {

    public function listar() {
        $sql = "SELECT m.id_matricula, a.nombre, a.apellido, g.nombre_grado, g.seccion, m.fecha_matricula
                FROM matriculas m
                LEFT JOIN alumnos a ON m.id_estudiante = a.id
                LEFT JOIN grados g ON m.id_grado = g.id_grado
                ORDER BY m.id_matricula DESC";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT m.*, a.nombre, a.apellido, g.nombre_grado, g.seccion
                FROM matriculas m
                LEFT JOIN alumnos a ON m.id_estudiante = a.id
                LEFT JOIN grados g ON m.id_grado = g.id_grado
                WHERE m.id_matricula = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorEstudiante($id_estudiante) {
        $sql = "SELECT m.*, g.nombre_grado, g.seccion
                FROM matriculas m
                LEFT JOIN grados g ON m.id_grado = g.id_grado
                WHERE m.id_estudiante = :id_estudiante
                ORDER BY m.fecha_matricula DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($id_estudiante, $id_grado) {
        $sql = "INSERT INTO matriculas (id_estudiante, id_grado, fecha_matricula)
                VALUES (:id_estudiante, :id_grado, NOW())";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->bindParam(":id_grado", $id_grado);

        return $stmt->execute();
    }

    public function actualizar($id, $id_estudiante, $id_grado) {
        $sql = "UPDATE matriculas SET id_estudiante = :id_estudiante, id_grado = :id_grado
                WHERE id_matricula = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->bindParam(":id_grado", $id_grado);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM matriculas WHERE id_matricula = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
