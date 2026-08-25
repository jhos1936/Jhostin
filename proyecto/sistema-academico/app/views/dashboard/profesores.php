<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'estudiante') {
        header("Location: estudiante.php");
    } else {
        header("Location: /login.php");
    }
    exit();
}

require_once "../../controllers/ProfesorController.php";
require_once "../../controllers/GradoController.php";

$controller = new ProfesorController();
$gradoCtrl  = new GradoController();

// AJAX: obtener datos para modal editar
if (isset($_GET['get_profesor'])) {
    header('Content-Type: application/json');
    $p = $controller->obtenerPorId($_GET['get_profesor']);
    echo json_encode($p ?: ['error' => 'No encontrado']);
    exit;
}

// Eliminar
if (isset($_GET['eliminar'])) {
    $controller->eliminar($_GET['eliminar']);
    header("Location: profesores.php");
    exit;
}

// POST: crear o editar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion      = $_POST['accion']      ?? '';
    $nombre      = $_POST['nombre']      ?? '';
    $apellido    = $_POST['apellido']    ?? '';
    $dni         = $_POST['dni']         ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    $id_usuario  = $_POST['id_usuario']  ?? null;
    $id_grado    = $_POST['id_grado']    ?? null;

    if ($accion === 'crear') {
        if ($controller->crear($nombre, $apellido, $dni, $especialidad, $id_usuario, $id_grado)) {
            header("Location: profesores.php?success=creado");
        } else {
            header("Location: profesores.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }

    if ($accion === 'editar') {
        $id = $_POST['id_profesor'] ?? '';
        if ($controller->actualizar($id, $nombre, $apellido, $dni, $especialidad)) {
            header("Location: profesores.php?success=editado");
        } else {
            header("Location: profesores.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }
}

$profesores = $controller->listar();
$usuariosDisponibles = $controller->listarUsuariosDisponibles();
$grados = $gradoCtrl->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Profesores | Sistema Académico</title>
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
    </style>
</head>
<body>

<!-- NAVBAR SUPERIOR -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="admin.php"                class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="estudiantes.php"          class="nav-link"><i class="bi bi-people me-1"></i>Estudiantes</a>
        <a href="profesores.php"           class="nav-link active"><i class="bi bi-person-video3 me-1"></i>Profesores</a>
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
    <div class="crud-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color:#0d0216; font-weight:600;">Listado de Profesores</h3>
            <button class="btn btn-purple px-4" data-bs-toggle="modal" data-bs-target="#modalNuevoProfesor">
                <i class="bi bi-person-plus-fill me-2"></i>Registrar Profesor
            </button>
        </div>

        <!-- Alertas -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_GET['success'] === 'creado' ? 'Profesor registrado correctamente.' : 'Profesor actualizado correctamente.' ?>
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
                        <th>Especialidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($profesores)): ?>
                        <?php foreach ($profesores as $p): ?>
                        <tr>
                            <td><strong>#<?= $p['id_profesor'] ?></strong></td>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= htmlspecialchars($p['apellido'] ?? '') ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['especialidad'] ?? 'No asignada') ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-action-edit me-1"
                                        onclick="abrirModalEditar(<?= $p['id_profesor'] ?>)">
                                    <i class="bi bi-pencil-square me-1"></i>Editar
                                </button>
                                <a href="?eliminar=<?= $p['id_profesor'] ?>"
                                   class="btn btn-sm btn-action-delete"
                                   onclick="return confirm('¿Seguro que quieres eliminar este profesor?')">
                                    <i class="bi bi-trash-fill me-1"></i>Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay profesores registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: NUEVO PROFESOR
════════════════════════════════════ -->
<div class="modal fade" id="modalNuevoProfesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">
            <form action="profesores.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Profesor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">

                    <!-- SELECTOR DE USUARIO -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Usuario vinculado</label>
                        <select name="id_usuario" id="nuevo_id_usuario" class="form-select" required>
                            <option value="">— Seleccionar usuario —</option>
                            <?php if (!empty($usuariosDisponibles)): ?>
                                <?php foreach ($usuariosDisponibles as $u): ?>
                                    <option value="<?= $u['id_usuario'] ?>"
                                            data-usuario="<?= htmlspecialchars($u['usuario']) ?>">
                                        <?= htmlspecialchars($u['usuario']) ?>
                                        <?php if (!empty($u['rol_nombre'])): ?>
                                            (<?= htmlspecialchars($u['rol_nombre']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay usuarios disponibles</option>
                            <?php endif; ?>
                        </select>
                        <div class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Se vinculará este usuario al nuevo registro de profesor.
                        </div>
                    </div>

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" name="nombre" id="nuevo_nombre" class="form-control bg-light text-muted"
                               placeholder="Se completará al elegir usuario" readonly required>
                    </div>

                    <!-- APELLIDO -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellido" id="nuevo_apellido" class="form-control bg-light text-muted"
                               placeholder="Se completará al elegir usuario" readonly>
                    </div>

                    <!-- ESPECIALIDAD -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" placeholder="Ej. Matemáticas">
                    </div>

                    <!-- GRADO -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grado asignado</label>
                        <select name="id_grado" class="form-select">
                            <option value="">— Sin grado por ahora —</option>
                            <?php foreach ($grados as $g): ?>
                                <option value="<?= $g['id_grado'] ?>">
                                    <?= htmlspecialchars($g['nombre_grado'] . ' — Sección ' . $g['seccion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">Guardar Profesor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: EDITAR PROFESOR
════════════════════════════════════ -->
<div class="modal fade" id="modalEditarProfesor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">

            <!-- Spinner -->
            <div id="editSpinner" class="text-center py-5">
                <div class="spinner-border" style="color:#9d4edd;" role="status"></div>
                <p class="mt-2 text-muted small">Cargando datos...</p>
            </div>

            <!-- Formulario -->
            <form action="profesores.php" method="POST" id="formEditarProfesor" style="display:none;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_profesor" id="edit_id_profesor">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Profesor</h5>
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
                        <label class="form-label fw-bold">Especialidad</label>
                        <input type="text" name="especialidad" id="edit_especialidad" class="form-control">
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
    // ── Modal Editar Profesor ──────────────────────────────────
    function abrirModalEditar(id) {
        const modal   = new bootstrap.Modal(document.getElementById('modalEditarProfesor'));
        const spinner = document.getElementById('editSpinner');
        const form    = document.getElementById('formEditarProfesor');

        spinner.style.display = 'block';
        form.style.display    = 'none';
        modal.show();

        fetch('profesores.php?get_profesor=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    modal.hide();
                    return;
                }
                document.getElementById('edit_id_profesor').value  = data.id_profesor;
                document.getElementById('edit_nombre').value       = data.nombre;
                document.getElementById('edit_apellido').value     = data.apellido     ?? '';
                document.getElementById('edit_especialidad').value = data.especialidad ?? '';

                spinner.style.display = 'none';
                form.style.display    = 'block';
            })
            .catch(() => {
                alert('No se pudo cargar el profesor.');
                modal.hide();
            });
    }

    // ── Modal Nuevo Profesor: nombre/apellido desde usuario (solo lectura) ──
    document.addEventListener('DOMContentLoaded', function () {
        const selectUsuario = document.getElementById('nuevo_id_usuario');
        const inputNombre   = document.getElementById('nuevo_nombre');
        const inputApellido = document.getElementById('nuevo_apellido');

        if (!selectUsuario) return;

        selectUsuario.addEventListener('change', function () {
            const option   = this.options[this.selectedIndex];
            const username = option.dataset.usuario || '';

            if (!username) {
                // Sin selección: limpiar y volver a placeholder
                inputNombre.value   = '';
                inputApellido.value = '';
                return;
            }

            // Separar "nombre.apellido", "nombre_apellido" o "nombre apellido"
            const partes = username.split(/[._\s]+/);
            if (partes.length >= 2) {
                inputNombre.value   = capitalizar(partes[0]);
                inputApellido.value = partes.slice(1).map(capitalizar).join(' ');
            } else {
                inputNombre.value   = capitalizar(username);
                inputApellido.value = '';
            }
        });

        function capitalizar(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }
    });
</script>
</body>
</html>