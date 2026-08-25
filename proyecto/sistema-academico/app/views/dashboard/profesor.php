<?php
session_start();
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Docente | Sistema Académico</title>
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
        .main-content { padding: 40px; min-width: 0; }
        .card-stat { background:white; border:none; border-radius:15px;
            box-shadow:0 5px 15px rgba(0,0,0,.05); padding:30px; text-align:center; transition:.3s; }
        .card-stat:hover { transform:translateY(-5px); box-shadow:0 10px 20px rgba(138,43,226,.2); }
        .card-stat i { font-size:2.5rem; color:#bc80ff; }
        .card-link { text-decoration:none; color:inherit; display:block; }
        .badge-count { background:#9d4edd; color:white; font-size:.9rem;
            padding:5px 10px; border-radius:20px; margin-top:5px; display:inline-block; }
        .card-highlight { border:2px solid #9d4edd; background:#faf5ff; }
    </style>
</head>
<body>
<div class="main-content">
    <div style="max-width:1100px; margin:0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2>Bienvenido, Profesor <?= htmlspecialchars($_SESSION['usuario'] ?? ''); ?></h2>
            <p class="text-muted mb-0">Panel de gestión académica. Ingresa notas por sección y unidad.</p>
        </div>
        <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Salir
        </a>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <a href="mis_secciones.php" class="card-link">
                <div class="card card-stat card-highlight">
                    <i class="bi bi-grid-3x2-gap"></i>
                    <h5 class="mt-3">Mis Secciones</h5>
                    <span class="badge-count">Ingresar notas por unidad</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="mis_cursos.php" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-book"></i>
                    <h5 class="mt-3">Mis Cursos</h5>
                    <span class="badge-count">Asignaturas asignadas</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="mis_estudiantes.php" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-people"></i>
                    <h5 class="mt-3">Mis Estudiantes</h5>
                    <span class="badge-count">Ver listado de alumnos</span>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="calificaciones.php" class="card-link">
                <div class="card card-stat">
                    <i class="bi bi-pencil-square"></i>
                    <h5 class="mt-3">Calificaciones</h5>
                    <span class="badge-count">Historial de notas</span>
                </div>
            </a>
        </div>
    </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
