<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/CursoController.php";

$controller = new CursoController();
$cursos = $controller->listar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Cursos</title>
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
        .main-container { padding: 40px; max-width: 1100px; margin: 0 auto; }
        .crud-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .btn-purple { background: #9d4edd; color: white; border: none; transition: 0.3s; }
        .btn-purple:hover { background: #7b2cbf; color: white; }
        .table th { background-color: #0d0216; color: #bc80ff; border: none; }
        .table td { vertical-align: middle; }
    </style>
</head>
<body>

<!-- NAVBAR SUPERIOR -->
<nav class="top-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span class="brand-title"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</span>

    <div class="d-flex align-items-center flex-wrap gap-1">
        <a href="admin.php" class="nav-link"><i class="bi bi-house-door me-1"></i>Inicio</a>
        <a href="estudiantes.php" class="nav-link"><i class="bi bi-people me-1"></i>Estudiantes</a>
        <a href="profesores.php" class="nav-link"><i class="bi bi-person-video3 me-1"></i>Profesores</a>
        <a href="usuarios.php" class="nav-link"><i class="bi bi-person-gear me-1"></i>Usuarios</a>
        <a href="cursos.php" class="nav-link active"><i class="bi bi-book me-1"></i>Cursos</a>
        <a href="grados.php" class="nav-link"><i class="bi bi-mortarboard me-1"></i>Grados</a>
        <a href="matriculas.php" class="nav-link"><i class="bi bi-clipboard-check me-1"></i>Matrículas</a>
        <a href="calificaciones_admin.php" class="nav-link"><i class="bi bi-pencil-square me-1"></i>Calificaciones</a>
    </div>

    <a href="../../../logout.php" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i>Salir
    </a>
</nav>

<div class="main-container">
    <div class="crud-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="m-0" style="color: #0d0216; font-weight: 600;">Listado de Cursos</h3>
            <button class="btn btn-purple px-4" data-bs-toggle="modal" data-bs-target="#modalCurso">
                <i class="bi bi-plus-circle me-2"></i>Registrar Curso
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Curso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($cursos)): ?>
                        <?php foreach($cursos as $c): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($c['id_curso']) ?></strong></td>
                            <td><?= htmlspecialchars($c['nombre_curso']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">No hay cursos registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Registrar -->
<div class="modal fade" id="modalCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <form action="procesar_curso.php?accion=crear" method="POST">
                <div class="modal-header" style="background: #0d0216; color: #bc80ff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Curso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Curso:</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Programación PHP" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-purple px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>