<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action     = isset($_GET['action'])     ? $_GET['action']     : '';

if (empty($controller)) {
    require_once 'views/login.php';
    exit;
}

if ($controller == 'registro') {
    require_once 'controllers/RegistroController.php';
    $registroController = new RegistroController();
    if ($action == 'registrar') {
        $registroController->registrar();
    } else {
        $registroController->index();
    }
    exit;
}

if ($controller == 'recuperar') {
    require_once 'controllers/RecuperarController.php';
    $recuperarController = new RecuperarController();
    if ($action == 'enviar') {
        $recuperarController->enviar();
    } elseif ($action == 'restablecer') {
        $recuperarController->restablecer();
    } elseif ($action == 'actualizar') {
        $recuperarController->actualizar();
    } else {
        $recuperarController->index();
    }
    exit;
}

if ($controller == 'login') {
    require_once 'controllers/LoginController.php';
    $loginController = new LoginController();
    if ($action == 'autenticar') {
        $loginController->autenticar();
    } elseif ($action == 'logout') {
        $loginController->logout();
    } else {
        require_once 'views/login.php';
    }
    exit;
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$rol         = $_SESSION['usuario_rol'] ?? 'cliente';
$solo_admin  = ['eliminar'];
$admin_editor = ['crear', 'guardar', 'editar'];

if ($rol != 'admin' && in_array($action, $solo_admin)) {
    header('Location: index.php?controller=' . $controller . '&action=listar&msg=No tienes permiso para eliminar');
    exit;
}

if (($rol == 'usuario' || $rol == 'cliente') && in_array($action, $admin_editor)) {
    header('Location: index.php?controller=' . $controller . '&action=listar&msg=Solo puedes ver la informacion');
    exit;
}

switch ($controller) {
    case 'cliente':
        require_once 'controllers/ClienteController.php';
        $ctrl = new ClienteController();
        break;
    case 'proyecto':
        require_once 'controllers/ProyectoController.php';
        $ctrl = new ProyectoController();
        break;
    default:
        require_once 'controllers/ClienteController.php';
        $ctrl = new ClienteController();
        $action = 'listar';
}

switch ($action) {
    case 'listar':   $ctrl->listar();   break;
    case 'crear':    $ctrl->crear();    break;
    case 'guardar':  $ctrl->guardar();  break;
    case 'editar':   $ctrl->editar();   break;
    case 'eliminar': $ctrl->eliminar(); break;
    default:         $ctrl->listar();
}
?>