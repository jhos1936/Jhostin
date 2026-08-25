<?php
require_once __DIR__ . '/../../config/Database.php';

class RegistroController {
    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function usuarioExiste($usuario) {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        return $stmt->rowCount() > 0;
    }

    public function obtenerIdRolAlumno() {
        $stmt = $this->pdo->prepare("SELECT id_rol FROM roles WHERE LOWER(nombre) = 'estudiante' LIMIT 1");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_rol'] : null;
    }

    public function registrar($usuario, $password) {
        if ($this->usuarioExiste($usuario)) {
            return ['exito' => false, 'mensaje' => 'El nombre de usuario ya está en uso.'];
        }
        $id_rol = $this->obtenerIdRolAlumno();
        if (!$id_rol) {
            return ['exito' => false, 'mensaje' => 'No se encontró el rol Estudiante en la base de datos.'];
        }
        if (strlen($password) < 6) {
            return ['exito' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres.'];
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("INSERT INTO usuarios (usuario, password, id_rol) VALUES (?, ?, ?)");
            $stmt->execute([$usuario, $hash, $id_rol]);
            $id_usuario = $this->pdo->lastInsertId();

            // Creamos su ficha de alumno vacía y la vinculamos por id_usuario.
            // Así el estudiante nace SIEMPRE con un id de alumno nuevo, propio y
            // sin notas ni cursos: nadie más pudo haber apuntado calificaciones
            // o matrículas a este id todavía.
            // Nota: alumnos.correo es UNIQUE, así que no puede quedar vacío
            // para todos; usamos un placeholder único basado en el usuario.
            $correoPlaceholder = $usuario . '@pendiente.com';
            $stmtAlumno = $this->pdo->prepare(
                "INSERT INTO alumnos (id_usuario, nombre, apellido, correo, grado) VALUES (?, ?, '', ?, NULL)"
            );
            $stmtAlumno->execute([$id_usuario, $usuario, $correoPlaceholder]);

            $this->pdo->commit();
            return ['exito' => true, 'mensaje' => '¡Cuenta creada! Ya puedes iniciar sesión.'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['exito' => false, 'mensaje' => 'Error al registrar. Intenta de nuevo.'];
        }
    }
}
