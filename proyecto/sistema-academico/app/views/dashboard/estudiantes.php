<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'estudiante') {
        header("Location: estudiante.php");
    } else {
        header("Location: /index.php");
    }
    exit();
}

require_once "../../controllers/EstudianteController.php";
require_once "../../controllers/GradoController.php";

$controller = new EstudianteController();
$gradoCtrl  = new GradoController();

// AJAX: obtener datos para modal editar
if (isset($_GET['get_estudiante'])) {
    header('Content-Type: application/json');
    $e = $controller->obtenerPorId($_GET['get_estudiante']);
    echo json_encode($e ?: ['error' => 'No encontrado']);
    exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
    if ($controller->eliminar($_GET['eliminar'])) {
        header("Location: estudiantes.php?success=eliminado");
    } else {
        header("Location: estudiantes.php?error=" . urlencode($controller->getError()));
    }
    exit;
}

// POST: crear o editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion   = $_POST['accion']   ?? '';
    $nombre   = $_POST['nombre']   ?? '';
    $apellido = $_POST['apellido'] ?? '';
    $correo   = $_POST['correo']   ?? '';
    $grado    = $_POST['grado']    ?? '';

    if ($accion === 'crear') {
        if ($controller->crear($nombre, $apellido, $correo, $grado)) {
            header("Location: estudiantes.php?success=creado");
        } else {
            header("Location: estudiantes.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }

    if ($accion === 'editar') {
        $id = $_POST['id'] ?? '';
        if ($controller->actualizar($id, $nombre, $apellido, $correo, $grado)) {
            header("Location: estudiantes.php?success=editado");
        } else {
            header("Location: estudiantes.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }
}

$grados      = $gradoCtrl->listar();

// ── Filtro Grado/Sección y Nombre ──────────────────────────────────────
$id_grado_sel = isset($_GET['id_grado']) && $_GET['id_grado'] !== '' ? (int)$_GET['id_grado'] : null;
$nombre_sel   = isset($_GET['nombre']) && trim($_GET['nombre']) !== '' ? trim($_GET['nombre']) : null;

if ($id_grado_sel || $nombre_sel) {
    $estudiantes = $controller->listarPorGradoYNombre($id_grado_sel, $nombre_sel);
} else {
    $estudiantes = $controller->listar();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Estudiantes | Sistema Académico</title>
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
        .btn-action-edit { background-color: #9d4edd; color: white !important; border: none; font-weight: 500; padding: 6px 14px; border-radius: 8px; transition: 0.3s; box-shadow: 0 2px 5px rgba(157,78,221,0.2); }
        .btn-action-edit:hover { background-color: #7b2cbf; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(157,78,221,0.4); }
        .btn-action-delete { background-color: #e63946; color: white !important; border: none; font-weight: 500; padding: 6px 14px; border-radius: 8px; transition: 0.3s; box-shadow: 0 2px 5px rgba(230,57,70,0.2); }
        .btn-action-delete:hover { background-color: #bd1f2d; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(230,57,70,0.4); }
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }

        /* ── SELECTOR GRADO/SECCIÓN/NOMBRE ── */
        .selector-grado { background: white; border-radius: 12px; padding: 20px 26px;
            box-shadow: 0 4px 14px rgba(0,0,0,.05); margin-bottom: 28px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</span>
    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="admin.php"                class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="estudiantes.php"          class="nav-link active"><i class="bi bi-people me-1"></i>Estudiantes</a>
        <a href="profesores.php"           class="nav-link"><i class="bi bi-person-video3 me-1"></i>Profesores</a>
        <a href="usuarios.php"             class="nav-link"><i class="bi bi-person-gear me-1"></i>Usuarios</a>
        <a href="cursos.php"               class="nav-link"><i class="bi bi-book me-1"></i>Cursos</a>
        <a href="grados.php"               class="nav-link"><i class="bi bi-mortarboard me-1"></i>Grados</a>
        <a href="matriculas.php"           class="nav-link"><i class="bi bi-clipboard-check me-1"></i>Matrículas</a>
        <a href="calificaciones_admin.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>
    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<div class="main-content">

    <!-- ─── SELECTOR GRADO/SECCIÓN Y NOMBRE ─── -->
    <div class="selector-grado">
        <h5 class="mb-3" style="color:#0d0216; font-weight:700;">
            <i class="bi bi-grid-3x2-gap me-2" style="color:#9d4edd;"></i>
            Filtrar por Grado / Sección y Nombre
        </h5>
        <form method="GET" action="estudiantes.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Grado y Sección</label>
                <select name="id_grado" class="form-select">
                    <option value="">-- Todos --</option>
                    <?php foreach ($grados as $g): ?>
                    <option value="<?= $g['id_grado'] ?>"
                        <?= $id_grado_sel == $g['id_grado'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nombre_grado'] . ' — Sección ' . $g['seccion']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Nombre del Estudiante</label>
                <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre o apellido..."
                       value="<?= htmlspecialchars($nombre_sel ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-purple w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Ver
                </button>
            </div>
        </form>
    </div>

    <?php if ($id_grado_sel || $nombre_sel): ?>
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
        <span>
            <i class="bi bi-funnel me-1" style="color:#9d4edd;"></i>
            Mostrando estudiantes filtrados.
        </span>
        <a href="estudiantes.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Quitar filtro
        </a>
    </div>
    <?php endif; ?>

    <div class="crud-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color:#0d0216; font-weight:600;">Listado de Alumnos</h3>
            <button class="btn btn-purple px-4" data-bs-toggle="modal" data-bs-target="#modalNuevoEstudiante">
                <i class="bi bi-person-plus-fill me-2"></i>Registrar Estudiante
            </button>
        </div>

        <!-- Alertas -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                    $msgs = [
                        'creado'    => 'Estudiante registrado correctamente.',
                        'editado'   => 'Estudiante actualizado correctamente.',
                        'eliminado' => 'Estudiante eliminado correctamente.',
                    ];
                    echo $msgs[$_GET['success']] ?? 'Operación realizada correctamente.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Grado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($estudiantes)): ?>
                        <?php foreach ($estudiantes as $e): ?>
                        <tr>
                            <td><strong>#<?= $e['id'] ?></strong></td>
                            <td><?= htmlspecialchars($e['nombre']) ?></td>
                            <td><?= htmlspecialchars($e['apellido'] ?? '') ?></td>
                            <td><?= htmlspecialchars($e['correo']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($e['grado'] ?? 'No asignado') ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-action-edit me-1"
                                        onclick="abrirModalEditar(<?= $e['id'] ?>)">
                                    <i class="bi bi-pencil-square me-1"></i>Editar
                                </button>
                                <a href="?eliminar=<?= $e['id'] ?>"
                                   class="btn btn-sm btn-action-delete"
                                   onclick="return confirm('¿Eliminar este estudiante?')">
                                    <i class="bi bi-trash-fill me-1"></i>Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">
                            <?= ($id_grado_sel || $nombre_sel)
                                ? 'No hay estudiantes que coincidan con el filtro aplicado.'
                                : 'No hay estudiantes registrados.' ?>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: NUEVO ESTUDIANTE
════════════════════════════════════ -->
<div class="modal fade" id="modalNuevoEstudiante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">
            <form action="estudiantes.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Estudiante</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Carlos" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellido" class="form-control" placeholder="Ej. Mendoza" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" placeholder="carlos@colegio.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grado</label>
                        <select name="grado" class="form-select" required>
                            <option value="" disabled selected>Selecciona un grado...</option>
                            <?php foreach ($grados as $g): ?>
                                <option value="<?= $g['id_grado'] ?>"><?= htmlspecialchars($g['nombre_grado'] . ' ' . $g['seccion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">Guardar Estudiante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: EDITAR ESTUDIANTE
════════════════════════════════════ -->
<div class="modal fade" id="modalEditarEstudiante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">

            <!-- Spinner -->
            <div id="editSpinner" class="text-center py-5">
                <div class="spinner-border" style="color:#9d4edd;" role="status"></div>
                <p class="mt-2 text-muted small">Cargando datos...</p>
            </div>

            <!-- Formulario -->
            <form action="estudiantes.php" method="POST" id="formEditarEstudiante" style="display:none;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Estudiante</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" name="correo" id="edit_correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grado</label>
                        <select name="grado" id="edit_grado" class="form-select" required>
                            <option value="" disabled>Selecciona un grado...</option>
                            <?php foreach ($grados as $g): ?>
                                <option value="<?= $g['id_grado'] ?>"><?= htmlspecialchars($g['nombre_grado'] . ' ' . $g['seccion']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">Guardar Cambios</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function abrirModalEditar(id) {
        const modal   = new bootstrap.Modal(document.getElementById('modalEditarEstudiante'));
        const spinner = document.getElementById('editSpinner');
        const form    = document.getElementById('formEditarEstudiante');

        spinner.style.display = 'block';
        form.style.display    = 'none';
        modal.show();

        fetch('estudiantes.php?get_estudiante=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    modal.hide();
                    return;
                }
                document.getElementById('edit_id').value       = data.id;
                document.getElementById('edit_nombre').value   = data.nombre;
                document.getElementById('edit_apellido').value = data.apellido  ?? '';
                document.getElementById('edit_correo').value   = data.correo;
                document.getElementById('edit_grado').value    = data.grado     ?? '';

                spinner.style.display = 'none';
                form.style.display    = 'block';
            })
            .catch(() => {
                alert('No se pudo cargar el estudiante.');
                modal.hide();
            });
    }
</script>
</body>
</html>