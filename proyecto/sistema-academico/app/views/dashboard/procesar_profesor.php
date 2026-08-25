<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/ProfesorController.php";

$controller = new ProfesorController();

// Validamos que venga una acción por la URL
if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // 1. ACCIÓN: ELIMINAR
    if ($accion == 'eliminar' && $id > 0) {
        if ($controller->eliminar($id)) {
            header("Location: profesores.php?status=success&msg=Eliminado");
        } else {
            header("Location: profesores.php?status=error&msg=No se pudo eliminar");
        }
        exit();
    }

    // 2. ACCIÓN: REGISTRAR (CREAR)
    if ($accion == 'crear' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre       = trim($_POST['nombre']);
        $apellido     = trim($_POST['apellido']);
        $dni          = trim($_POST['dni']);
        $especialidad = trim($_POST['especialidad']);

        if (!empty($nombre) && !empty($apellido) && !empty($dni) && !empty($especialidad)) {
            if ($controller->crear($nombre, $apellido, $dni, $especialidad)) {
                header("Location: profesores.php?status=success");
            } else {
                // Aquí capturamos el error real del controlador
                $errorMsg = urlencode($controller->getError());
                header("Location: profesores.php?status=error&msg=$errorMsg");
            }
        } else {
            header("Location: profesores.php?status=error&msg=CamposVacios");
        }
        exit();
    }

    // 3. ACCIÓN: ACTUALIZAR
    if ($accion == 'actualizar' && $_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
        $nombre       = trim($_POST['nombre']);
        $apellido     = trim($_POST['apellido']);
        $dni          = trim($_POST['dni']);
        $especialidad = trim($_POST['especialidad']);

        if (!empty($nombre) && !empty($apellido) && !empty($dni) && !empty($especialidad)) {
            if ($controller->actualizar($id, $nombre, $apellido, $dni, $especialidad)) {
                header("Location: profesores.php?status=success");
            } else {
                $errorMsg = urlencode($controller->getError());
                header("Location: profesores.php?status=error&msg=$errorMsg");
            }
        } else {
            header("Location: profesores.php?status=error&msg=CamposVacios");
        }
        exit();
    }
}

// Si llega aquí sin hacer nada, regresa al listado
header("Location: profesores.php");
exit();