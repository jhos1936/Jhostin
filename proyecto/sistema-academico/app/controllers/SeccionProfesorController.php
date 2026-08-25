<?php
require_once __DIR__ . '/../../config/Database.php';

class SeccionProfesorController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function getError() { return $this->error; }

    public function listar() {
        try {
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
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function listarPorProfesor($id_profesor) {
        try {
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
                    WHERE sp.id_profesor = ?
                    ORDER BY g.nombre_grado, g.seccion, c.nombre_curso";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_profesor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    /** Alumnos matriculados en un grado */
    public function alumnosPorGrado($id_grado) {
        try {
            $sql = "SELECT
                        a.id,
                        a.nombre,
                        a.apellido,
                        a.correo
                    FROM matriculas m
                    INNER JOIN alumnos a ON m.id_estudiante = a.id
                    WHERE m.id_grado = ?
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_grado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    /** Cursos únicos que el profesor tiene asignados (para selects) */
    public function cursosDelProfesor($id_profesor) {
        try {
            $sql = "SELECT DISTINCT c.id_curso, c.nombre_curso
                    FROM secciones_profesor sp
                    INNER JOIN cursos c ON sp.id_curso = c.id_curso
                    WHERE sp.id_profesor = ?
                    ORDER BY c.nombre_curso";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_profesor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    /** Alumnos matriculados en cualquiera de los grados/secciones que dicta el profesor */
    public function alumnosDelProfesor($id_profesor) {
        try {
            $sql = "SELECT DISTINCT a.id, a.nombre, a.apellido
                    FROM secciones_profesor sp
                    INNER JOIN matriculas m ON m.id_grado = sp.id_grado
                    INNER JOIN alumnos a   ON a.id = m.id_estudiante
                    WHERE sp.id_profesor = ?
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_profesor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    /** true si el profesor tiene asignado ese curso (en algún grado) */
    public function profesorTieneCurso($id_profesor, $id_curso) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM secciones_profesor WHERE id_profesor = ? AND id_curso = ?"
            );
            $stmt->execute([$id_profesor, $id_curso]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /** Notas existentes de un alumno para un curso específico */
    public function notasAlumno($id_estudiante, $id_curso) {
        try {
            $sql = "SELECT id_calificacion, nota_1, nota_2, nota_3, promedio
                    FROM calificaciones
                    WHERE id_estudiante = ? AND id_curso = ?
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_estudiante, $id_curso]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function crear($id_profesor, $id_grado, $id_curso, $anio_escolar) {
        try {
            $sql = "INSERT INTO secciones_profesor
                        (id_profesor, id_grado, id_curso, anio_escolar)
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id_profesor, $id_grado, $id_curso, $anio_escolar]);
        } catch (PDOException $e) {
            $this->error = ($e->getCode() == 23000)
                ? "Esta asignación ya existe."
                : $e->getMessage();
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM secciones_profesor WHERE id_asignacion = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
?>
