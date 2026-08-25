<?php
require_once __DIR__ . "/Model.php";

class Calificacion extends Model {

    private function calcularPromedio($nota) {
        return ($nota - floor($nota) >= 0.5) ? ceil($nota) : floor($nota);
    }

    public function listar() {
        $sql = "SELECT
                    c.*,
                    a.nombre AS nombre_alumno,
                    a.apellido AS apellido_alumno,
                    cur.nombre_curso,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_profesor
                FROM calificaciones c
                INNER JOIN alumnos a
                    ON c.id_estudiante = a.id
                INNER JOIN cursos cur
                    ON c.id_curso = cur.id_curso
                INNER JOIN profesores p
                    ON c.id_profesor = p.id_profesor
                ORDER BY c.id_calificacion DESC";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorProfesor($id_profesor) {
        $sql = "SELECT
                    c.id_calificacion,
                    a.nombre AS nombre_alumno,
                    a.apellido AS apellido_alumno,
                    cur.nombre_curso,
                    c.nota_1,
                    c.promedio
                FROM calificaciones c
                INNER JOIN alumnos a
                    ON c.id_estudiante = a.id
                INNER JOIN cursos cur
                    ON c.id_curso = cur.id_curso
                WHERE c.id_profesor = :id_profesor
                ORDER BY c.id_calificacion DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_profesor", $id_profesor);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorEstudiante($id_alumno) {
        $sql = "SELECT
                    c.id_calificacion,
                    cur.nombre_curso,
                    c.nota_1,
                    c.promedio,
                    c.fecha_registro,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_profesor
                FROM calificaciones c
                INNER JOIN cursos cur
                    ON c.id_curso = cur.id_curso
                INNER JOIN profesores p
                    ON c.id_profesor = p.id_profesor
                WHERE c.id_estudiante = :id_alumno
                ORDER BY c.fecha_registro DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_alumno", $id_alumno);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT
                    c.*,
                    a.nombre AS nombre_alumno,
                    a.apellido AS apellido_alumno
                FROM calificaciones c
                INNER JOIN alumnos a
                    ON c.id_estudiante = a.id
                WHERE c.id_calificacion = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeCalificacion($id_estudiante, $id_curso) {
        $sql = "SELECT COUNT(*) FROM calificaciones WHERE id_estudiante = :id_estudiante AND id_curso = :id_curso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->bindParam(":id_curso", $id_curso);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function crear($id_estudiante, $id_curso, $id_profesor, $nota_1) {
        $promedio = $this->calcularPromedio($nota_1);

        $sql = "INSERT INTO calificaciones
                (id_estudiante, id_curso, id_profesor, nota_1, promedio, fecha_registro)
                VALUES (:id_estudiante, :id_curso, :id_profesor, :nota_1, :promedio, CURDATE())";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->bindParam(":id_curso", $id_curso);
        $stmt->bindParam(":id_profesor", $id_profesor);
        $stmt->bindParam(":nota_1", $nota_1);
        $stmt->bindParam(":promedio", $promedio);

        return $stmt->execute();
    }

    public function actualizar($id, $id_estudiante, $id_curso, $id_profesor, $nota_1) {
        $promedio = $this->calcularPromedio($nota_1);

        $sql = "UPDATE calificaciones
                SET id_estudiante = :id_estudiante,
                    id_curso      = :id_curso,
                    id_profesor   = :id_profesor,
                    nota_1        = :nota_1,
                    promedio      = :promedio
                WHERE id_calificacion = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_estudiante", $id_estudiante);
        $stmt->bindParam(":id_curso", $id_curso);
        $stmt->bindParam(":id_profesor", $id_profesor);
        $stmt->bindParam(":nota_1", $nota_1);
        $stmt->bindParam(":promedio", $promedio);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM calificaciones WHERE id_calificacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
