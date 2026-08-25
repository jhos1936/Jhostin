<?php
require_once __DIR__ . '/../config/conexion.php';
class Usuario {
    private $conn;
    private $tabla = "usuarios";
    
    public function __construct() {
        $this->conn = Conexion::getInstance()->getConn();
    }
    
    public function validarLogin($email, $password) {
        // Intenta leer cliente_id; si la columna no existe aún, usa NULL
        $tieneClienteId = false;
        $check = $this->conn->query("SHOW COLUMNS FROM " . $this->tabla . " LIKE 'cliente_id'");
        if ($check && $check->num_rows > 0) {
            $tieneClienteId = true;
        }

        if ($tieneClienteId) {
            $query = "SELECT id, nombre, email, password, rol, activo, cliente_id FROM " . $this->tabla . " WHERE email = ? AND activo = 1";
        } else {
            $query = "SELECT id, nombre, email, password, rol, activo FROM " . $this->tabla . " WHERE email = ? AND activo = 1";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        if ($tieneClienteId) {
            $stmt->bind_result($id, $nombre, $emailDB, $hash, $rol, $activo, $cliente_id);
        } else {
            $stmt->bind_result($id, $nombre, $emailDB, $hash, $rol, $activo);
            $cliente_id = null;
        }

        if ($stmt->fetch()) {
            $stmt->close();
            if (password_verify($password, $hash)) {
                return [
                    'id'         => $id,
                    'nombre'     => $nombre,
                    'email'      => $emailDB,
                    'rol'        => $rol,
                    'activo'     => $activo,
                    'cliente_id' => $cliente_id
                ];
            }
        } else {
            $stmt->close();
        }
        return false;
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT id, nombre, email, rol FROM " . $this->tabla . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($uid, $nombre, $email, $rol);
        if ($stmt->fetch()) {
            $stmt->close();
            return ['id' => $uid, 'nombre' => $nombre, 'email' => $email, 'rol' => $rol];
        }
        $stmt->close();
        return null;
    }

    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->tabla . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    // Crea las columnas necesarias para la recuperación de contraseña si aún no existen
    private function asegurarColumnasRecuperacion() {
        $check = $this->conn->query("SHOW COLUMNS FROM " . $this->tabla . " LIKE 'reset_token'");
        if ($check && $check->num_rows == 0) {
            $this->conn->query(
                "ALTER TABLE " . $this->tabla . "
                 ADD COLUMN reset_token VARCHAR(64) NULL,
                 ADD COLUMN reset_token_expira DATETIME NULL"
            );
        }
    }

    // Genera un token de recuperación para el email indicado (si existe y está activo)
    public function generarTokenRecuperacion($email) {
        $this->asegurarColumnasRecuperacion();

        $stmt = $this->conn->prepare("SELECT id FROM " . $this->tabla . " WHERE email = ? AND activo = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $existe = $stmt->fetch();
        $stmt->close();

        if (!$existe) {
            return false;
        }

        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmtU = $this->conn->prepare("UPDATE " . $this->tabla . " SET reset_token = ?, reset_token_expira = ? WHERE id = ?");
        $stmtU->bind_param("ssi", $token, $expira, $id);
        $stmtU->execute();
        $stmtU->close();

        return $token;
    }

    // Verifica que el token exista y no haya vencido. Devuelve los datos del usuario o false
    public function validarTokenRecuperacion($token) {
        $this->asegurarColumnasRecuperacion();

        if (empty($token)) {
            return false;
        }

        $stmt = $this->conn->prepare("SELECT id, reset_token_expira FROM " . $this->tabla . " WHERE reset_token = ? AND activo = 1");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->bind_result($id, $expira);
        $encontrado = $stmt->fetch();
        $stmt->close();

        if (!$encontrado) {
            return false;
        }

        if (strtotime($expira) < time()) {
            return false; // token vencido
        }

        return ['id' => $id];
    }

    // Guarda la nueva contraseña y elimina el token usado
    public function actualizarPassword($id, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE " . $this->tabla . " SET password = ?, reset_token = NULL, reset_token_expira = NULL WHERE id = ?");
        $stmt->bind_param("si", $hash, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function registrar($nombre, $email, $password) {
        if ($this->emailExiste($email)) {
            return ['exito' => false, 'mensaje' => 'El correo ya está registrado.'];
        }
        $hash   = password_hash($password, PASSWORD_DEFAULT);
        $rol    = 'cliente';
        $activo = 1;

        // Buscar si ya existe un cliente con ese email
        $cliente_id = null;
        $stmtC = $this->conn->prepare("SELECT id FROM clientes WHERE email = ? LIMIT 1");
        $stmtC->bind_param("s", $email);
        $stmtC->execute();
        $stmtC->bind_result($cid);
        if ($stmtC->fetch()) {
            $cliente_id = $cid;
        }
        $stmtC->close();

        // Si no existe en clientes, crearlo automaticamente
        if (is_null($cliente_id)) {
            $stmtIns = $this->conn->prepare(
                "INSERT INTO clientes (nombre, email, activo) VALUES (?, ?, 1)"
            );
            $stmtIns->bind_param("ss", $nombre, $email);
            $stmtIns->execute();
            $cliente_id = $this->conn->insert_id;
            $stmtIns->close();
        }

        // Registrar usuario con cliente_id vinculado
        $query = "INSERT INTO " . $this->tabla . " (nombre, email, password, rol, activo, cliente_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bind_param("ssssii", $nombre, $email, $hash, $rol, $activo, $cliente_id);
        if ($stmt->execute()) {
            $stmt->close();
            return ['exito' => true, 'mensaje' => 'Cuenta creada correctamente. ¡Ya puedes iniciar sesión!'];
        } else {
            $stmt->close();
            return ['exito' => false, 'mensaje' => 'Error al registrar. Intenta de nuevo.'];
        }
    }
}
