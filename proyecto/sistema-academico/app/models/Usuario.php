<?php
require_once __DIR__ . "/Model.php";

class Usuario extends Model {

    public function login($usuario) {
        $sql = "SELECT
                    u.*,
                    r.nombre AS rol
                FROM usuarios u
                INNER JOIN roles r
                    ON u.id_rol = r.id_rol
                WHERE u.usuario = :usuario";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar() {
        $sql = "SELECT u.id_usuario, u.usuario, r.nombre AS rol_nombre
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                ORDER BY u.id_usuario DESC";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($usuario, $password, $id_rol) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (usuario, password, id_rol)
                VALUES (:usuario, :password, :id_rol)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id_rol", $id_rol);

        return $stmt->execute();
    }

    public function actualizar($id, $usuario, $id_rol) {
        $sql = "UPDATE usuarios SET usuario = :usuario, id_rol = :id_rol WHERE id_usuario = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":usuario", $usuario);
        $stmt->bindParam(":id_rol", $id_rol);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function actualizarPassword($id, $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function listarRoles() {
        $stmt = $this->conexion->query("SELECT * FROM roles ORDER BY id_rol ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
