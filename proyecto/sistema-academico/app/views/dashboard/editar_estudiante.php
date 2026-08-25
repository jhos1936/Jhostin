<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/EstudianteController.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new EstudianteController();
$estudiante = $controller->obtenerPorId($id);

if (!$estudiante) {
    header("Location: estudiantes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Estudiante</title>
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
    <h3 class="mb-4" style="color: #0d0216; font-weight: 600;">Editar Datos del Alumno</h3>
    <form action="procesar_estudiante.php?accion=actualizar&id=<?= $estudiante['id']; ?>" method="POST">
        <div class="mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($estudiante['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($estudiante['apellido'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($estudiante['correo']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Grado / Curso asignado</label>
            <select name="grado" class="form-select" required>
                <?php $g = $estudiante['grado']; ?>
                <option value="1ro Año" <?= $g == '1ro Año' ? 'selected' : ''; ?>>1ro Año</option>
                <option value="2do Año" <?= $g == '2do Año' ? 'selected' : ''; ?>>2do Año</option>
                <option value="3ro Año" <?= $g == '3ro Año' ? 'selected' : ''; ?>>3ro Año</option>
                <option value="4to Año" <?= $g == '4to Año' ? 'selected' : ''; ?>>4to Año</option>
                <option value="5to Año" <?= $g == '5to Año' ? 'selected' : ''; ?>>5to Año</option>
            </select>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="estudiantes.php" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="background: #9d4edd; border: none;">Actualizar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>
