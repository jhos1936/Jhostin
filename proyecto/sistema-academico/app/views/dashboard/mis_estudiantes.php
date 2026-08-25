<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'profesor') {
    header("Location: /login.php");
    exit();
}
$root = dirname(__DIR__, 3) . '/';
require_once $root . 'config/Database.php';
require_once $root . 'app/controllers/SeccionProfesorController.php';

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

$seccionCtrl = new SeccionProfesorController();

// Grados/secciones asignados a este profesor (sin duplicados)
$misAsignaciones = $seccionCtrl->listarPorProfesor($id_profesor);
$gradosAsignados = [];
foreach ($misAsignaciones as $a) {
    $gradosAsignados[$a['id_grado']] = [
        'id_grado'     => $a['id_grado'],
        'nombre_grado' => $a['nombre_grado'],
        'seccion'      => $a['seccion'],
    ];
}

// Alumnos matriculados en esos grados/secciones
$estudiantes = [];
foreach ($gradosAsignados as $g) {
    $alumnosGrado = $seccionCtrl->alumnosPorGrado($g['id_grado']);
    foreach ($alumnosGrado as $al) {
        $al['grado'] = $g['nombre_grado'] . ' — Sección ' . $g['seccion'];
        $estudiantes[] = $al;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Estudiantes | Panel Docente</title>
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
    </style>
</head>
<body>
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-mortarboard-fill me-2"></i>DOCENTE PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="profesor.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="mis_secciones.php" class="nav-link"><i class="bi bi-grid-3x2-gap me-1"></i>Mis Secciones</a>
        <a href="mis_cursos.php" class="nav-link"><i class="bi bi-journal-bookmark me-1"></i>Mis Cursos</a>
        <a href="mis_estudiantes.php" class="nav-link active"><i class="bi bi-people me-1"></i>Mis Estudiantes</a>
        <a href="calificaciones.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>
<div class="main-content">
    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color: #0d0216; font-weight: 600;">Listado de mis Estudiantes</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="rounded-start">Nombre Completo</th>
                        <th>Grado</th>
                        <th class="rounded-end">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($estudiantes)): ?>
                        <?php foreach ($estudiantes as $est): ?>
                        <tr>
                            <td><?= htmlspecialchars($est['nombre'] . ' ' . ($est['apellido'] ?? '')); ?></td>
                            <td><?= htmlspecialchars($est['grado'] ?? 'No asignado'); ?></td>
                            <td><?= htmlspecialchars($est['correo']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <?= empty($gradosAsignados)
                                    ? 'No tienes secciones asignadas. Contacta al administrador.'
                                    : 'No hay estudiantes matriculados en tus secciones.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>