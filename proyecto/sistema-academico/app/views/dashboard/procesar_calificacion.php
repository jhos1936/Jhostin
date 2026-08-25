<?php
session_start();
$root = dirname(__DIR__, 3) . '/';
require_once $root . "/middleware/Auth.php";
require_once $root . "/app/controllers/CalificacionController.php";

$controller = new CalificacionController();
$accion = $_GET['accion'] ?? '';

$esAdmin = (isset($_SESSION['rol']) && strtolower($_SESSION['rol']) === 'administrador');
// FIX: profesores usan id_profesor, admin usa id_usuario
$id_usuario_sesion = ($_SESSION['rol'] ?? '') === 'profesor'
    ? ($_SESSION['id_profesor'] ?? 0)
    : ($_SESSION['id_usuario'] ?? 0);

$pagina_destino = $esAdmin ? "calificaciones_admin.php" : "calificaciones.php";
$pagina_crear   = "calificaciones_admin.php"; // modal crear está en calificaciones_admin
$pagina_editar  = "calificaciones_admin.php"; // modal editar está en calificaciones_admin

// --- ACCIÓN: CREAR ---
if ($accion == 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_SANITIZE_NUMBER_INT);
    $id_curso      = filter_input(INPUT_POST, 'id_curso',      FILTER_SANITIZE_NUMBER_INT);
    $id_profesor   = filter_input(INPUT_POST, 'id_profesor',   FILTER_SANITIZE_NUMBER_INT);
    $nota_1        = filter_input(INPUT_POST, 'nota_1',        FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($id_estudiante && $id_curso && $id_profesor && $nota_1 !== false) {
        if ($controller->crear($id_estudiante, $id_curso, $id_profesor, $nota_1)) {
            header("Location: " . $pagina_destino . "?status=success");
            exit();
        } else {
            header("Location: " . $pagina_crear . "?error=" . urlencode($controller->getError()));
            exit();
        }
    } else {
        header("Location: " . $pagina_crear . "?error=" . urlencode("Completa todos los campos obligatorios."));
        exit();
    }
}

// --- ACCIÓN: EDITAR ---
elseif ($accion == 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = filter_input(INPUT_GET,  'id',            FILTER_SANITIZE_NUMBER_INT)
                  ?: filter_input(INPUT_POST, 'id',            FILTER_SANITIZE_NUMBER_INT);
    $id_estudiante = filter_input(INPUT_POST, 'id_estudiante', FILTER_SANITIZE_NUMBER_INT);
    $id_curso      = filter_input(INPUT_POST, 'id_curso',      FILTER_SANITIZE_NUMBER_INT);
    $id_profesor   = filter_input(INPUT_POST, 'id_profesor',   FILTER_SANITIZE_NUMBER_INT);
    $nota_1        = filter_input(INPUT_POST, 'nota_1',        FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    if ($id && $id_estudiante && $id_curso && $id_profesor && $nota_1 !== false) {
        if ($controller->actualizar($id, $id_estudiante, $id_curso, $id_profesor, $nota_1, null, null, $esAdmin, $id_usuario_sesion)) {
            header("Location: " . $pagina_destino . "?status=updated");
            exit();
        } else {
            // Redirige al modal editar en calificaciones_admin
            header("Location: " . $pagina_editar . "?editar=" . $id . "&error=" . urlencode($controller->getError()));
            exit();
        }
    } else {
        header("Location: " . $pagina_editar . "?editar=" . ($id ?? '') . "&error=" . urlencode("Faltan datos obligatorios para actualizar."));
        exit();
    }
}

// --- ACCIÓN: ELIMINAR ---
elseif ($accion == 'eliminar') {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id && $controller->eliminar($id)) {
        header("Location: " . $pagina_destino . "?status=deleted");
        exit();
    } else {
        header("Location: " . $pagina_destino . "?error=" . urlencode("No se pudo eliminar la calificación."));
        exit();
    }
}

else {
    header("Location: " . $pagina_destino);
    exit();
}