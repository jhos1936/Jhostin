<?php
require_once 'models/Cliente.php';

class ClienteController {
    private $clienteModel;
    public function __construct() {
        $this->clienteModel = new Cliente();
    }
    public function listar() {
        $rol = $_SESSION['usuario_rol'] ?? 'cliente';
        if ($rol === 'cliente') {
            header('Location: index.php?controller=proyecto&action=listar');
            exit;
        }
        $clientes = $this->clienteModel->obtenerTodos();
        require_once 'views/clientes.php';
    }
    public function crear() {
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
    public function guardar() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'nombre'    => $_POST['nombre'],
                'email'     => $_POST['email'],
                'telefono'  => $_POST['telefono'],
                'direccion' => $_POST['direccion']
            ];
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $this->clienteModel->actualizar($_POST['id'], $datos);
            } else {
                $this->clienteModel->crear($datos);
            }
            header('Location: index.php?controller=cliente&action=listar');
            exit;
        }
    }
    public function editar() {
        header('Location: index.php?controller=cliente&action=listar');
        exit;
    }
    public function eliminar() {
        $id = $_GET['id'];
        $resultado = $this->clienteModel->eliminar($id);
        if ($resultado === false) {
            header('Location: index.php?controller=cliente&action=listar&error=eliminar_fallo');
        } else {
            header('Location: index.php?controller=cliente&action=listar&success=eliminado');
        }
        exit;
    }
}
?>