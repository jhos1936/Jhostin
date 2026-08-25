<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/EstudianteController.php";
require_once "../../controllers/GradoController.php";

$controller = new EstudianteController();
$gradoCtrl  = new GradoController();
$grados     = $gradoCtrl->listar();
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo   = trim($_POST['correo']   ?? '');
    $grado    = trim($_POST['grado']    ?? '');

    if ($controller->crear($nombre, $apellido, $correo, $grado)) {
        header("Location: estudiantes.php");
        exit();
    } else {
        $error = $controller->getError();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Estudiante</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; padding: 40px; }
        .card-edit { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); padding: 30px; max-width: 500px; margin: auto; }
    </style>
</head>
<body>

<div class="card-edit">
    <h3 class="mb-4" style="color: #0d0216; font-weight: 600;">Registrar Nuevo Estudiante</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="procesar_estudiante.php?accion=crear" method="POST">
        <div class="mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellido" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="correo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Grado</label>
            <select name="grado" class="form-select" required>
                <option value="">Seleccione un grado...</option>
                <?php foreach ($grados as $g): ?>
                    <option value="<?= $g['id_grado']; ?>">
                        <?= htmlspecialchars($g['nombre_grado'] . ' ' . $g['seccion']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="estudiantes.php" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn" style="background: #9d4edd; color: white; border: none;">Registrar</button>
        </div>
    </form>
</div>

</body>
</html>