<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/ProfesorController.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$controller = new ProfesorController();
$profesor = $controller->obtenerPorId($id);

if (!$profesor) {
    header("Location: profesores.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Profesor</title>
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
    <h3 class="mb-4" style="color: #0d0216; font-weight: 600;">Editar Datos del Profesor</h3>
    <form action="procesar_profesor.php?accion=actualizar&id=<?= $profesor['id_profesor']; ?>" method="POST">
        <div class="mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($profesor['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($profesor['apellido'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">DNI</label>
            <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($profesor['dni']); ?>" maxlength="15" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($profesor['especialidad'] ?? ''); ?>" required>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="profesores.php" class="btn btn-light">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="background: #9d4edd; border: none;">Actualizar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>
