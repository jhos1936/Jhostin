<?php
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {

    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $usuario  = $_POST['usuario']  ?? '';
        $password = $_POST['password'] ?? '';

        $datos = $this->usuario->login($usuario);

        if ($datos && password_verify($password, $datos['password'])) {
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $datos['id_usuario'];
            $_SESSION['usuario']    = $datos['usuario'];
            $rol = strtolower(trim($datos['rol']));
            $_SESSION['rol'] = $rol;

            if ($rol === 'profesor') {
                require_once __DIR__ . '/../../config/Database.php';
                $db  = new Database();
                $pdo = $db->conectar();
                $stmt = $pdo->prepare("SELECT id_profesor FROM profesores WHERE nombre = ? LIMIT 1");
                $stmt->execute([$datos['usuario']]);
                $prof = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$prof) {
                    $stmt2 = $pdo->prepare("SELECT id_profesor FROM profesores ORDER BY id_profesor ASC LIMIT 1 OFFSET ?");
                    $offset = max(0, (int)$datos['id_usuario'] - 1);
                    $stmt2->bindValue(1, $offset, PDO::PARAM_INT);
                    $stmt2->execute();
                    $prof = $stmt2->fetch(PDO::FETCH_ASSOC);
                }
                $_SESSION['id_profesor'] = $prof ? $prof['id_profesor'] : null;
            }

            if ($rol === 'estudiante') {
                require_once __DIR__ . '/../../config/Database.php';
                $db  = new Database();
                $pdo = $db->conectar();
                $stmt = $pdo->prepare("SELECT id FROM alumnos WHERE id_usuario = ? LIMIT 1");
                $stmt->execute([$datos['id_usuario']]);
                $al = $stmt->fetch(PDO::FETCH_ASSOC);
                // Si por algún motivo el usuario no tiene alumno vinculado
                // (cuentas creadas antes de la migración), lo creamos ahora.
                if (!$al) {
                    $correoPlaceholder = $datos['usuario'] . '@pendiente.com';
                    $ins = $pdo->prepare(
                        "INSERT INTO alumnos (id_usuario, nombre, apellido, correo, grado) VALUES (?, ?, '', ?, NULL)"
                    );
                    $ins->execute([$datos['id_usuario'], $datos['usuario'], $correoPlaceholder]);
                    $_SESSION['id_estudiante'] = $pdo->lastInsertId();
                } else {
                    $_SESSION['id_estudiante'] = $al['id'];
                }
            }

            $rutas = [
                'administrador' => '/app/views/dashboard/admin.php',
                'profesor'      => '/app/views/dashboard/profesor.php',
                'estudiante'    => '/app/views/dashboard/estudiante.php'
            ];

            if (array_key_exists($rol, $rutas)) {
                header("Location: " . $rutas[$rol]);
                exit();
            } else {
                echo "Error: Rol no definido.";
            }
        } else {
            // Redirigir al login con error
            header("Location: /login.php?error=1");
            exit();
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: /login.php");
        exit();
    }
}
