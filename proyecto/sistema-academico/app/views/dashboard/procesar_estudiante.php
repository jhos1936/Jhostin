<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/EstudianteController.php";
require_once "../../controllers/MatriculaController.php";

$controller    = new EstudianteController();
$matriculaCtrl = new MatriculaController();

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($accion == 'eliminar' && $id > 0) {
        if ($controller->eliminar($id)) {
            header("Location: estudiantes.php");
            exit();
        } else {
            die("Error: No se pudo eliminar el estudiante.");
        }
    }

    if ($accion == 'crear' && $_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre   = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo   = trim($_POST['correo']);
        $grado    = trim($_POST['grado']);

        if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($grado)) {
            if ($controller->crear($nombre, $apellido, $correo, $grado)) {
                $nuevoId = $controller->ultimoId();
                if ($nuevoId) {
                    $matriculaCtrl->crear($nuevoId, $grado);
                }
                header("Location: estudiantes.php");
                exit();
            } else {
                die("Error: " . $controller->getError());
            }
        } else {
            die("Error: Todos los campos son obligatorios.");
        }
    }

    if ($accion == 'actualizar' && $_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
        $nombre   = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo   = trim($_POST['correo']);
        $grado    = trim($_POST['grado']);

        if (!empty($nombre) && !empty($apellido) && !empty($correo) && !empty($grado)) {
            if ($controller->actualizar($id, $nombre, $apellido, $correo, $grado)) {
                header("Location: estudiantes.php");
                exit();
            } else {
                die("Error: No se pudieron actualizar los datos.");
            }
        } else {
            die("Error: Todos los campos son obligatorios.");
        }
    }
} else {
    header("Location: estudiantes.php");
    exit();
}
