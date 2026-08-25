<?php
require_once __DIR__ . '/../../config/Database.php';

class CalificacionController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function getError() { return $this->error; }

    /** Promedio redondeado al entero más cercano (≥0.5 sube) */
    private function calcularPromedio($nota_1, $nota_2 = null, $nota_3 = null) {
        $notas  = array_filter([$nota_1, $nota_2, $nota_3], fn($n) => $n !== null && $n !== '');
        if (empty($notas)) return 0;
        $prom = array_sum($notas) / count($notas);
        return ($prom - floor($prom) >= 0.5) ? (int)ceil($prom) : (int)floor($prom);
    }

    public function listar() {
        try {
            $sql = "SELECT
                        c.*,
                        a.nombre  AS nombre_alumno,
                        a.apellido AS apellido_alumno,
                        cur.nombre_curso,
                        CONCAT(p.nombre,' ',p.apellido) AS nombre_profesor,
                        g.nombre_grado,
                        g.seccion
                    FROM calificaciones c
                    INNER JOIN alumnos    a   ON c.id_estudiante = a.id
                    INNER JOIN cursos     cur ON c.id_curso      = cur.id_curso
                    INNER JOIN profesores p   ON c.id_profesor   = p.id_profesor
                    LEFT  JOIN matriculas m   ON m.id_estudiante = a.id
                    LEFT  JOIN grados     g   ON m.id_grado      = g.id_grado
                    ORDER BY c.id_calificacion DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    /** Listar calificaciones filtradas por Grado/Sección y Curso (vista admin) */
    public function listarPorGradoYCurso($id_grado, $id_curso) {
        try {
            $sql = "SELECT
                        c.*,
                        a.nombre  AS nombre_alumno,
                        a.apellido AS apellido_alumno,
                        cur.nombre_curso,
                        CONCAT(p.nombre,' ',p.apellido) AS nombre_profesor,
                        g.nombre_grado,
                        g.seccion
                    FROM calificaciones c
                    INNER JOIN alumnos    a   ON c.id_estudiante = a.id
                    INNER JOIN cursos     cur ON c.id_curso      = cur.id_curso
                    INNER JOIN profesores p   ON c.id_profesor   = p.id_profesor
                    INNER JOIN matriculas m   ON m.id_estudiante = a.id
                    INNER JOIN grados     g   ON m.id_grado      = g.id_grado
                    WHERE g.id_grado = :id_grado AND c.id_curso = :id_curso
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id_grado", $id_grado, PDO::PARAM_INT);
            $stmt->bindParam(":id_curso", $id_curso, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function listarPorProfesor($id_profesor) {
        try {
            $sql = "SELECT
                        c.id_calificacion,
                        a.nombre  AS nombre_alumno,
                        a.apellido AS apellido_alumno,
                        cur.nombre_curso,
                        g.nombre_grado,
                        g.seccion,
                        c.nota_1, c.nota_2, c.nota_3,
                        c.promedio
                    FROM calificaciones c
                    INNER JOIN alumnos    a   ON c.id_estudiante = a.id
                    INNER JOIN cursos     cur ON c.id_curso      = cur.id_curso
                    LEFT  JOIN matriculas m   ON m.id_estudiante = a.id
                    LEFT  JOIN grados     g   ON m.id_grado      = g.id_grado
                    WHERE c.id_profesor = ?
                    ORDER BY g.nombre_grado, g.seccion, a.apellido";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_profesor]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function listarPorSeccionYCurso($id_grado, $id_curso, $id_profesor) {
        try {
            // Alumnos matriculados en ese grado, con sus notas (si existen) para ese curso/profesor
            $sql = "SELECT
                        a.id,
                        a.nombre,
                        a.apellido,
                        a.correo,
                        c.id_calificacion,
                        c.nota_1,
                        c.nota_2,
                        c.nota_3,
                        c.promedio
                    FROM matriculas m
                    INNER JOIN alumnos a ON m.id_estudiante = a.id
                    LEFT JOIN calificaciones c
                        ON c.id_estudiante = a.id
                       AND c.id_curso      = :id_curso
                       AND c.id_profesor   = :id_profesor
                    WHERE m.id_grado = :id_grado
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":id_grado",    $id_grado);
            $stmt->bindParam(":id_curso",    $id_curso);
            $stmt->bindParam(":id_profesor", $id_profesor);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function listarPorEstudiante($id_alumno) {
        try {
            $sql = "SELECT
                        c.id_calificacion,
                        cur.nombre_curso,
                        c.nota_1, c.nota_2, c.nota_3,
                        c.promedio,
                        c.fecha_registro,
                        CONCAT(p.nombre,' ',p.apellido) AS nombre_profesor
                    FROM calificaciones c
                    INNER JOIN cursos     cur ON c.id_curso    = cur.id_curso
                    INNER JOIN profesores p   ON c.id_profesor = p.id_profesor
                    WHERE c.id_estudiante = ?
                    ORDER BY c.fecha_registro DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_alumno]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function obtenerPorId($id) {
        try {
            $sql = "SELECT c.*,
                           a.nombre  AS nombre_alumno,
                           a.apellido AS apellido_alumno
                    FROM calificaciones c
                    INNER JOIN alumnos a ON c.id_estudiante = a.id
                    WHERE c.id_calificacion = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) { return null; }
    }

    public function existeCalificacion($id_estudiante, $id_curso) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM calificaciones WHERE id_estudiante = ? AND id_curso = ?"
            );
            $stmt->execute([$id_estudiante, $id_curso]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return false; }
    }

    /** true si el profesor tiene ese curso asignado (en algún grado) */
    public function profesorTieneCurso($id_profesor, $id_curso) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM secciones_profesor WHERE id_profesor = ? AND id_curso = ?"
            );
            $stmt->execute([$id_profesor, $id_curso]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return false; }
    }

    public function estaMatriculado($id_estudiante) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM matriculas WHERE id_estudiante = ?");
            $stmt->execute([$id_estudiante]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) { return false; }
    }

    /** Crear nota con hasta 3 unidades */
    public function crear($id_estudiante, $id_curso, $id_profesor, $nota_1, $nota_2 = null, $nota_3 = null) {
        try {
            if (!$this->profesorTieneCurso($id_profesor, $id_curso)) {
                $this->error = "No tienes asignado este curso.";
                return false;
            }
            if (!$this->estaMatriculado($id_estudiante)) {
                $this->error = "Este estudiante todavía no está matriculado en ningún grado.";
                return false;
            }
            if ($this->existeCalificacion($id_estudiante, $id_curso)) {
                $this->error = "Este estudiante ya tiene calificación en ese curso.";
                return false;
            }
            $promedio = $this->calcularPromedio($nota_1, $nota_2, $nota_3);
            $sql = "INSERT INTO calificaciones
                        (id_estudiante, id_curso, id_profesor, nota_1, nota_2, nota_3, promedio, fecha_registro)
                    VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $id_estudiante, $id_curso, $id_profesor,
                $nota_1,
                ($nota_2 !== '' ? $nota_2 : null),
                ($nota_3 !== '' ? $nota_3 : null),
                $promedio
            ]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /** Guardar/actualizar notas por unidad desde la vista de sección */
    public function guardarPorSeccion($id_estudiante, $id_curso, $id_profesor, $nota_1, $nota_2 = null, $nota_3 = null) {
        try {
            $nota_2_val = ($nota_2 !== '' && $nota_2 !== null) ? $nota_2 : null;
            $nota_3_val = ($nota_3 !== '' && $nota_3 !== null) ? $nota_3 : null;
            $promedio   = $this->calcularPromedio($nota_1, $nota_2_val, $nota_3_val);

            if ($this->existeCalificacion($id_estudiante, $id_curso)) {
                $sql = "UPDATE calificaciones
                        SET nota_1   = ?, nota_2 = ?, nota_3 = ?, promedio = ?
                        WHERE id_estudiante = ? AND id_curso = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$nota_1, $nota_2_val, $nota_3_val, $promedio, $id_estudiante, $id_curso]);
            } else {
                $sql = "INSERT INTO calificaciones
                            (id_estudiante, id_curso, id_profesor, nota_1, nota_2, nota_3, promedio, fecha_registro)
                        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE())";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $id_estudiante, $id_curso, $id_profesor,
                    $nota_1, $nota_2_val, $nota_3_val, $promedio
                ]);
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function actualizar($id, $id_estudiante, $id_curso, $id_profesor,
                               $nota_1, $nota_2 = null, $nota_3 = null,
                               $esAdmin = false, $id_usuario_sesion = 0) {
        try {
            if (!$esAdmin) {
                $actual = $this->obtenerPorId($id);
                if (!$actual || $actual['id_profesor'] != $id_usuario_sesion) {
                    $this->error = "Acceso denegado.";
                    return false;
                }
                if (!$this->profesorTieneCurso($id_profesor, $id_curso)) {
                    $this->error = "No tienes asignado este curso.";
                    return false;
                }
            }
            $nota_2_val = ($nota_2 !== '' && $nota_2 !== null) ? $nota_2 : null;
            $nota_3_val = ($nota_3 !== '' && $nota_3 !== null) ? $nota_3 : null;
            $promedio   = $this->calcularPromedio($nota_1, $nota_2_val, $nota_3_val);

            $sql = "UPDATE calificaciones
                    SET id_estudiante = ?, id_curso = ?, id_profesor = ?,
                        nota_1 = ?, nota_2 = ?, nota_3 = ?, promedio = ?
                    WHERE id_calificacion = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $id_estudiante, $id_curso, $id_profesor,
                $nota_1, $nota_2_val, $nota_3_val, $promedio, $id
            ]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM calificaciones WHERE id_calificacion = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
