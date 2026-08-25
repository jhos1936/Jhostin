<?php
require_once __DIR__ . '/../../controllers/MatriculaController.php';

$controller = new MatriculaController();
$accion = $_GET['accion'] ?? '';

if ($accion === 'crear') {
    $id_estudiante = $_POST['id_estudiante'] ?? null;
    $id_grado      = $_POST['id_grado']      ?? null;

    if ($id_estudiante && $id_grado) {
        if ($controller->yaMatriculado($id_estudiante)) {
            header("Location: crear_matricula.php?error=ya_matriculado");
            exit();
        }
        if ($controller->crear($id_estudiante, $id_grado)) {
            header("Location: matriculas.php");
            exit();
        } else {
            header("Location: crear_matricula.php?error=db_error");
            exit();
        }
    } else {
        header("Location: crear_matricula.php?error=faltan_datos");
        exit();
    }

} elseif ($accion === 'eliminar') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $controller->eliminar($id);
        header("Location: matriculas.php");
        exit();
    }
}
