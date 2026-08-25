<?php
require_once __DIR__ . '/../../config/Database.php';

class EstudianteController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function getError() { return $this->error; }

    public function listar() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM alumnos ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Solo alumnos que ya están matriculados en algún grado/sección.
     * Se usa en los formularios de "Registrar Nota": no tiene sentido
     * poder calificar a alguien que todavía no está matriculado.
     */
    public function listarMatriculados() {
        try {
            $sql = "SELECT DISTINCT a.*
                    FROM alumnos a
                    INNER JOIN matriculas m ON m.id_estudiante = a.id
                    ORDER BY a.apellido, a.nombre";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar: " . $e->getMessage();
            return [];
        }
    }

    public function listarPorGradoYNombre($id_grado = null, $nombre = null) {
        try {
            $sql = "SELECT * FROM alumnos WHERE 1=1";
            $params = [];

            if (!empty($id_grado)) {
                $sql .= " AND grado = ?";
                $params[] = $id_grado;
            }

            if (!empty($nombre)) {
                $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR CONCAT(nombre, ' ', apellido) LIKE ?)";
                $like = '%' . $nombre . '%';
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            $sql .= " ORDER BY id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar: " . $e->getMessage();
            return [];
        }
    }

    public function obtenerPorId($id) {
        if (empty($id)) return false;
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM alumnos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al obtener: " . $e->getMessage();
            return false;
        }
    }

    public function crear($nombre, $apellido, $correo, $grado) {
        if (empty($nombre) || empty($apellido) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->error = "Datos inválidos o correo incorrecto.";
            return false;
        }
        try {
            $sql = "INSERT INTO alumnos (nombre, apellido, correo, grado) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nombre, $apellido, $correo, $grado]);
        } catch (PDOException $e) {
            $this->error = ($e->getCode() == 23000) ? "El correo ya está registrado." : "Error: " . $e->getMessage();
            return false;
        }
    }

    public function ultimoId() {
        try {
            $stmt = $this->pdo->query("SELECT MAX(id) FROM alumnos");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function actualizar($id, $nombre, $apellido, $correo, $grado) {
        if (empty($id)) return false;
        try {
            $sql = "UPDATE alumnos SET nombre = ?, apellido = ?, correo = ?, grado = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$nombre, $apellido, $correo, $grado, $id]);
        } catch (PDOException $e) {
            $this->error = "Error al actualizar: " . $e->getMessage();
            return false;
        }
    }

    public function eliminar($id) {
        if (empty($id)) return false;
        try {
            $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Código 23000 = violación de llave foránea (el alumno tiene
            // matrícula y/o calificaciones registradas y no se puede borrar
            // directamente sin quitar antes esas referencias).
            $this->error = ($e->getCode() == 23000)
                ? "No se puede eliminar: este alumno tiene una matrícula y/o notas registradas. Elimina primero su matrícula (o sus notas) desde el panel correspondiente."
                : "No se puede eliminar: " . $e->getMessage();
            return false;
        }
    }
}
