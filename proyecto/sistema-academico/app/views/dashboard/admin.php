<?php
// SE QUITÓ session_start() DE AQUÍ PORQUE Auth.php YA LO TIENE EN SU LÍNEA 2
require_once "../../../middleware/Auth.php";

// Importamos todos los controladores necesarios
require_once "../../controllers/EstudianteController.php";
require_once "../../controllers/ProfesorController.php";
require_once "../../controllers/CursoController.php";
require_once "../../controllers/GradoController.php";
require_once "../../controllers/MatriculaController.php";
require_once "../../controllers/CalificacionController.php";

// Instanciamos los controladores
$estudianteCtrl = new EstudianteController();
$profesorCtrl = new ProfesorController();
$cursoCtrl = new CursoController();
$gradoCtrl = new GradoController();
$matriculaCtrl = new MatriculaController();
$calificacionCtrl = new CalificacionController();

// Obtenemos los conteos asegurando que devuelvan un array vacío si hay error
$totalEstudiantes = is_array($estudianteCtrl->listar()) ? count($estudianteCtrl->listar()) : 0;
$totalProfesores = is_array($profesorCtrl->listar()) ? count($profesorCtrl->listar()) : 0;
$totalCursos = is_array($cursoCtrl->listar()) ? count($cursoCtrl->listar()) : 0;
$totalGrados = is_array($gradoCtrl->listar()) ? count($gradoCtrl->listar()) : 0;
$totalMatriculas = is_array($matriculaCtrl->listar()) ? count($matriculaCtrl->listar()) : 0;
$totalCalificaciones = is_array($calificacionCtrl->listar()) ? count($calificacionCtrl->listar()) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd; position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: 0.3s; font-size: 0.9rem; }
        .top-bar .nav-link:hover { background: #1a0b2e; color: #bc80ff; }
        .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }
        .main-content { padding: 40px; max-width: 1100px; margin: 0 auto; }
        .card-stat { background: white; border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; text-align: center; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(138,43,226,0.2); }
        .card-stat i { font-size: 2.5rem; color: #bc80ff; }
        .card-link { text-decoration: none; color: inherit; display: block; }
        .badge-count { background-color: #9d4edd; color: white; font-size: 0.9rem; padding: 5px 10px; border-radius: 20px; margin-top: 5px; display: inline-block; }
    </style>
</head>
<body>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? 'Admin'); ?></h2>
        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4"><a href="estudiantes.php" class="card-link"><div class="card card-stat"><i class="bi bi-people"></i><h5 class="mt-3">Estudiantes</h5><span class="badge-count"><?= $totalEstudiantes; ?> Registrados</span></div></a></div>
        <div class="col-md-4"><a href="profesores.php" class="card-link"><div class="card card-stat"><i class="bi bi-person-video3"></i><h5 class="mt-3">Profesores</h5><span class="badge-count"><?= $totalProfesores; ?> Registrados</span></div></a></div>
        <div class="col-md-4"><a href="cursos.php" class="card-link"><div class="card card-stat"><i class="bi bi-book"></i><h5 class="mt-3">Cursos</h5><span class="badge-count"><?= $totalCursos; ?> Existentes</span></div></a></div>
        
        <div class="col-md-4"><a href="grados.php" class="card-link"><div class="card card-stat"><i class="bi bi-mortarboard"></i><h5 class="mt-3">Grados</h5><span class="badge-count"><?= $totalGrados; ?> Existentes</span></div></a></div>
        <div class="col-md-4"><a href="matriculas.php" class="card-link"><div class="card card-stat"><i class="bi bi-clipboard-check"></i><h5 class="mt-3">Matrículas</h5><span class="badge-count"><?= $totalMatriculas; ?> Registradas</span></div></a></div>
        
        <div class="col-md-4"><a href="calificaciones_admin.php" class="card-link"><div class="card card-stat"><i class="bi bi-pencil-square"></i><h5 class="mt-3">Calificaciones</h5><span class="badge-count"><?= $totalCalificaciones; ?> Registradas</span></div></a></div>
    </div>
</div>

</body>
</html>