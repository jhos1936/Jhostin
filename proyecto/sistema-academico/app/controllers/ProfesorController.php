<?php
require_once __DIR__ . '/../../config/Database.php';

class ProfesorController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function getError() {
        return $this->error;
    }

    public function listar() {
        try {
            // CORREGIDO: Apuntamos a la tabla real 'profesores'
            $stmt = $this->pdo->query("SELECT * FROM profesores ORDER BY id_profesor DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Lista usuarios con rol 'profesor' que aún no están vinculados
     * a ningún registro en la tabla profesores.
     * Si no existe la columna id_usuario en profesores, devuelve todos los
     * usuarios con rol profesor.
     */
    public function listarUsuariosDisponibles() {
        try {
            // Verificar si existe columna id_usuario en profesores
            $stmt = $this->pdo->query("SHOW COLUMNS FROM profesores LIKE 'id_usuario'");
            $tieneIdUsuario = $stmt->rowCount() > 0;

            if ($tieneIdUsuario) {
                // Solo usuarios no vinculados aún
                $sql = "SELECT u.id_usuario, u.usuario, r.nombre AS rol_nombre
                        FROM usuarios u
                        LEFT JOIN roles r ON u.id_rol = r.id_rol
                        WHERE LOWER(r.nombre) LIKE '%profesor%'
                          AND u.id_usuario NOT IN (
                              SELECT id_usuario FROM profesores WHERE id_usuario IS NOT NULL
                          )
                        ORDER BY u.usuario ASC";
            } else {
                // Sin FK: listar todos los usuarios con rol profesor
                $sql = "SELECT u.id_usuario, u.usuario, r.nombre AS rol_nombre
                        FROM usuarios u
                        LEFT JOIN roles r ON u.id_rol = r.id_rol
                        WHERE LOWER(r.nombre) LIKE '%profesor%'
                        ORDER BY u.usuario ASC";
            }
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback: listar todos sin filtro de rol
            try {
                $stmt = $this->pdo->query(
                    "SELECT id_usuario, usuario FROM usuarios ORDER BY usuario ASC"
                );
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                $this->error = "Error al listar usuarios: " . $e2->getMessage();
                return [];
            }
        }
    }

    public function obtenerPorId($id) {
        if (empty($id)) return false;
        try {
            // CORREGIDO: Apuntamos a la tabla real 'profesores'
            $stmt = $this->pdo->prepare("SELECT * FROM profesores WHERE id_profesor = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al obtener: " . $e->getMessage();
            return false;
        }
    }

    /** Verifica si la columna 'dni' existe en la tabla profesores */
    private function columnaDniExiste() {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM profesores LIKE 'dni'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crear($nombre, $apellido, $dni, $especialidad, $id_usuario = null, $id_grado = null) {
        try {
            $tieneDni       = $this->columnaDniExiste();
            $tieneIdUsuario = $this->columnaIdUsuarioExiste();
            $idGrado        = $id_grado ?: null;

            if ($tieneDni && $tieneIdUsuario) {
                $sql    = "INSERT INTO profesores (nombre, apellido, dni, especialidad, id_usuario, id_grado) VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$nombre, $apellido, $dni, $especialidad, $id_usuario ?: null, $idGrado];
            } elseif ($tieneDni) {
                $sql    = "INSERT INTO profesores (nombre, apellido, dni, especialidad, id_grado) VALUES (?, ?, ?, ?, ?)";
                $params = [$nombre, $apellido, $dni, $especialidad, $idGrado];
            } elseif ($tieneIdUsuario) {
                $sql    = "INSERT INTO profesores (nombre, apellido, especialidad, id_usuario, id_grado) VALUES (?, ?, ?, ?, ?)";
                $params = [$nombre, $apellido, $especialidad, $id_usuario ?: null, $idGrado];
            } else {
                $sql    = "INSERT INTO profesores (nombre, apellido, especialidad, id_grado) VALUES (?, ?, ?, ?)";
                $params = [$nombre, $apellido, $especialidad, $idGrado];
            }
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->error = ($e->getCode() == 23000) ? "El DNI ya está registrado." : $e->getMessage();
            return false;
        }
    }

    /** Verifica si la columna 'id_usuario' existe en la tabla profesores */
    private function columnaIdUsuarioExiste() {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM profesores LIKE 'id_usuario'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $nombre, $apellido, $dni, $especialidad) {
        if (empty($id)) return false;
        try {
            if ($this->columnaDniExiste()) {
                $sql  = "UPDATE profesores SET nombre = ?, apellido = ?, dni = ?, especialidad = ? WHERE id_profesor = ?";
                $params = [$nombre, $apellido, $dni, $especialidad, $id];
            } else {
                $sql  = "UPDATE profesores SET nombre = ?, apellido = ?, especialidad = ? WHERE id_profesor = ?";
                $params = [$nombre, $apellido, $especialidad, $id];
            }
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->error = ($e->getCode() == 23000) ? "El DNI ya pertenece a otro profesor." : $e->getMessage();
            return false;
        }
    }

    public function eliminar($id) {
        if (empty($id)) return false;
        try {
            // CORREGIDO: Apuntamos a la tabla real 'profesores'
            $stmt = $this->pdo->prepare("DELETE FROM profesores WHERE id_profesor = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->error = "Error al eliminar: " . $e->getMessage();
            return false;
        }
    }
}