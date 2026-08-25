<?php
$root = dirname(__DIR__, 3) . '/';
require_once $root . "/middleware/Auth.php";
require_once $root . "/app/models/Calificacion.php";
require_once $root . "/app/controllers/EstudianteController.php";
require_once $root . "/app/controllers/CursoController.php";
require_once $root . "/app/controllers/ProfesorController.php";

$calModel    = new Calificacion();
$alumnoCtrl  = new EstudianteController();
$cursoCtrl   = new CursoController();
$profesorCtrl = new ProfesorController();

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: calificaciones.php"); exit; }

$calificacion = $calModel->obtenerPorId($id);
$alumnos      = $alumnoCtrl->listar();
$cursos       = $cursoCtrl->listar();
$profesores   = $profesorCtrl->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Calificación</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card p-4 shadow-sm">
            <h4>Editar Calificación</h4>
            <form action="procesar_calificacion.php?accion=editar&id=<?= (int)$id ?>" method="POST">
                <div class="mb-3">
                    <label>Estudiante</label>
                    <select name="id_estudiante" class="form-select" required>
                        <?php foreach($alumnos as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= ($calificacion['id_estudiante'] == $a['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['nombre'] . ' ' . $a['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Curso</label>
                    <select name="id_curso" class="form-select" required>
                        <?php foreach($cursos as $c): ?>
                            <option value="<?= $c['id_curso'] ?>" <?= ($calificacion['id_curso'] == $c['id_curso']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre_curso']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Profesor</label>
                    <select name="id_profesor" class="form-select" required>
                        <?php foreach($profesores as $p): $pid = $p['id_profesor'] ?? $p['id']; ?>
                            <option value="<?= $pid ?>" <?= ($calificacion['id_profesor'] == $pid) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Nota</label>
                    <input type="number" name="nota_1" class="form-control" value="<?= $calificacion['nota_1'] ?>" step="0.1" required>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>
