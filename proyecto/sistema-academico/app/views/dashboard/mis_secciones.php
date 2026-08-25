<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php"); exit();
}

$root = dirname(__DIR__, 3) . '/';
require_once $root . 'config/Database.php';
require_once $root . 'app/controllers/SeccionProfesorController.php';
require_once $root . 'app/controllers/CalificacionController.php';

// ── Resolver id_profesor desde sesión ─────────────────────────────────────
$id_profesor = $_SESSION['id_profesor'] ?? null;
if (!$id_profesor) {
    $db   = new Database();
    $pdo  = $db->conectar();
    $stmt = $pdo->prepare("SELECT id_profesor FROM profesores WHERE nombre = ? LIMIT 1");
    $stmt->execute([$_SESSION['usuario']]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($prof) {
        $id_profesor = $prof['id_profesor'];
        $_SESSION['id_profesor'] = $id_profesor;
    }
}

$seccionCtrl  = new SeccionProfesorController();
$califCtrl    = new CalificacionController();

// Secciones asignadas al profesor
$misAsignaciones = $seccionCtrl->listarPorProfesor($id_profesor);

// Parámetros de filtro
$id_grado_sel  = isset($_GET['id_grado'])  ? (int)$_GET['id_grado']  : null;
$id_curso_sel  = isset($_GET['id_curso'])  ? (int)$_GET['id_curso']  : null;

$alumnos        = [];
$nombre_grado   = '';
$seccion_sel    = '';
$nombre_curso   = '';

if ($id_grado_sel && $id_curso_sel) {
    $alumnos = $califCtrl->listarPorSeccionYCurso($id_grado_sel, $id_curso_sel, $id_profesor);
    // Buscar nombres
    foreach ($misAsignaciones as $a) {
        if ($a['id_grado'] == $id_grado_sel && $a['id_curso'] == $id_curso_sel) {
            $nombre_grado = $a['nombre_grado'];
            $seccion_sel  = $a['seccion'];
            $nombre_curso = $a['nombre_curso'];
            break;
        }
    }
}

// Agrupar asignaciones por grado para el selector
$gradosCursos = [];
foreach ($misAsignaciones as $a) {
    $key = $a['id_grado'];
    if (!isset($gradosCursos[$key])) {
        $gradosCursos[$key] = [
            'id_grado'     => $a['id_grado'],
            'nombre_grado' => $a['nombre_grado'],
            'seccion'      => $a['seccion'],
            'cursos'       => []
        ];
    }
    $gradosCursos[$key]['cursos'][] = [
        'id_curso'     => $a['id_curso'],
        'nombre_curso' => $a['nombre_curso'],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Secciones | Panel Docente</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* ── TOP BAR ── */
        .top-bar { background: #0d0216; color: white; padding: 12px 30px; border-bottom: 2px solid #9d4edd; position: sticky; top: 0; z-index: 1000; }
        .top-bar .nav-link { color: #d1d1d1; padding: 6px 14px; border-radius: 6px; transition: 0.3s; font-size: 0.9rem; }
        .top-bar .nav-link:hover { background: #1a0b2e; color: #bc80ff; }
        .top-bar .nav-link.active { background: #1a0b2e; color: #bc80ff; }
        .brand-title { color: #bc80ff; font-weight: bold; font-size: 1.1rem; white-space: nowrap; }

        /* ── MAIN ── */
        .main-content { padding: 36px 40px; min-height: 100vh; }

        /* ── CARDS ── */
        .card-seccion {
            background: white; border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06); padding: 22px 26px;
            transition: .25s; border: 2px solid transparent;
        }
        .card-seccion:hover { border-color: #9d4edd; transform: translateY(-2px); }
        .card-seccion.selected { border-color: #9d4edd; background: #faf5ff; }

        .crud-card {
            background: white; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,.06); padding: 30px;
        }

        /* ── TABLA DE NOTAS ── */
        .table th { background: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }
        .nota-input {
            width: 72px; text-align: center; border: 1px solid #d0d0d0;
            border-radius: 8px; padding: 5px 6px; font-size: .9rem;
        }
        .nota-input:focus { border-color: #9333ea; outline: none;
            box-shadow: 0 0 0 3px rgba(147,51,234,.15); }

        /* ── BADGES ── */
        .badge-apro    { background: #d1fae5; color: #065f46; padding: 4px 11px;
            border-radius: 20px; font-weight: 600; font-size: .82rem; }
        .badge-desapr  { background: #fee2e2; color: #991b1b; padding: 4px 11px;
            border-radius: 20px; font-weight: 600; font-size: .82rem; }
        .badge-pend    { background: #fef3c7; color: #92400e; padding: 4px 11px;
            border-radius: 20px; font-weight: 600; font-size: .82rem; }

        .btn-purple { background: #9d4edd; color: white; border: none; }
        .btn-purple:hover { background: #7b2cbf; color: white; }

        /* ── SELECTOR GRADO/CURSO ── */
        .selector-grado { background: white; border-radius: 12px; padding: 20px 26px;
            box-shadow: 0 4px 14px rgba(0,0,0,.05); margin-bottom: 28px; }
    </style>
</head>
<body>

<!-- TOP BAR -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>DOCENTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="profesor.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_secciones.php" class="nav-link active"><i class="bi bi-grid-3x2-gap me-1"></i>Mis Secciones</a>
        <a href="mis_cursos.php" class="nav-link"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_estudiantes.php" class="nav-link"><i class="bi bi-people me-1"></i>Mis Estudiantes</a>
        <a href="calificaciones.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<!-- MAIN -->
<div class="main-content">

    <?php if (isset($_GET['guardado'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3">
        <i class="bi bi-check-circle me-1"></i> Notas guardadas correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error_guard'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <?= htmlspecialchars(urldecode($_GET['error_guard'])) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- ─── RESUMEN DE SECCIONES ASIGNADAS ─── -->
    <?php if (empty($misAsignaciones)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>
        No tienes secciones asignadas aún. Contacta al administrador.
    </div>
    <?php else: ?>

    <!-- SELECTOR -->
    <div class="selector-grado">
        <h5 class="mb-3" style="color:#0d0216; font-weight:700;">
            <i class="bi bi-grid-3x2-gap me-2" style="color:#9d4edd;"></i>
            Selecciona Grado / Sección / Curso
        </h5>
        <form method="GET" action="mis_secciones.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Grado y Sección</label>
                <select name="id_grado" class="form-select" id="selGrado" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($gradosCursos as $g): ?>
                    <option value="<?= $g['id_grado'] ?>"
                        <?= $id_grado_sel == $g['id_grado'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['nombre_grado'] . ' — Sección ' . $g['seccion']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Curso</label>
                <select name="id_curso" class="form-select" id="selCurso" required>
                    <option value="">-- Primero selecciona un grado --</option>
                    <?php if ($id_grado_sel && isset($gradosCursos[$id_grado_sel])): ?>
                        <?php foreach ($gradosCursos[$id_grado_sel]['cursos'] as $c): ?>
                        <option value="<?= $c['id_curso'] ?>"
                            <?= $id_curso_sel == $c['id_curso'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre_curso']) ?>
                        </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-purple w-100" type="submit">
                    <i class="bi bi-search me-1"></i> Ver
                </button>
            </div>
        </form>
    </div>

    <!-- ─── LISTA DE ALUMNOS CON NOTAS ─── -->
    <?php if ($id_grado_sel && $id_curso_sel): ?>
    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-1" style="color:#0d0216; font-weight:700;">
                    <i class="bi bi-people me-2" style="color:#9d4edd;"></i>
                    <?= htmlspecialchars($nombre_grado) ?> — Sección <?= htmlspecialchars($seccion_sel) ?>
                </h4>
                <span class="text-muted" style="font-size:.92rem;">
                    Curso: <strong><?= htmlspecialchars($nombre_curso) ?></strong> &nbsp;|&nbsp;
                    <?= count($alumnos) ?> alumno(s)
                </span>
            </div>
            <button class="btn btn-purple px-4" type="submit" form="formNotas">
                <i class="bi bi-floppy me-1"></i> Guardar todas las notas
            </button>
        </div>

        <?php if (empty($alumnos)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-inbox" style="font-size:2.2rem; display:block; margin-bottom:8px;"></i>
            No hay alumnos matriculados en esta sección.
        </div>
        <?php else: ?>
        <form id="formNotas" method="POST" action="procesar_notas_seccion.php">
            <input type="hidden" name="id_grado"    value="<?= $id_grado_sel ?>">
            <input type="hidden" name="id_curso"    value="<?= $id_curso_sel ?>">
            <input type="hidden" name="id_profesor" value="<?= (int)$id_profesor ?>">

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="rounded-start">#</th>
                            <th>Apellidos y Nombres</th>
                            <th class="text-center">Unidad 1<br><small class="fw-normal">(0–20)</small></th>
                            <th class="text-center">Unidad 2<br><small class="fw-normal">(0–20)</small></th>
                            <th class="text-center">Unidad 3<br><small class="fw-normal">(0–20)</small></th>
                            <th class="text-center rounded-end">Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($alumnos as $i => $alu): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($alu['apellido'] . ', ' . $alu['nombre']) ?></strong>
                            <input type="hidden" name="alumnos[<?= $i ?>][id]" value="<?= $alu['id'] ?>">
                        </td>
                        <td class="text-center">
                            <input type="number" name="alumnos[<?= $i ?>][nota_1]"
                                   class="nota-input nota-field"
                                   data-row="<?= $i ?>"
                                   min="0" max="20" step="0.1"
                                   value="<?= $alu['nota_1'] !== null ? htmlspecialchars($alu['nota_1']) : '' ?>"
                                   placeholder="–">
                        </td>
                        <td class="text-center">
                            <input type="number" name="alumnos[<?= $i ?>][nota_2]"
                                   class="nota-input nota-field"
                                   data-row="<?= $i ?>"
                                   min="0" max="20" step="0.1"
                                   value="<?= $alu['nota_2'] !== null ? htmlspecialchars($alu['nota_2']) : '' ?>"
                                   placeholder="–">
                        </td>
                        <td class="text-center">
                            <input type="number" name="alumnos[<?= $i ?>][nota_3]"
                                   class="nota-input nota-field"
                                   data-row="<?= $i ?>"
                                   min="0" max="20" step="0.1"
                                   value="<?= $alu['nota_3'] !== null ? htmlspecialchars($alu['nota_3']) : '' ?>"
                                   placeholder="–">
                        </td>
                        <td class="text-center" id="prom-<?= $i ?>">
                            <?php
                            $notas  = array_filter([$alu['nota_1'], $alu['nota_2'], $alu['nota_3']], fn($n) => $n !== null);
                            $prom   = empty($notas) ? null : round(array_sum($notas) / count($notas));
                            ?>
                            <?php if ($prom !== null): ?>
                                <span class="<?= $prom >= 11 ? 'badge-apro' : 'badge-desapr' ?>">
                                    <?= $prom ?>
                                </span>
                            <?php else: ?>
                                <span class="badge-pend">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-purple px-5" type="submit">
                    <i class="bi bi-floppy me-2"></i> Guardar notas
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <?php elseif (!empty($gradosCursos)): ?>
    <!-- Tarjetas resumen de secciones -->
    <div class="row g-3 mt-1">
        <?php foreach ($gradosCursos as $g): ?>
        <div class="col-md-4">
            <div class="card-seccion">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="background:#f3e8ff; width:46px; height:46px; border-radius:12px;
                                display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-mortarboard" style="font-size:1.4rem; color:#9d4edd;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="color:#0d0216; font-size:1rem;">
                            <?= htmlspecialchars($g['nombre_grado']) ?>
                        </div>
                        <div class="text-muted" style="font-size:.85rem;">
                            Sección <strong><?= htmlspecialchars($g['seccion']) ?></strong>
                        </div>
                    </div>
                </div>
                <?php foreach ($g['cursos'] as $c): ?>
                <a href="mis_secciones.php?id_grado=<?= $g['id_grado'] ?>&id_curso=<?= $c['id_curso'] ?>"
                   class="btn btn-sm btn-outline-secondary w-100 mb-1 text-start">
                    <i class="bi bi-book me-1"></i>
                    <?= htmlspecialchars($c['nombre_curso']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- JS para actualizar promedio en tiempo real -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Actualizar selector de cursos según grado seleccionado
const gradoCursos = <?= json_encode(array_values($gradosCursos)) ?>;

document.getElementById('selGrado')?.addEventListener('change', function () {
    const id = parseInt(this.value);
    const selCurso = document.getElementById('selCurso');
    selCurso.innerHTML = '<option value="">-- Selecciona un curso --</option>';
    const g = gradoCursos.find(x => x.id_grado == id);
    if (g) {
        g.cursos.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id_curso;
            opt.textContent = c.nombre_curso;
            selCurso.appendChild(opt);
        });
    }
});

// Recalcular promedio de fila en tiempo real
function recalcularFila(row) {
    const inputs = document.querySelectorAll(`.nota-field[data-row="${row}"]`);
    const notas  = Array.from(inputs)
        .map(i => parseFloat(i.value))
        .filter(n => !isNaN(n));
    const cell = document.getElementById(`prom-${row}`);
    if (!cell) return;
    if (notas.length === 0) {
        cell.innerHTML = '<span class="badge-pend">—</span>';
        return;
    }
    const prom = Math.round(notas.reduce((a, b) => a + b, 0) / notas.length);
    const cls  = prom >= 11 ? 'badge-apro' : 'badge-desapr';
    cell.innerHTML = `<span class="${cls}">${prom}</span>`;
}

document.querySelectorAll('.nota-field').forEach(inp => {
    inp.addEventListener('input', () => recalcularFila(inp.dataset.row));
});
</script>
</body>
</html>
