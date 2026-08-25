<?php
session_start();
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'administrador') {
    header("Location: /login.php"); exit();
}

$root = dirname(__DIR__, 3) . '/';
require_once $root . 'middleware/Auth.php';
require_once $root . 'app/controllers/SeccionProfesorController.php';

$ctrl   = new SeccionProfesorController();
$accion = $_GET['accion'] ?? '';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_profesor  = filter_input(INPUT_POST, 'id_profesor',  FILTER_SANITIZE_NUMBER_INT);
    $id_grado     = filter_input(INPUT_POST, 'id_grado',     FILTER_SANITIZE_NUMBER_INT);
    $id_curso     = filter_input(INPUT_POST, 'id_curso',     FILTER_SANITIZE_NUMBER_INT);
    $anio_escolar = filter_input(INPUT_POST, 'anio_escolar', FILTER_SANITIZE_NUMBER_INT);

    if ($id_profesor && $id_grado && $id_curso && $anio_escolar) {
        if ($ctrl->crear($id_profesor, $id_grado, $id_curso, $anio_escolar)) {

            // Crear calificaciones vacías para todos los estudiantes del grado
            require_once $root . 'config/Database.php';
            $pdo = (new Database())->conectar();

            $stmt = $pdo->prepare("SELECT id_estudiante FROM matriculas WHERE id_grado = ?");
            $stmt->execute([$id_grado]);
            $estudiantes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $insCalif = $pdo->prepare("
                INSERT IGNORE INTO calificaciones (id_estudiante, id_curso, nota_1, nota_2, nota_3, promedio)
                VALUES (?, ?, 0, 0, 0, 0)
            ");
            foreach ($estudiantes as $id_est) {
                $insCalif->execute([$id_est, $id_curso]);
            }

            header("Location: secciones_admin.php?ok=creado"); exit();
        } else {
            header("Location: secciones_admin.php?err=" . urlencode($ctrl->getError())); exit();
        }
    } else {
        header("Location: secciones_admin.php?err=" . urlencode("Completa todos los campos.")); exit();
    }
}

elseif ($accion === 'eliminar') {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id && $ctrl->eliminar($id)) {
        header("Location: secciones_admin.php?ok=eliminado"); exit();
    } else {
        header("Location: secciones_admin.php?err=" . urlencode("No se pudo eliminar.")); exit();
    }
}

else {
    header("Location: secciones_admin.php"); exit();
}
