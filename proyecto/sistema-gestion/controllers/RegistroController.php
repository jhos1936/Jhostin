<?php
require_once __DIR__ . '/../models/Usuario.php';

class RegistroController {

    public function index() {
        require_once __DIR__ . '/../views/registro.php';
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre   = trim($_POST['nombre']   ?? '');
            $email    = trim($_POST['email']    ?? '');
            $password = $_POST['password']      ?? '';
            $confirmar = $_POST['confirmar']    ?? '';

            // Validaciones básicas
            if (empty($nombre) || empty($email) || empty($password)) {
                $error = "Por favor completa todos los campos.";
                require_once __DIR__ . '/../views/registro.php';
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El correo electrónico no es válido.";
                require_once __DIR__ . '/../views/registro.php';
                return;
            }

            if (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
                require_once __DIR__ . '/../views/registro.php';
                return;
            }

            if ($password !== $confirmar) {
                $error = "Las contraseñas no coinciden.";
                require_once __DIR__ . '/../views/registro.php';
                return;
            }

            $usuarioModel = new Usuario();
            $resultado = $usuarioModel->registrar($nombre, $email, $password);

            if ($resultado['exito']) {
                $exito = $resultado['mensaje'];
                require_once __DIR__ . '/../views/registro.php';
            } else {
                $error = $resultado['mensaje'];
                require_once __DIR__ . '/../views/registro.php';
            }
        } else {
            header('Location: index.php?controller=registro');
            exit;
        }
    }
}

