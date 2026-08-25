<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'administrador') {
    header("Location: /login.php"); exit();
}

$root = dirname(__DIR__, 3) . '/';
require_once $root . 'app/controllers/SeccionProfesorController.php';
require_once $root . 'app/controllers/ProfesorController.php';
require_once $root . 'app/controllers/GradoController.php';
require_once $root . 'app/controllers/CursoController.php';

$secCtrl  = new SeccionProfesorController();
$profCtrl = new ProfesorController();
$gradCtrl = new GradoController();
$curCtrl  = new CursoController();

$asignaciones = $secCtrl->listar();
$profesores   = $profCtrl->listar();
$grados       = $gradCtrl->listar();
$cursos       = $curCtrl->listar();

$anio_actual  = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Secciones por Profesor | Admin</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd;
            position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: .3s; font-size: .9rem; }
        .top-bar .nav-link:hover, .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }
        .main-content { padding: 36px 40px; max-width: 1150px; margin: 0 auto; }
        .crud-card { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,.05); padding: 30px; }
        .btn-purple { background: #9d4edd; color: white; border: none; }
        .btn-purple:hover { background: #7b2cbf; color: white; }
        .table th { background: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }
        .btn-del { background: #e63946; color: white; border: none; padding: 5px 12px;
            border-radius: 7px; font-size: .85rem; transition: .2s; }
        .btn-del:hover { background: #bd1f2d; color: white; }

        /* Modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);
            z-index:1050; align-items:center; justify-content:center; }
        .modal-overlay.show { display:flex; }
        .modal-box { background:#fff; border-radius:14px; width:100%; max-width:520px;
            overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.35);
            animation: mIn .2s ease; }
        @keyframes mIn { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:none} }
        .modal-hdr { background:#1a0a2e; padding:1rem 1.5rem;
            display:flex; align-items:center; justify-content:space-between; }
        .modal-hdr h5 { color:#bc80ff; margin:0; font-size:1rem; font-weight:700; }
        .btn-x { background:none; border:none; color:#ccc; font-size:1.3rem; cursor:pointer; }
        .btn-x:hover { color:#fff; }
        .modal-bdy { padding:1.5rem; }
        .modal-ftr { padding:.9rem 1.5rem 1.3rem; display:flex; justify-content:flex-end; gap:.75rem; }
        .btn-save { background:#9333ea; border:none; border-radius:8px; padding:.5rem 1.5rem;
            font-weight:600; color:#fff; cursor:pointer; }
        .btn-save:hover { background:#7e22ce; }
        .btn-cancel { background:none; border:1.5px solid #ccc; border-radius:8px;
            padding:.5rem 1.2rem; color:#444; cursor:pointer; }
        .btn-cancel:hover { border-color:#888; }
    </style>
</head>
<body>

<!-- NAVBAR ADMIN -->
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
        <a href="secciones_admin.php"      class="nav-link active"><i class="bi bi-grid-3x2-gap me-1"></i>Secciones</a>
        <a href="calificaciones_admin.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>
    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<div class="main-content">

    <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="bi bi-check-circle me-1"></i>
        <?= $_GET['ok'] === 'creado' ? 'Asignación creada correctamente.' : 'Asignación eliminada.' ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['err'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <?= htmlspecialchars(urldecode($_GET['err'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="m-0" style="color:#0d0216; font-weight:700;">
                    <i class="bi bi-grid-3x2-gap me-2" style="color:#9d4edd;"></i>
                    Secciones Asignadas por Profesor
                </h3>
                <p class="text-muted mb-0" style="font-size:.88rem;">
                    Asigna a cada profesor su grado, sección y curso a dictar.
                </p>
            </div>
            <button class="btn btn-purple px-4" onclick="abrirModal()">
                <i class="bi bi-plus-circle me-2"></i> Nueva Asignación
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="rounded-start">Profesor</th>
                        <th>Grado</th>
                        <th>Sección</th>
                        <th>Curso</th>
                        <th>Año</th>
                        <th class="text-center rounded-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($asignaciones)): ?>
                        <?php foreach ($asignaciones as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['nombre_profesor']) ?></strong></td>
                            <td><?= htmlspecialchars($a['nombre_grado']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($a['seccion']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($a['nombre_curso']) ?></td>
                            <td><?= htmlspecialchars($a['anio_escolar']) ?></td>
                            <td class="text-center">
                                <a href="procesar_seccion.php?accion=eliminar&id=<?= $a['id_asignacion'] ?>"
                                   class="btn-del"
                                   onclick="return confirm('¿Eliminar esta asignación?')">
                                    <i class="bi bi-trash-fill me-1"></i> Quitar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                No hay asignaciones registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ MODAL NUEVA ASIGNACIÓN ══ -->
<div class="modal-overlay" id="modalAsig">
    <div class="modal-box">
        <div class="modal-hdr">
            <h5><i class="bi bi-plus-circle me-2"></i>Nueva Asignación de Sección</h5>
            <button class="btn-x" onclick="cerrarModal()">✕</button>
        </div>
        <form method="POST" action="procesar_seccion.php?accion=crear" id="formAsig">
            <div class="modal-bdy">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Profesor</label>
                    <select name="id_profesor" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($profesores as $p): ?>
                        <option value="<?= $p['id_profesor'] ?>">
                            <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Grado y Sección</label>
                    <select name="id_grado" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($grados as $g): ?>
                        <option value="<?= $g['id_grado'] ?>">
                            <?= htmlspecialchars($g['nombre_grado'] . ' — Sección ' . $g['seccion']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Curso</label>
                    <select name="id_curso" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id_curso'] ?>">
                            <?= htmlspecialchars($c['nombre_curso']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Año Escolar</label>
                    <input type="number" name="anio_escolar" class="form-control"
                           value="<?= $anio_actual ?>" min="2020" max="2099" required>
                </div>
            </div>
            <div class="modal-ftr">
                <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg me-1"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function abrirModal()  { document.getElementById('modalAsig').classList.add('show'); document.body.style.overflow='hidden'; }
function cerrarModal() { document.getElementById('modalAsig').classList.remove('show'); document.body.style.overflow=''; }
document.getElementById('modalAsig').addEventListener('click', e => { if(e.target===document.getElementById('modalAsig')) cerrarModal(); });
document.addEventListener('keydown', e => { if(e.key==='Escape') cerrarModal(); });
<?php if (isset($_GET['err'])): ?> abrirModal(); <?php endif; ?>
</script>
</body>
</html>
