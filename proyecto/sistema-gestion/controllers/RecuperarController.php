<?php
require_once __DIR__ . '/../models/Usuario.php';

class RecuperarController {

    // Muestra el formulario para pedir el correo
    public function index() {
        require_once __DIR__ . '/../views/recuperar.php';
    }

    // Procesa el correo, genera el token y "envía" el enlace de recuperación
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?controller=recuperar');
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Ingresa un correo electrónico válido.";
            require_once __DIR__ . '/../views/recuperar.php';
            return;
        }

        $usuarioModel = new Usuario();
        $token = $usuarioModel->generarTokenRecuperacion($email);

        // Por seguridad mostramos siempre el mismo mensaje, exista o no el correo
        $exito = "Si el correo está registrado, generamos un enlace de recuperación válido por 1 hora.";

        if ($token) {
            $enlace = $this->construirEnlace($token);

            // Intento de envío real por correo (requiere que el hosting tenga
            // configurado un servidor de correo/SMTP para que realmente llegue)
            $asunto    = "Recuperación de contraseña - TechSolutions";
            $mensaje   = "Hola,\n\nSolicitaste recuperar tu contraseña.\n"
                       . "Ingresa al siguiente enlace para crear una nueva (válido por 1 hora):\n\n"
                       . $enlace . "\n\nSi no solicitaste esto, ignora este mensaje.";
            $cabeceras = "From: no-reply@ttecnosolucion.com";

            if (function_exists('mail')) {
                @mail($email, $asunto, $mensaje, $cabeceras);
            }

            // Como no siempre hay un servidor de correo configurado en este entorno,
            // también mostramos el enlace directamente en pantalla para que la
            // recuperación funcione de inmediato. Si más adelante configuras un
            // SMTP real (ej. PHPMailer), puedes quitar esta variable y dejar
            // solo el envío por mail() de arriba.
            $enlaceDemo = $enlace;
        }

        require_once __DIR__ . '/../views/recuperar.php';
    }

    // Muestra el formulario para crear la nueva contraseña, validando el token
    public function restablecer() {
        $token = $_GET['token'] ?? '';

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->validarTokenRecuperacion($token);

        if (!$usuario) {
            $tokenInvalido = true;
        }

        require_once __DIR__ . '/../views/restablecer.php';
    }

    // Guarda la nueva contraseña
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?controller=recuperar');
            exit;
        }

        $token     = $_POST['token']     ?? '';
        $password  = $_POST['password']  ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->validarTokenRecuperacion($token);

        if (!$usuario) {
            $tokenInvalido = true;
            require_once __DIR__ . '/../views/restablecer.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
            require_once __DIR__ . '/../views/restablecer.php';
            return;
        }

        if ($password !== $confirmar) {
            $error = "Las contraseñas no coinciden.";
            require_once __DIR__ . '/../views/restablecer.php';
            return;
        }

        $usuarioModel->actualizarPassword($usuario['id'], $password);

        header('Location: index.php?msg=' . urlencode('Contraseña actualizada correctamente. Ya puedes iniciar sesión.'));
        exit;
    }

    private function construirEnlace($token) {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base      = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/');

        return $protocolo . '://' . $host . $base . '/index.php?controller=recuperar&action=restablecer&token=' . $token;
    }
}
