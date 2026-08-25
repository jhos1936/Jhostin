<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'administrador') {
    header("Location: /login.php");
    exit();
}

$root = dirname(__DIR__, 3) . '/';
require_once $root . "/middleware/Auth.php";
require_once $root . "/app/controllers/CalificacionController.php";
require_once $root . "/app/controllers/EstudianteController.php";
require_once $root . "/app/controllers/CursoController.php";
require_once $root . "/app/controllers/ProfesorController.php";

$controller     = new CalificacionController();
$calificaciones = $controller->listar();

$alumnoCtrl   = new EstudianteController();
$cursoCtrl    = new CursoController();
$profesorCtrl = new ProfesorController();

$alumnos    = $alumnoCtrl->listarMatriculados();
$cursos     = $cursoCtrl->listar();
$profesores = $profesorCtrl->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Calificaciones | Admin</title>
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
        .crud-card { background: white; border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; }
        .btn-purple { background: #9d4edd; color: white; border: none; transition: 0.3s; }
        .btn-purple:hover { background: #7b2cbf; color: white; }
        .btn-action-edit { background-color: #9d4edd; color: white !important; border: none; font-weight: 500; padding: 6px 14px; border-radius: 8px; transition: 0.3s; }
        .btn-action-edit:hover { background-color: #7b2cbf; transform: translateY(-1px); }
        .btn-action-delete { background-color: #e63946; color: white !important; border: none; font-weight: 500; padding: 6px 14px; border-radius: 8px; transition: 0.3s; text-decoration: none; }
        .btn-action-delete:hover { background-color: #bd1f2d; transform: translateY(-1px); }
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }

        /* ── Modal overlay ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 1050;
            align-items: center; justify-content: center;
        }
        .modal-overlay.show { display: flex; }

        /* ── Modal box ── */
        .modal-box {
            background: #fff; border-radius: 12px;
            width: 100%; max-width: 500px; overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            animation: modalIn .22s ease;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: translateY(18px) scale(.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1);   }
        }

        /* ── Modal header ── */
        .modal-header-custom {
            background: #1a0a2e; padding: 1.1rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .modal-header-custom h5 {
            color: #bc80ff; font-size: 1rem; font-weight: 700;
            margin: 0; display: flex; align-items: center; gap: .5rem;
        }
        .btn-close-modal {
            background: none; border: none; color: #ccc;
            font-size: 1.3rem; line-height: 1; cursor: pointer;
            padding: 0; transition: color .15s;
        }
        .btn-close-modal:hover { color: #fff; }

        /* ── Modal body ── */
        .modal-body-custom { padding: 1.6rem 1.5rem 1rem; }
        .modal-body-custom label { font-weight: 600; font-size: .9rem; margin-bottom: .4rem; display: block; color: #495057; }
        .modal-body-custom .form-select,
        .modal-body-custom .form-control {
            border-radius: 8px; border: 1px solid #d0d0d0;
            font-size: .9rem; padding: .55rem .9rem;
        }
        .modal-body-custom .form-select:focus,
        .modal-body-custom .form-control:focus {
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147,51,234,.15);
        }

        /* ── Modal footer ── */
        .modal-footer-custom {
            padding: 1rem 1.5rem 1.4rem;
            display: flex; justify-content: flex-end; gap: .75rem;
        }
        .btn-cancel-modal {
            background: none; border: 1.5px solid #ccc;
            border-radius: 8px; padding: .5rem 1.3rem;
            font-size: .9rem; color: #444; cursor: pointer; transition: border-color .15s;
        }
        .btn-cancel-modal:hover { border-color: #888; }
        .btn-save-modal {
            background: #9333ea; border: none; border-radius: 8px;
            padding: .5rem 1.5rem; font-size: .9rem; font-weight: 600;
            color: #fff; cursor: pointer; transition: background .15s;
        }
        .btn-save-modal:hover { background: #7e22ce; }

        /* ── Alert inside modal ── */
        .alert-modal {
            margin-bottom: 1rem; padding: .6rem .9rem; border-radius: 8px;
            font-size: .85rem; background: #fef2f2; color: #dc2626;
            border: 1px solid #fecaca; display: flex; align-items: center; gap: .5rem;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</span>
    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="admin.php"                class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="estudiantes.php"          class="nav-link"><i class="bi bi-people me-1"></i>Estudiantes</a>
        <a href="profesores.php"           class="nav-link"><i class="bi bi-person-video3 me-1"></i>Profesores</a>
        <a href="usuarios.php"             class="nav-link"><i class="bi bi-person-gear me-1"></i>Usuarios</a>
        <a href="cursos.php"               class="nav-link"><i class="bi bi-book me-1"></i>Cursos</a>
        <a href="grados.php"               class="nav-link"><i class="bi bi-mortarboard me-1"></i>Grados</a>
        <a href="matriculas.php"           class="nav-link"><i class="bi bi-clipboard-check me-1"></i>Matrículas</a>
        <a href="calificaciones_admin.php" class="nav-link active"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>
    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<!-- CONTENIDO -->
<div class="main-content">

    <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
        <div class="alert alert-success mt-3">Calificación actualizada correctamente.</div>
    <?php endif; ?>

    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color:#0d0216; font-weight:600;">Gestión Total de Calificaciones</h3>
            <button class="btn btn-purple px-4" onclick="openModal()">
                <i class="bi bi-plus-circle me-2"></i> Nueva Calificación
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="rounded-start">Estudiante</th>
                        <th>Curso</th>
                        <th>Profesor</th>
                        <th>Nota</th>
                        <th>Promedio</th>
                        <th class="text-center rounded-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($calificaciones)): ?>
                        <?php foreach ($calificaciones as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['nombre_alumno'] . ' ' . $fila['apellido_alumno']) ?></td>
                            <td><?= htmlspecialchars($fila['nombre_curso']) ?></td>
                            <td><?= htmlspecialchars($fila['nombre_profesor']) ?></td>
                            <td><?= htmlspecialchars($fila['nota_1']) ?></td>
                            <td><strong><?= htmlspecialchars($fila['promedio']) ?></strong></td>
                            <td class="text-center">
                                <a href="editar_calificacion.php?id=<?= $fila['id_calificacion'] ?>" class="btn btn-sm btn-action-edit me-2">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </a>
                                <a href="procesar_calificacion.php?accion=eliminar&id=<?= $fila['id_calificacion'] ?>"
                                   class="btn btn-sm btn-action-delete"
                                   onclick="return confirm('¿Estás seguro de eliminar esta calificación?')">
                                    <i class="bi bi-trash-fill me-1"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay calificaciones registradas en el sistema.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══════════ MODAL NUEVA CALIFICACIÓN ══════════ -->
<div class="modal-overlay" id="modalCalificacion">
    <div class="modal-box">

        <!-- Header -->
        <div class="modal-header-custom">
            <h5><i class="bi bi-plus-circle me-1"></i> Registrar Nueva Calificación</h5>
            <button class="btn-close-modal" onclick="closeModal()">✕</button>
        </div>

        <!-- Body -->
        <div class="modal-body-custom">

            <?php if (!empty($_GET['error'])): ?>
            <div class="alert-modal">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            </div>
            <?php endif; ?>

            <form action="procesar_calificacion.php?accion=crear" method="POST" id="formCalificacion">

                <div class="mb-3">
                    <label>Estudiante</label>
                    <select name="id_estudiante" class="form-select" required>
                        <option value="">Seleccione un alumno...</option>
                        <?php if (is_array($alumnos)) foreach ($alumnos as $a): ?>
                            <option value="<?= htmlspecialchars($a['id']) ?>">
                                <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Curso</label>
                    <select name="id_curso" class="form-select" required>
                        <option value="">Seleccione un curso...</option>
                        <?php if (is_array($cursos)) foreach ($cursos as $c): ?>
                            <option value="<?= htmlspecialchars($c['id_curso']) ?>">
                                <?= htmlspecialchars($c['nombre_curso']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Profesor</label>
                    <select name="id_profesor" class="form-select" required>
                        <option value="">Seleccione un profesor...</option>
                        <?php if (is_array($profesores)) foreach ($profesores as $p):
                            $id = $p['id_profesor'] ?? $p['id'];
                        ?>
                            <option value="<?= htmlspecialchars($id) ?>">
                                <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nota 1 (0–20)</label>
                    <input type="number" name="nota_1" class="form-control"
                           min="0" max="20" step="0.1" placeholder="Ej: 15.5" required>
                </div>

            </form>
        </div>

        <!-- Footer -->
        <div class="modal-footer-custom">
            <button type="button" class="btn-cancel-modal" onclick="closeModal()">Cancelar</button>
            <button type="submit" form="formCalificacion" class="btn-save-modal">
                <i class="bi bi-check-lg me-1"></i> Guardar
            </button>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openModal() {
        document.getElementById('modalCalificacion').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('modalCalificacion').classList.remove('show');
        document.body.style.overflow = '';
    }
    document.getElementById('modalCalificacion').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    <?php if (!empty($_GET['error'])): ?>
    openModal();
    <?php endif; ?>
</script>
</body>
</html>