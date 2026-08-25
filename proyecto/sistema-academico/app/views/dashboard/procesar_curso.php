<?php
require_once dirname(__DIR__, 3) . '/middleware/Auth.php';
require_once dirname(__DIR__, 3) . '/app/controllers/CursoController.php';

$controller = new CursoController();

if (isset($_GET['accion']) && $_GET['accion'] == 'crear' && $_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');

    if (!empty($nombre)) {
        if ($controller->crear($nombre)) {
            header("Location: cursos.php?status=success&msg=Guardado");
        } else {
            header("Location: cursos.php?status=error&msg=Error BD");
        }
    } else {
        header("Location: cursos.php?status=error&msg=Nombre vacio");
    }
    exit();
}