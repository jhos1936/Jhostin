<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php");
    exit();
}

require_once "../../controllers/SeccionProfesorController.php";

$spCtrl      = new SeccionProfesorController();
$id_profesor = $_SESSION['id_profesor'] ?? null;

$cursos = $id_profesor ? $spCtrl->listarPorProfesor($id_profesor) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos | Panel Docente</title>
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

        .crud-card {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
        }
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }
        .empty-state { text-align:center; padding:50px 20px; color:#aaa; }
        .empty-state i { font-size:3rem; display:block; margin-bottom:12px; }
    </style>
</head>
<body>

<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>DOCENTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="profesor.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_secciones.php" class="nav-link"><i class="bi bi-grid-3x2-gap me-1"></i>Mis Secciones</a>
        <a href="mis_cursos.php" class="nav-link active"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_estudiantes.php" class="nav-link"><i class="bi bi-people me-1"></i>Mis Estudiantes</a>
        <a href="calificaciones.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<div class="main-content">
    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color: #0d0216; font-weight: 600;">Mis Cursos Asignados</h3>
        </div>

        <?php if (!empty($cursos)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="rounded-start">Nombre del Curso</th>
                        <th>Grado</th>
                        <th class="rounded-end">Sección</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cursos as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nombre_curso']) ?></td>
                        <td><?= htmlspecialchars($c['nombre_grado']) ?></td>
                        <td><?= htmlspecialchars($c['seccion']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-journal-x text-secondary"></i>
            <p class="mb-0">Aún no tienes cursos asignados.</p>
            <small>Comunícate con el administrador para que te asigne una sección y curso.</small>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
