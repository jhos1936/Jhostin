<?php
session_start();
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'estudiante') {
    header("Location: /login.php"); exit();
}

require_once "../../controllers/CalificacionController.php";

$calCtrl        = new CalificacionController();
$id_estudiante  = $_SESSION['id_estudiante'] ?? 0;
$calificaciones = $calCtrl->listarPorEstudiante($id_estudiante);

$totalNotas   = count($calificaciones);
$sumaNotas    = array_sum(array_column($calificaciones, 'promedio'));
$promedioGral = $totalNotas > 0 ? round($sumaNotas / $totalNotas, 2) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Calificaciones | Sistema Académico</title>
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
        .main-content { padding:40px; }
        .page-header { background:linear-gradient(135deg,#0d0216 0%,#1a0b2e 100%);
            color:white; border-radius:15px; padding:25px 35px; margin-bottom:30px; border:1px solid #9d4edd; }
        .page-header h3 { color:#bc80ff; margin:0; font-weight:700; }
        .resumen-card { background:white; border-radius:12px; padding:20px 25px;
            box-shadow:0 3px 10px rgba(0,0,0,.05); border-left:5px solid #9d4edd;
            display:flex; align-items:center; gap:20px; }
        .resumen-card i { font-size:2rem; color:#9d4edd; }
        .resumen-card h4 { margin:0; font-weight:700; color:#0d0216; }
        .section-card { background:white; border-radius:15px; box-shadow:0 4px 12px rgba(0,0,0,.05); overflow:hidden; }
        .section-header { background:#0d0216; color:#bc80ff; padding:18px 25px; font-weight:600; }
        .table th { background:#f8f3ff; color:#5a3d8a; font-weight:600; border:none; }
        .table td { vertical-align:middle; color:#444; }
        .badge-nota { font-size:.88rem; padding:4px 12px; border-radius:20px; font-weight:600; }
        .nota-aprobado    { background:#d4edda; color:#155724; }
        .nota-regular     { background:#fff3cd; color:#856404; }
        .nota-desaprobado { background:#f8d7da; color:#721c24; }
        .nota-pend        { background:#e2e8f0; color:#64748b; }
        .promedio-badge { display:inline-block; background:#9d4edd; color:white;
            border-radius:50px; padding:6px 20px; font-weight:700; font-size:1.1rem; }
    </style>
</head>
<body>
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>ESTUDIANTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="estudiante.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_cursos_estudiante.php" class="nav-link"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_calificaciones_estudiante.php" class="nav-link active"><i class="bi bi-bar-chart-fill me-1"></i>Mis Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<div class="main-content">
    <div class="page-header">
        <h3><i class="bi bi-bar-chart-fill me-2"></i>Mis Calificaciones</h3>
        <p class="mb-0" style="color:#c0b0d0; font-size:.9rem;">
            Historial de notas por unidades y promedios registrados por tus profesores.
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="resumen-card">
                <i class="bi bi-clipboard2-check-fill"></i>
                <div><h4><?= $totalNotas ?></h4><small>Calificaciones registradas</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="resumen-card" style="border-left-color:#198754;">
                <i class="bi bi-trophy-fill" style="color:#198754;"></i>
                <div><h4><?= $promedioGral ?></h4><small>Promedio general</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="resumen-card" style="border-left-color:#0dcaf0;">
                <i class="bi bi-journal-bookmark-fill" style="color:#0dcaf0;"></i>
                <div><h4><?= count(array_unique(array_column($calificaciones, 'nombre_curso'))) ?></h4>
                    <small>Cursos con nota</small></div>
            </div>
        </div>
    </div>

    <div class="section-card">
        <div class="section-header d-flex align-items-center gap-2">
            <i class="bi bi-list-stars"></i> Detalle de calificaciones por unidad
        </div>
        <div class="p-4">
            <?php if (!empty($calificaciones)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Curso</th>
                            <th class="text-center">Unidad 1</th>
                            <th class="text-center">Unidad 2</th>
                            <th class="text-center">Unidad 3</th>
                            <th class="text-center">Promedio</th>
                            <th class="text-center">Estado</th>
                            <th>Profesor</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($calificaciones as $i => $cal): ?>
                    <?php
                        $prom   = (float)$cal['promedio'];
                        $cls    = $prom >= 14 ? 'nota-aprobado' : ($prom >= 11 ? 'nota-regular' : 'nota-desaprobado');
                        $estado = $prom >= 14 ? 'Aprobado' : ($prom >= 11 ? 'Regular' : 'Desaprobado');
                        $fmtNota = fn($v) => $v !== null && $v !== '' ? htmlspecialchars($v) : '<span class="badge-nota nota-pend">—</span>';
                    ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($cal['nombre_curso']) ?></strong></td>
                        <td class="text-center"><?= $fmtNota($cal['nota_1']) ?></td>
                        <td class="text-center"><?= $fmtNota($cal['nota_2']) ?></td>
                        <td class="text-center"><?= $fmtNota($cal['nota_3']) ?></td>
                        <td class="text-center">
                            <span class="badge-nota <?= $cls ?>"><?= $cal['promedio'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge-nota <?= $cls ?>"><?= $estado ?></span>
                        </td>
                        <td><?= htmlspecialchars($cal['nombre_profesor'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <span class="text-muted me-2">Promedio general:</span>
                <span class="promedio-badge"><?= $promedioGral ?></span>
            </div>
            <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3" style="color:#ddd;"></i>
                <p>Aún no tienes calificaciones registradas.</p>
                <small>Comunícate con tu profesor si crees que esto es un error.</small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
