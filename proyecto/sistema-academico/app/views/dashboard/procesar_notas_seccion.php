<?php
session_start();
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php"); exit();
}

$root = dirname(__DIR__, 3) . '/';
require_once $root . 'middleware/Auth.php';
require_once $root . 'app/controllers/CalificacionController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mis_secciones.php"); exit();
}

$id_grado    = filter_input(INPUT_POST, 'id_grado',    FILTER_SANITIZE_NUMBER_INT);
$id_curso    = filter_input(INPUT_POST, 'id_curso',    FILTER_SANITIZE_NUMBER_INT);
$id_profesor = filter_input(INPUT_POST, 'id_profesor', FILTER_SANITIZE_NUMBER_INT);
$alumnos     = $_POST['alumnos'] ?? [];

if (!$id_grado || !$id_curso || !$id_profesor || empty($alumnos)) {
    header("Location: mis_secciones.php?id_grado=$id_grado&id_curso=$id_curso&error_guard=" . urlencode("Datos incompletos."));
    exit();
}

$ctrl   = new CalificacionController();
$errores = [];

foreach ($alumnos as $fila) {
    $id_est = isset($fila['id']) ? (int)$fila['id'] : 0;
    if (!$id_est) continue;

    $nota_1 = isset($fila['nota_1']) && $fila['nota_1'] !== '' ? (float)$fila['nota_1'] : null;
    $nota_2 = isset($fila['nota_2']) && $fila['nota_2'] !== '' ? (float)$fila['nota_2'] : null;
    $nota_3 = isset($fila['nota_3']) && $fila['nota_3'] !== '' ? (float)$fila['nota_3'] : null;

    // Solo guardar si al menos nota_1 tiene valor
    if ($nota_1 === null) continue;

    $ok = $ctrl->guardarPorSeccion($id_est, $id_curso, $id_profesor, $nota_1, $nota_2, $nota_3);
    if (!$ok) {
        $errores[] = $ctrl->getError();
    }
}

if (!empty($errores)) {
    $msg = implode(' | ', array_unique($errores));
    header("Location: mis_secciones.php?id_grado=$id_grado&id_curso=$id_curso&error_guard=" . urlencode($msg));
    exit();
}

header("Location: mis_secciones.php?id_grado=$id_grado&id_curso=$id_curso&guardado=1");
exit();
