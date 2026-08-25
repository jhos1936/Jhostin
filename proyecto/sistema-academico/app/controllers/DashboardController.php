<?php

require_once __DIR__ . "/Controller.php";

class DashboardController extends Controller {

    public function index(){

        session_start();

        if($_SESSION['rol'] == 'Administrador'){
            header("Location: ../views/dashboard/admin.php");
            exit();
        }

        if($_SESSION['rol'] == 'Profesor'){
            header("Location: ../views/dashboard/profesor.php");
            exit();
        }

        if($_SESSION['rol'] == 'Estudiante'){
            header("Location: ../views/dashboard/estudiante.php");
            exit();
        }
    }
}
?>