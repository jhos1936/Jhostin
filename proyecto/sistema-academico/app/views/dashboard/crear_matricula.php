<?php
require_once __DIR__ . '/../../controllers/MatriculaController.php';
require_once __DIR__ . '/../../controllers/GradoController.php';

$matriculaCtrl = new MatriculaController();
$graCtrl       = new GradoController();

$estudiantes = $matriculaCtrl->estudiantesNoMatriculados();
$grados      = $graCtrl->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nueva Matrícula</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 40px; }
        .form-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: auto; }
    </style>
</head>
<body>
<div class="form-container">
    <h4 class="mb-4 text-center">Registrar Nueva Matrícula</h4>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php if ($_GET['error'] === 'ya_matriculado'): ?>
                Este estudiante ya está matriculado en un salón.
            <?php elseif ($_GET['error'] === 'faltan_datos'): ?>
                Faltan datos en el formulario.
            <?php else: ?>
                Error al guardar la matrícula.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form action="procesar_matricula.php?accion=crear" method="POST">
        <div class="mb-3">
            <label class="form-label">Estudiante</label>
            <select name="id_estudiante" class="form-select" required>
                <option value="">-- Seleccione un alumno --</option>
                <?php if (!empty($estudiantes)): ?>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?= $e['id'] ?>">
                            <?= htmlspecialchars($e['nombre'] . ' ' . $e['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option disabled>Todos los estudiantes ya están matriculados</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Grado</label>
            <select name="id_grado" class="form-select" required>
                <option value="">-- Seleccione un grado --</option>
                <?php foreach ($grados as $g): ?>
                    <option value="<?= $g['id_grado'] ?>">
                        <?= htmlspecialchars($g['nombre_grado'] . ' - Secc: ' . $g['seccion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">Guardar Matrícula</button>
        <a href="matriculas.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
