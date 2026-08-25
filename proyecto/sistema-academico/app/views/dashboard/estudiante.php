<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// 1. Protección de acceso
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'estudiante') {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: /index.php");
    }
    exit();
}

require_once "../../controllers/CalificacionController.php";

$calCtrl        = new CalificacionController();
$id_estudiante  = $_SESSION['id_estudiante'] ?? 0;
$calificaciones = $calCtrl->listarPorEstudiante($id_estudiante);

$totalNotas   = count($calificaciones);
$sumaNotas    = array_sum(array_column($calificaciones, 'promedio'));
$promedioGral = $totalNotas > 0 ? round($sumaNotas / $totalNotas, 2) : 0;
$cursosUnicos = count(array_unique(array_column($calificaciones, 'nombre_curso')));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Estudiante | Sistema Académico</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
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
        <div>
            <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario'] ?? ''); ?></h2>
            <p class="text-muted mb-0">Panel de seguimiento académico.</p>
        </div>
        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="mis_calificaciones_estudiante.php" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-bar-chart-fill"></i>
                    <h5 class="mt-3">Mis Calificaciones</h5>
                    <span class="badge-count"><?= $totalNotas; ?> Notas registradas</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="mis_cursos_estudiante.php" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <h5 class="mt-3">Mis Cursos</h5>
                    <span class="badge-count"><?= $cursosUnicos; ?> Cursos con nota</span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="#" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-trophy-fill"></i>
                    <h5 class="mt-3">Promedio General</h5>
                    <span class="badge-count"><?= $promedioGral; ?> pts</span>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>