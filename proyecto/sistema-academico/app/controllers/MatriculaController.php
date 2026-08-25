<?php
require_once __DIR__ . '/../../config/Database.php';

class MatriculaController {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function listar() {
        try {
            $sql = "SELECT m.id_matricula, a.nombre, a.apellido, g.nombre_grado, g.seccion, m.fecha_matricula 
                    FROM matriculas m
                    LEFT JOIN alumnos a ON m.id_estudiante = a.id
                    LEFT JOIN grados g ON m.id_grado = g.id_grado
                    ORDER BY m.id_matricula DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listar: " . $e->getMessage());
            return [];
        }
    }

    public function estudiantesNoMatriculados() {
        try {
            // Todo alumno (venga de alta manual del admin o de auto-registro,
            // ambos ya tienen fila propia en "alumnos") que aún no está
            // matriculado en ningún grado/sección.
            $sql = "SELECT a.id, a.nombre, a.apellido
                    FROM alumnos a
                    WHERE a.id NOT IN (SELECT id_estudiante FROM matriculas)
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en estudiantesNoMatriculados: " . $e->getMessage());
            return [];
        }
    }

    public function yaMatriculado($id_estudiante) {
        $sql  = "SELECT COUNT(*) FROM matriculas WHERE id_estudiante = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_estudiante]);
        return $stmt->fetchColumn() > 0;
    }

    public function crear($id_estudiante, $id_grado) {
        if (empty($id_estudiante) || empty($id_grado)) return false;
        try {
            // El alumno ya debe existir en "alumnos" (alta manual del admin
            // o fila creada automáticamente al auto-registrarse). Si no
            // existe, no inventamos uno nuevo: evitamos duplicar alumnos.
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE id = ?");
            $check->execute([$id_estudiante]);
            if ($check->fetchColumn() == 0) {
                error_log("Matrícula rechazada: alumno $id_estudiante no existe.");
                return false;
            }

            $sql  = "INSERT INTO matriculas (id_estudiante, id_grado, fecha_matricula) VALUES (?, ?, CURDATE())";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([$id_estudiante, $id_grado]);

            if ($ok) {
                // ── Sincroniza automáticamente el campo "grado" del alumno ──
                $this->sincronizarGradoAlumno($id_estudiante, $id_grado);
            }
            return $ok;
        } catch (PDOException $e) {
            error_log("Error al insertar matrícula: " . $e->getMessage());
            return false;
        }
    }

    /** Copia "nombre_grado + sección" al alumno para que se vea "asignado" en el listado */
    private function sincronizarGradoAlumno($id_estudiante, $id_grado) {
        try {
            $g = $this->pdo->prepare("SELECT nombre_grado, seccion FROM grados WHERE id_grado = ?");
            $g->execute([$id_grado]);
            $grado = $g->fetch(PDO::FETCH_ASSOC);
            if ($grado) {
                $texto = trim($grado['nombre_grado'] . ' ' . $grado['seccion']);
                $u = $this->pdo->prepare("UPDATE alumnos SET grado = ? WHERE id = ?");
                $u->execute([$texto, $id_estudiante]);
            }
        } catch (PDOException $e) {
            error_log("Error al sincronizar grado del alumno: " . $e->getMessage());
        }
    }

    public function eliminar($id) {
        try {
            // Averigua a qué alumno pertenece antes de borrar, para poder
            // limpiar su campo "grado" si se le quita la matrícula.
            $get = $this->pdo->prepare("SELECT id_estudiante FROM matriculas WHERE id_matricula = ?");
            $get->execute([$id]);
            $fila = $get->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->pdo->prepare("DELETE FROM matriculas WHERE id_matricula = ?");
            $ok = $stmt->execute([$id]);

            if ($ok && $fila) {
                $u = $this->pdo->prepare("UPDATE alumnos SET grado = NULL WHERE id = ?");
                $u->execute([$fila['id_estudiante']]);
            }
            return $ok;
        } catch (PDOException $e) {
            error_log("Error al eliminar: " . $e->getMessage());
            return false;
        }
    }
}