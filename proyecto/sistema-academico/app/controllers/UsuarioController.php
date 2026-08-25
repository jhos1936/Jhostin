<?php
require_once __DIR__ . '/../../config/Database.php';

class UsuarioController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function getError() {
        return $this->error;
    }

    /**
     * Lista todos los usuarios uniendo con la tabla roles
     */
    public function listar() {
        try {
            $sql = "SELECT u.id_usuario, u.usuario, r.nombre AS rol_nombre 
                    FROM usuarios u
                    LEFT JOIN roles r ON u.id_rol = r.id_rol
                    ORDER BY u.id_usuario DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar usuarios: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Lista usuarios filtrando por nombre de usuario
     */
    public function listarPorNombre($nombre) {
        try {
            $sql = "SELECT u.id_usuario, u.usuario, r.nombre AS rol_nombre 
                    FROM usuarios u
                    LEFT JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.usuario LIKE ?
                    ORDER BY u.id_usuario DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['%' . $nombre . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al listar usuarios: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Obtiene un usuario por su ID (para el modal de edición)
     */
    public function obtenerPorId($id) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id_usuario, usuario, id_rol FROM usuarios WHERE id_usuario = ?"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = "Error al obtener usuario: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Crea un nuevo usuario
     */
    public function crear($usuario, $password, $id_rol) {
        try {
            $sql  = "INSERT INTO usuarios (usuario, password, id_rol) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $usuario,
                password_hash($password, PASSWORD_DEFAULT),
                $id_rol
            ]);
        } catch (PDOException $e) {
            $this->error = "Error al crear: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Actualiza un usuario.
     * Si $password está vacío, no modifica la contraseña almacenada.
     */
    public function actualizar($id, $usuario, $password, $id_rol) {
        try {
            if (!empty($password)) {
                // Actualizar también la contraseña
                $sql  = "UPDATE usuarios SET usuario = ?, password = ?, id_rol = ? WHERE id_usuario = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([
                    $usuario,
                    password_hash($password, PASSWORD_DEFAULT),
                    $id_rol,
                    $id
                ]);
            } else {
                // Solo actualizar usuario y rol
                $sql  = "UPDATE usuarios SET usuario = ?, id_rol = ? WHERE id_usuario = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$usuario, $id_rol, $id]);
            }
        } catch (PDOException $e) {
            $this->error = "Error al actualizar: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Elimina un usuario por su ID
     */
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            $this->error = "Error al eliminar: " . $e->getMessage();
            return false;
        }
    }
}