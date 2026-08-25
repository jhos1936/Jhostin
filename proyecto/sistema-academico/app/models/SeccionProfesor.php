<?php
require_once __DIR__ . "/Model.php";

class SeccionProfesor extends Model {

    /** Todas las asignaciones con datos completos (para admin) */
    public function listar() {
        $sql = "SELECT
                    sp.id_asignacion,
                    sp.anio_escolar,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_profesor,
                    p.id_profesor,
                    g.id_grado,
                    g.nombre_grado,
                    g.seccion,
                    c.id_curso,
                    c.nombre_curso
                FROM secciones_profesor sp
                INNER JOIN profesores p ON sp.id_profesor = p.id_profesor
                INNER JOIN grados     g ON sp.id_grado    = g.id_grado
                INNER JOIN cursos     c ON sp.id_curso    = c.id_curso
                ORDER BY p.apellido, g.nombre_grado, g.seccion, c.nombre_curso";
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Asignaciones de un profesor específico */
    public function listarPorProfesor($id_profesor) {
        $sql = "SELECT
                    sp.id_asignacion,
                    sp.anio_escolar,
                    g.id_grado,
                    g.nombre_grado,
                    g.seccion,
                    c.id_curso,
                    c.nombre_curso
                FROM secciones_profesor sp
                INNER JOIN grados g ON sp.id_grado = g.id_grado
                INNER JOIN cursos c ON sp.id_curso = c.id_curso
                WHERE sp.id_profesor = :id_profesor
                ORDER BY g.nombre_grado, g.seccion, c.nombre_curso";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_profesor", $id_profesor);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Alumnos matriculados en un grado (sección) específico */
    public function alumnosPorGrado($id_grado) {
        $sql = "SELECT
                    a.id,
                    a.nombre,
                    a.apellido,
                    a.correo
                FROM matriculas m
                INNER JOIN alumnos a ON m.id_estudiante = a.id
                WHERE m.id_grado = :id_grado
                ORDER BY a.apellido, a.nombre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_grado", $id_grado);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($id_profesor, $id_grado, $id_curso, $anio_escolar) {
        $sql = "INSERT INTO secciones_profesor
                    (id_profesor, id_grado, id_curso, anio_escolar)
                VALUES (:id_profesor, :id_grado, :id_curso, :anio_escolar)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id_profesor",  $id_profesor);
        $stmt->bindParam(":id_grado",     $id_grado);
        $stmt->bindParam(":id_curso",     $id_curso);
        $stmt->bindParam(":anio_escolar", $anio_escolar);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql  = "DELETE FROM secciones_profesor WHERE id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM secciones_profesor WHERE id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
