<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../../controllers/CalificacionController.php";
require_once "../../controllers/EstudianteController.php";
require_once "../../controllers/CursoController.php";
require_once "../../controllers/SeccionProfesorController.php";

if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php");
    exit();
}

// ── FIX: usar id_profesor (no id_usuario) ──────────────────────────────────
$id_profesor_sesion = $_SESSION['id_profesor'] ?? null;

if (!$id_profesor_sesion) {
    require_once dirname(__DIR__, 3) . '/config/Database.php';
    $db  = new Database();
    $pdo = $db->conectar();
    $stmt = $pdo->prepare("SELECT id_profesor FROM profesores WHERE nombre = ? LIMIT 1");
    $stmt->execute([$_SESSION['usuario']]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$prof) {
        $stmt2 = $pdo->prepare("SELECT id_profesor FROM profesores ORDER BY id_profesor ASC LIMIT 1 OFFSET ?");
        $offset = max(0, (int)($_SESSION['id_usuario'] ?? 1) - 1);
        $stmt2->bindValue(1, $offset, PDO::PARAM_INT);
        $stmt2->execute();
        $prof = $stmt2->fetch(PDO::FETCH_ASSOC);
    }
    if ($prof) {
        $_SESSION['id_profesor'] = $prof['id_profesor'];
        $id_profesor_sesion = $prof['id_profesor'];
    }
}

$calificacionCtrl  = new CalificacionController();
$estudianteCtrl    = new EstudianteController();
$cursoCtrl         = new CursoController();
$spCtrl            = new SeccionProfesorController();

$misCalificaciones = $calificacionCtrl->listarPorProfesor($id_profesor_sesion);

// ── Solo cursos y estudiantes que corresponden a lo asignado a este profesor ──
$estudiantes = $id_profesor_sesion ? $spCtrl->alumnosDelProfesor($id_profesor_sesion) : [];
$cursos      = $id_profesor_sesion ? $spCtrl->cursosDelProfesor($id_profesor_sesion) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificaciones | Panel Docente</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* ── TOP BAR ── */
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd; position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: 0.3s; font-size: 0.9rem; }
        .top-bar .nav-link:hover { background: #1a0b2e; color: #bc80ff; }
        .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }

        /* ── MAIN ── */
        .main-content { padding: 36px 40px; min-height: 100vh; }

        /* ── CARD ── */
        .crud-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.06);
            padding: 30px;
        }

        /* ── BOTONES ── */
        .btn-purple { background: #9d4edd; color: white; border: none; transition: 0.3s; }
        .btn-purple:hover { background: #7b2cbf; color: white; }
        .btn-action-edit {
            background-color: #9d4edd; color: white !important;
            border: none; font-weight: 500; padding: 5px 13px;
            border-radius: 8px; transition: 0.3s;
            text-decoration: none; font-size: .85rem;
        }
        .btn-action-edit:hover { background-color: #7b2cbf; transform: translateY(-1px); }

        /* ── TABLA ── */
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }

        /* ── BADGES ── */
        .badge-aprobado    { background:#d1fae5; color:#065f46; padding:4px 10px; border-radius:20px; font-weight:600; font-size:.82rem; }
        .badge-desaprobado { background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:20px; font-weight:600; font-size:.82rem; }

        /* ── ALERTA SESIÓN ── */
        .alert-session { background:#fff3cd; border:1px solid #ffc107; border-radius:10px; padding:11px 16px; margin-bottom:18px; font-size:.88rem; color:#856404; }
    </style>
</head>
<body>

<!-- ════ TOP BAR ════ -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>DOCENTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="profesor.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_secciones.php" class="nav-link"><i class="bi bi-grid-3x2-gap me-1"></i>Mis Secciones</a>
        <a href="mis_cursos.php" class="nav-link"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_estudiantes.php" class="nav-link"><i class="bi bi-people me-1"></i>Mis Estudiantes</a>
        <a href="calificaciones.php" class="nav-link active"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<!-- ════ CONTENIDO ════ -->
<div class="main-content">

    <?php if (!$id_profesor_sesion): ?>
    <div class="alert-session">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Atención:</strong> No se encontró tu perfil de profesor. Cierra sesión y vuelve a ingresar.
    </div>
    <?php endif; ?>

    <?php if ($id_profesor_sesion && empty($cursos)): ?>
    <div class="alert-session">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Todavía no tienes cursos asignados. Pide al administrador que te asigne una sección/curso.
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Nota registrada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['status'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Nota actualizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="m-0" style="color:#0d0216; font-weight:700;">Registro de Calificaciones</h3>
                <p class="text-muted mb-0" style="font-size:.9rem;">Profesor: <strong><?= htmlspecialchars($_SESSION['usuario'] ?? '') ?></strong></p>
            </div>
            <button class="btn btn-purple px-4" data-bs-toggle="modal" data-bs-target="#modalNota"
                <?= (!$id_profesor_sesion || empty($cursos)) ? 'disabled' : '' ?>>
                <i class="bi bi-plus-circle me-2"></i> Nueva Nota
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="rounded-start">Estudiante</th>
                        <th>Curso</th>
                        <th>Nota</th>
                        <th>Promedio</th>
                        <th class="text-center rounded-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($misCalificaciones)): ?>
                        <?php foreach ($misCalificaciones as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['nombre_alumno'] . ' ' . ($fila['apellido_alumno'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                            <td><?= htmlspecialchars($fila['nota_1']) ?></td>
                            <td>
                                <?php $prom = (int)$fila['promedio']; ?>
                                <span class="<?= $prom >= 11 ? 'badge-aprobado' : 'badge-desaprobado' ?>">
                                    <?= $prom ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="editar_calificacion.php?id=<?= $fila['id_calificacion'] ?>" class="btn-action-edit">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                No tiene calificaciones registradas actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ════ MODAL NUEVA NOTA ════ -->
<div class="modal fade" id="modalNota" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; overflow:hidden;">
            <div class="modal-header" style="background:#0d0216;">
                <h5 class="modal-title" style="color:#bc80ff;">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Nota
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="procesar_calificacion.php?accion=crear" method="POST">
                <div class="modal-body p-4">

                    <?php if (!empty($_GET['error'])): ?>
                    <div class="alert alert-danger py-2 px-3" style="font-size:.88rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <?= htmlspecialchars(urldecode($_GET['error'])) ?>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Estudiante</label>
                        <select name="id_estudiante" class="form-select" required>
                            <option value="" disabled selected>-- Selecciona un estudiante --</option>
                            <?php foreach ($estudiantes as $est): ?>
                                <option value="<?= $est['id'] ?>">
                                    <?= htmlspecialchars($est['nombre'] . ' ' . ($est['apellido'] ?? '')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Curso</label>
                        <select name="id_curso" class="form-select" required>
                            <option value="" disabled selected>-- Selecciona un curso --</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?= $curso['id_curso'] ?>">
                                    <?= htmlspecialchars($curso['nombre_curso']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nota <span class="text-muted">(0 – 20)</span></label>
                        <input type="number" name="nota_1" class="form-control"
                               step="0.1" min="0" max="20" placeholder="Ej. 15" required>
                    </div>

                    <!-- FIX: id_profesor real, no id_usuario -->
                    <input type="hidden" name="id_profesor" value="<?= (int)$id_profesor_sesion ?>">
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar Nota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if (!empty($_GET['error'])): ?>
    new bootstrap.Modal(document.getElementById('modalNota')).show();
    <?php endif; ?>
</script>
</body>
</html>