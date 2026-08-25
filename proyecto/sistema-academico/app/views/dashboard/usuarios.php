<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/UsuarioController.php";

$controller = new UsuarioController();

// Obtener datos para el modal de edición via AJAX
if (isset($_GET['get_usuario'])) {
    header('Content-Type: application/json');
    $u = $controller->obtenerPorId($_GET['get_usuario']);
    echo json_encode($u ?: ['error' => 'No encontrado']);
    exit;
}

// Manejar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $usuario  = $_POST['usuario']  ?? '';
        $password = $_POST['password'] ?? '';
        $id_rol   = $_POST['id_rol']   ?? '';
        if ($controller->crear($usuario, $password, $id_rol)) {
            header("Location: usuarios.php?success=creado");
        } else {
            header("Location: usuarios.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }

    if ($accion === 'editar') {
        $id_usuario = $_POST['id_usuario'] ?? '';
        $usuario    = $_POST['usuario']    ?? '';
        $password   = $_POST['password']   ?? '';
        $id_rol     = $_POST['id_rol']     ?? '';
        if ($controller->actualizar($id_usuario, $usuario, $password, $id_rol)) {
            header("Location: usuarios.php?success=editado");
        } else {
            header("Location: usuarios.php?error=" . urlencode($controller->getError()));
        }
        exit;
    }
}

// Eliminar por GET
if (isset($_GET['eliminar'])) {
    $controller->eliminar($_GET['eliminar']);
    header("Location: usuarios.php");
    exit;
}

$usuarios = $controller->listar();

// ── Filtro por nombre de usuario ──────────────────────────────────────
$nombre_sel = isset($_GET['nombre']) && trim($_GET['nombre']) !== '' ? trim($_GET['nombre']) : null;

if ($nombre_sel) {
    $usuarios = $controller->listarPorNombre($nombre_sel);
}

// Roles para los <select>
$db    = new Database();
$pdo   = $db->conectar();
$roles = $pdo->query("SELECT id_rol, nombre FROM roles ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }

        /* ── Navbar ── */
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd; position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: 0.3s; font-size: 0.9rem; }
        .top-bar .nav-link:hover { background: #1a0b2e; color: #bc80ff; }
        .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }

        /* ── Layout ── */
        .main-container { padding: 40px; max-width: 1100px; margin: 0 auto; }
        .crud-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

        /* ── Botón y tabla ── */
        .btn-purple { background: #9d4edd; color: white; border: none; transition: 0.3s; }
        .btn-purple:hover { background: #7b2cbf; color: white; }
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }

        /* ── SELECTOR FILTRO NOMBRE ── */
        .selector-grado { background: white; border-radius: 12px; padding: 20px 26px;
            box-shadow: 0 4px 14px rgba(0,0,0,.05); margin-bottom: 28px; }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="admin.php"                class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="estudiantes.php"          class="nav-link"><i class="bi bi-people me-1"></i>Estudiantes</a>
        <a href="profesores.php"           class="nav-link"><i class="bi bi-person-video3 me-1"></i>Profesores</a>
        <a href="usuarios.php"             class="nav-link active"><i class="bi bi-person-gear me-1"></i>Usuarios</a>
        <a href="cursos.php"               class="nav-link"><i class="bi bi-book me-1"></i>Cursos</a>
        <a href="grados.php"               class="nav-link"><i class="bi bi-mortarboard me-1"></i>Grados</a>
        <a href="matriculas.php"           class="nav-link"><i class="bi bi-clipboard-check me-1"></i>Matrículas</a>
        <a href="calificaciones_admin.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<!-- ── CONTENIDO ── -->
<div class="main-container">

    <!-- ─── SELECTOR FILTRO POR NOMBRE ─── -->
    <div class="selector-grado">
        <h5 class="mb-3" style="color:#0d0216; font-weight:700;">
            <i class="bi bi-grid-3x2-gap me-2" style="color:#9d4edd;"></i>
            Filtrar por Nombre de Usuario
        </h5>
        <form method="GET" action="usuarios.php" class="row g-3 align-items-end">
            <div class="col-md-10">
                <label class="form-label fw-semibold">Usuario</label>
                <input type="text" name="nombre" class="form-control" placeholder="Buscar por nombre de usuario..."
                       value="<?= htmlspecialchars($nombre_sel ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-purple w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Ver
                </button>
            </div>
        </form>
    </div>

    <?php if ($nombre_sel): ?>
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
        <span>
            <i class="bi bi-funnel me-1" style="color:#9d4edd;"></i>
            Mostrando usuarios filtrados.
        </span>
        <a href="usuarios.php" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Quitar filtro
        </a>
    </div>
    <?php endif; ?>

    <div class="crud-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color:#0d0216; font-weight:600;">Usuarios Registrados</h3>
            <button class="btn btn-purple px-4" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
            </button>
        </div>

        <!-- Alertas -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_GET['success'] === 'creado' ? 'Usuario creado correctamente.' : 'Usuario actualizado correctamente.' ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($u['id_usuario']) ?></strong></td>
                            <td><i class="bi bi-person-circle me-1 text-secondary"></i><?= htmlspecialchars($u['usuario']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($u['rol_nombre'] ?? '—') ?></span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary me-1"
                                        onclick="abrirModalEditar(<?= $u['id_usuario'] ?>)">
                                    <i class="bi bi-pencil-fill me-1"></i>Editar
                                </button>
                                <a href="?eliminar=<?= $u['id_usuario'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    <i class="bi bi-trash-fill me-1"></i>Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <?= $nombre_sel
                                    ? 'No hay usuarios que coincidan con el filtro aplicado.'
                                    : 'No hay usuarios registrados.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: NUEVO USUARIO
════════════════════════════════════ -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">
            <form action="usuarios.php" method="POST">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de Usuario</label>
                        <input type="text" name="usuario" class="form-control" placeholder="Ej: jperez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rol</label>
                        <select name="id_rol" class="form-select" required>
                            <option value="">Seleccione un rol...</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════
     MODAL: EDITAR USUARIO
════════════════════════════════════ -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px; border:none;">

            <!-- Spinner (visible mientras carga) -->
            <div id="editSpinner" class="text-center py-5">
                <div class="spinner-border" style="color:#9d4edd;" role="status"></div>
                <p class="mt-2 text-muted small">Cargando datos...</p>
            </div>

            <!-- Formulario (oculto hasta que cargue) -->
            <form action="usuarios.php" method="POST" id="formEditarUsuario" style="display:none;">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_usuario" id="edit_id_usuario">
                <div class="modal-header" style="background:#0d0216; color:#bc80ff; border-top-left-radius:14px; border-top-right-radius:14px;">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de Usuario</label>
                        <input type="text" name="usuario" id="edit_usuario" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Nueva Contraseña
                            <small class="text-muted fw-normal">(dejar vacío para no cambiar)</small>
                        </label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rol</label>
                        <select name="id_rol" id="edit_id_rol" class="form-select" required>
                            <option value="">Seleccione un rol...</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
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
        const modal   = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        const spinner = document.getElementById('editSpinner');
        const form    = document.getElementById('formEditarUsuario');

        // Resetear: mostrar spinner, ocultar form
        spinner.style.display = 'block';
        form.style.display    = 'none';
        modal.show();

        fetch('usuarios.php?get_usuario=' + id)
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    modal.hide();
                    return;
                }
                document.getElementById('edit_id_usuario').value = data.id_usuario;
                document.getElementById('edit_usuario').value    = data.usuario;
                document.getElementById('edit_id_rol').value     = data.id_rol;

                spinner.style.display = 'none';
                form.style.display    = 'block';
            })
            .catch(() => {
                alert('No se pudo cargar el usuario.');
                modal.hide();
            });
    }
</script>
</body>
</html>