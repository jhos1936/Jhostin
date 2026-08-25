<?php
require_once __DIR__ . '/../models/Usuario.php';

class LoginController {
    
    public function index() {
        require_once __DIR__ . '/../views/login.php';
    }
    
    public function autenticar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = $_POST['email']    ?? '';
            $password = $_POST['password'] ?? '';
            
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->validarLogin($email, $password);
            
            if ($usuario) {
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol']    = $usuario['rol'];
                $_SESSION['cliente_id']     = $usuario['cliente_id']; // null para admin/gerente
                
                header('Location: index.php?controller=cliente&action=listar');
                exit;
            } else {
                $error = "Credenciales inválidas";
                require_once __DIR__ . '/../views/login.php';
            }
        } else {
            header('Location: index.php');
            exit;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
