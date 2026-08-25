<?php
require_once 'models/Proyecto.php';
require_once 'models/Cliente.php';

class ProyectoController {
    private $proyectoModel;
    private $clienteModel;
    public function __construct() {
        $this->proyectoModel = new Proyecto();
        $this->clienteModel  = new Cliente();
    }
    public function listar() {
        $rol        = $_SESSION['usuario_rol'] ?? 'cliente';
        $cliente_id = $_SESSION['cliente_id']  ?? null;

        if ($rol === 'cliente' && !empty($cliente_id)) {
            $proyectos = $this->proyectoModel->obtenerPorCliente($cliente_id);
        } else {
            $proyectos = $this->proyectoModel->obtenerTodos();
        }
        require_once 'views/proyectos.php';
    }
    public function crear() {
        header('Location: index.php?controller=proyecto&action=listar');
        exit;
    }
    public function guardar() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos = [
                'nombre'       => $_POST['nombre'],
                'descripcion'  => $_POST['descripcion'],
                'cliente_id'   => $_POST['cliente_id'],
                'estado'       => $_POST['estado'],
                'presupuesto'  => isset($_POST['presupuesto']) ? (float)$_POST['presupuesto'] : 0.00,
                'fecha_inicio' => !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d'),
                'fecha_fin'    => !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null
            ];
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $this->proyectoModel->actualizar($_POST['id'], $datos);
            } else {
                $this->proyectoModel->crear($datos);
            }
            header('Location: index.php?controller=proyecto&action=listar');
            exit;
        }
    }
    public function editar() {
        header('Location: index.php?controller=proyecto&action=listar');
        exit;
    }
    public function eliminar() {
        $id = $_GET['id'];
        $this->proyectoModel->eliminar($id);
        header('Location: index.php?controller=proyecto&action=listar');
        exit;
    }
}
