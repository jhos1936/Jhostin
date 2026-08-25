<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'estudiante') {
    header("Location: /login.php");
    exit();
}

require_once "../../controllers/CalificacionController.php";

$calCtrl        = new CalificacionController();
$id_estudiante  = $_SESSION['id_estudiante'] ?? 0;
$calificaciones = $calCtrl->listarPorEstudiante($id_estudiante);

// Curso seleccionado (si aplica)
$cursoSeleccionado = isset($_GET['curso']) ? htmlspecialchars($_GET['curso']) : null;
$detalleActual     = null;

if ($cursoSeleccionado) {
    foreach ($calificaciones as $cal) {
        if ($cal['nombre_curso'] === $cursoSeleccionado) {
            $detalleActual = $cal;
            break;
        }
    }
}

// Agrupamos por curso (un alumno puede tener solo 1 registro por curso)
$cursosUnicos = [];
foreach ($calificaciones as $cal) {
    $nombre = $cal['nombre_curso'];
    if (!isset($cursosUnicos[$nombre])) {
        $cursosUnicos[$nombre] = $cal;
    }
}

$promedioGral = count($calificaciones) > 0
    ? round(array_sum(array_column($calificaciones, 'promedio')) / count($calificaciones), 2)
    : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos | Sistema Académico</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }

        /* ── Top bar ── */
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd; position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: 0.3s; font-size: 0.9rem; }
        .top-bar .nav-link:hover { background: #1a0b2e; color: #bc80ff; }
        .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }

        /* ── Main ── */
        .main-content { padding: 40px; }

        /* ── Page header ── */
        .page-header {
            background: linear-gradient(135deg, #0d0216 0%, #1a0b2e 100%);
            color: white; border-radius: 15px; padding: 25px 35px;
            margin-bottom: 30px; border: 1px solid #9d4edd;
        }
        .page-header h3 { color: #bc80ff; margin: 0; font-weight: 700; }

        /* ── Tarjetas de cursos ── */
        .course-card {
            background: white; border-radius: 15px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
            padding: 0; overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            cursor: pointer; text-decoration: none; color: inherit;
            display: block;
        }
        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(157,78,221,.2);
            text-decoration: none; color: inherit;
        }
        .course-card .card-top {
            background: linear-gradient(135deg, #0d0216 0%, #3a0a6e 100%);
            padding: 22px 22px 14px;
        }
        .course-card .card-top i { font-size: 2rem; color: #bc80ff; }
        .course-card .card-top h6 { color: white; margin: 8px 0 2px; font-weight: 700; font-size: 1rem; }
        .course-card .card-top small { color: #c0b0d0; font-size: .78rem; }
        .course-card .card-body-inner { padding: 18px 22px; }

        /* Barra de progreso nota */
        .nota-bar-wrap { margin: 6px 0 4px; }
        .nota-bar-bg { background: #f0e8ff; border-radius: 20px; height: 8px; overflow: hidden; }
        .nota-bar-fill { height: 8px; border-radius: 20px; transition: width .6s ease; }

        .badge-nota { font-size: .82rem; padding: 4px 14px; border-radius: 20px; font-weight: 700; }
        .nota-aprobado    { background: #d4edda; color: #155724; }
        .nota-regular     { background: #fff3cd; color: #856404; }
        .nota-desaprobado { background: #f8d7da; color: #721c24; }
        .nota-pend        { background: #e2e8f0; color: #64748b; }

        /* ── Panel detalle curso ── */
        .detail-panel {
            background: white; border-radius: 16px;
            box-shadow: 0 6px 24px rgba(0,0,0,.08);
            overflow: hidden; margin-top: 10px;
        }
        .detail-header {
            background: linear-gradient(135deg, #0d0216 0%, #3a0a6e 100%);
            padding: 24px 30px; color: white;
        }
        .detail-header h4 { color: #bc80ff; margin: 0; font-weight: 700; }

        /* Unidades en timeline */
        .unidad-timeline { padding: 30px; }
        .unidad-item {
            display: flex; align-items: flex-start; gap: 20px;
            padding: 16px 0; border-bottom: 1px solid #f0e8ff;
        }
        .unidad-item:last-child { border-bottom: none; }
        .unidad-num {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #9d4edd, #6a0dad);
            color: white; font-weight: 700; font-size: 1rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .unidad-num.pend { background: #e2e8f0; color: #64748b; }
        .unidad-body { flex: 1; }
        .unidad-body h6 { margin: 0 0 6px; font-weight: 700; color: #1a0b2e; }
        .unidad-nota-big {
            font-size: 2rem; font-weight: 800; line-height: 1;
            color: #9d4edd;
        }
        .unidad-nota-big.aprobado { color: #198754; }
        .unidad-nota-big.regular  { color: #856404; }
        .unidad-nota-big.desaprobado { color: #dc3545; }
        .unidad-nota-big.pend { color: #94a3b8; font-size: 1.4rem; }

        /* Promedio final card */
        .promedio-final-card {
            background: linear-gradient(135deg, #0d0216, #1a0b2e);
            border-radius: 14px; padding: 28px 32px;
            display: flex; align-items: center; gap: 24px;
            margin: 0 30px 30px;
        }
        .promedio-final-card .pf-icon { font-size: 2.6rem; color: #bc80ff; }
        .promedio-final-card h5 { color: #c0b0d0; margin: 0 0 4px; font-size: .9rem; }
        .promedio-final-card .pf-num {
            font-size: 3rem; font-weight: 900; color: #bc80ff; line-height: 1;
        }
        .promedio-final-card .pf-estado {
            margin-top: 6px; font-size: .9rem; font-weight: 700;
        }

        /* Back button */
        .btn-back {
            background: none; border: 2px solid #9d4edd; color: #9d4edd;
            border-radius: 8px; padding: 7px 18px; font-weight: 600;
            transition: .2s; text-decoration: none; display: inline-flex;
            align-items: center; gap: 6px;
        }
        .btn-back:hover { background: #9d4edd; color: white; }

        /* Empty state */
        .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
        .empty-state i { font-size: 3.5rem; color: #ddd; display: block; margin-bottom: 16px; }
    </style>
</head>
<body>

<!-- ═══ TOP BAR ═══ -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>ESTUDIANTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="estudiante.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_cursos_estudiante.php" class="nav-link active"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_calificaciones_estudiante.php" class="nav-link"><i class="bi bi-bar-chart-fill me-1"></i>Mis Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<!-- ═══ CONTENIDO PRINCIPAL ═══ -->
<div class="main-content">

    <?php if ($detalleActual): ?>
    <!-- ═══ VISTA DETALLE DE UN CURSO ═══ -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="mis_cursos_estudiante.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <div>
            <h4 class="mb-0" style="color:#0d0216; font-weight:700;">
                <?= htmlspecialchars($detalleActual['nombre_curso']) ?>
            </h4>
            <small class="text-muted">Detalle de notas por unidad</small>
        </div>
    </div>

    <div class="detail-panel">
        <div class="detail-header">
            <h4><i class="bi bi-journal-text me-2"></i><?= htmlspecialchars($detalleActual['nombre_curso']) ?></h4>
            <small style="color:#c0b0d0;">
                Profesor: <?= htmlspecialchars($detalleActual['nombre_profesor'] ?? '—') ?>
                &nbsp;·&nbsp;
                Fecha de registro: <?= htmlspecialchars($detalleActual['fecha_registro'] ?? '—') ?>
            </small>
        </div>

        <!-- Unidades -->
        <div class="unidad-timeline">
            <?php
            $unidades = [
                ['label' => 'Unidad 1', 'nota' => $detalleActual['nota_1']],
                ['label' => 'Unidad 2', 'nota' => $detalleActual['nota_2']],
                ['label' => 'Unidad 3', 'nota' => $detalleActual['nota_3']],
            ];
            foreach ($unidades as $idx => $u):
                $nota    = $u['nota'];
                $tienNota = ($nota !== null && $nota !== '');
                $notaNum  = $tienNota ? (float)$nota : null;
                $numClass = '';
                $estado   = 'Sin registrar';
                $barColor = '#e2e8f0';
                $barWidth = 0;
                if ($tienNota) {
                    $barWidth = min(100, round($notaNum * 5));
                    if ($notaNum >= 14) {
                        $numClass = 'aprobado'; $estado = 'Aprobado'; $barColor = '#198754';
                    } elseif ($notaNum >= 11) {
                        $numClass = 'regular';  $estado = 'Regular';  $barColor = '#ffc107';
                    } else {
                        $numClass = 'desaprobado'; $estado = 'Desaprobado'; $barColor = '#dc3545';
                    }
                }
            ?>
            <div class="unidad-item">
                <div class="unidad-num <?= $tienNota ? '' : 'pend' ?>">
                    <?= $idx + 1 ?>
                </div>
                <div class="unidad-body">
                    <h6><?= htmlspecialchars($u['label']) ?></h6>
                    <?php if ($tienNota): ?>
                        <div class="unidad-nota-big <?= $numClass ?>"><?= number_format($notaNum, 1) ?></div>
                        <div class="nota-bar-wrap mt-2">
                            <div class="nota-bar-bg">
                                <div class="nota-bar-fill" style="width:<?= $barWidth ?>%; background:<?= $barColor ?>;"></div>
                            </div>
                        </div>
                        <span class="badge-nota nota-<?= $numClass ?> mt-1 d-inline-block"><?= $estado ?></span>
                    <?php else: ?>
                        <div class="unidad-nota-big pend">—</div>
                        <span class="badge-nota nota-pend mt-1 d-inline-block">Sin registrar</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Promedio Final -->
        <?php
        $prom      = (float)$detalleActual['promedio'];
        $pfClass   = $prom >= 14 ? 'aprobado' : ($prom >= 11 ? 'regular' : 'desaprobado');
        $pfEstado  = $prom >= 14 ? '✓ Aprobado' : ($prom >= 11 ? '⚠ Regular' : '✗ Desaprobado');
        $pfColor   = $prom >= 14 ? '#4cbb84' : ($prom >= 11 ? '#f0c040' : '#e05555');
        ?>
        <div class="promedio-final-card">
            <div class="pf-icon"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <h5>Promedio Final</h5>
                <div class="pf-num"><?= $prom ?></div>
                <div class="pf-estado" style="color:<?= $pfColor ?>;"><?= $pfEstado ?></div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- ═══ VISTA LISTA DE CURSOS ═══ -->
    <div class="page-header">
        <h3><i class="bi bi-journal-bookmark-fill me-2"></i>Mis Cursos</h3>
        <p class="mb-0" style="color:#c0b0d0; font-size:.9rem;">
            Haz clic en un curso para ver tus notas por unidad y tu promedio final.
        </p>
    </div>

    <?php if (!empty($cursosUnicos)): ?>
    <div class="row g-4">
        <?php foreach ($cursosUnicos as $nombre => $cal):
            $prom   = (float)$cal['promedio'];
            $cls    = $prom >= 14 ? 'aprobado' : ($prom >= 11 ? 'regular' : 'desaprobado');
            $estado = $prom >= 14 ? 'Aprobado' : ($prom >= 11 ? 'Regular' : 'Desaprobado');
            $barW   = min(100, round($prom * 5));
            $barColor = $prom >= 14 ? '#198754' : ($prom >= 11 ? '#ffc107' : '#dc3545');

            $unidadesCompletas = 0;
            foreach (['nota_1','nota_2','nota_3'] as $k) {
                if ($cal[$k] !== null && $cal[$k] !== '') $unidadesCompletas++;
            }
        ?>
        <div class="col-md-4 col-sm-6">
            <a href="mis_cursos_estudiante.php?curso=<?= urlencode($nombre) ?>" class="course-card">
                <div class="card-top">
                    <i class="bi bi-journal-text"></i>
                    <h6><?= htmlspecialchars($nombre) ?></h6>
                    <small><i class="bi bi-person-fill me-1"></i><?= htmlspecialchars($cal['nombre_profesor'] ?? '—') ?></small>
                </div>
                <div class="card-body-inner">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted" style="font-size:.83rem;">
                            <i class="bi bi-layers-fill me-1"></i><?= $unidadesCompletas ?>/3 unidades
                        </span>
                        <span class="badge-nota nota-<?= $cls ?>"><?= $estado ?></span>
                    </div>
                    <div class="nota-bar-wrap">
                        <div class="nota-bar-bg">
                            <div class="nota-bar-fill" style="width:<?= $barW ?>%; background:<?= $barColor ?>;"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">Promedio</small>
                        <strong style="color:#1a0b2e; font-size:1.1rem;"><?= $prom ?></strong>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Resumen general -->
    <div class="mt-4 p-4 rounded-3" style="background:white; box-shadow:0 3px 10px rgba(0,0,0,.05); border-left:5px solid #9d4edd;">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-star-fill fs-3" style="color:#9d4edd;"></i>
            <div>
                <div class="text-muted" style="font-size:.85rem;">Promedio general de todos los cursos</div>
                <strong style="font-size:1.6rem; color:#0d0216;"><?= $promedioGral ?></strong>
                <span class="badge-nota ms-2
                    <?= $promedioGral >= 14 ? 'nota-aprobado' : ($promedioGral >= 11 ? 'nota-regular' : 'nota-desaprobado') ?>">
                    <?= $promedioGral >= 14 ? 'Aprobado' : ($promedioGral >= 11 ? 'Regular' : 'Desaprobado') ?>
                </span>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <i class="bi bi-journal-x"></i>
        <p class="fs-5 fw-semibold mb-1">No tienes cursos con notas aún</p>
        <small>Cuando tu profesor registre tus calificaciones, aparecerán aquí.</small>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
