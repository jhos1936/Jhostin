<?php
require_once "../../../middleware/Auth.php";
require_once "../../controllers/UsuarioController.php";

$controller = new UsuarioController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    $id_rol = $_POST['id_rol'] ?? '';

    if ($controller->crear($usuario, $password, $id_rol)) {
        header("Location: usuarios.php?success=1");
        exit;
    } else {
        $error = $controller->getError();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; display: flex; align-items: center; min-height: 100vh; }
        .form-card { background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); padding: 40px; }
        .btn-primary { background-color: #9d4edd; border: none; }
        .btn-primary:hover { background-color: #7b2cbf; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="form-card">
                <h3 class="mb-4 text-center fw-bold text-dark">Registrar Usuario</h3>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nombre de Usuario</label>
                        <input type="text" name="usuario" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Rol</label>
                        <select name="id_rol" class="form-select form-select-lg" required>
                            <option value="">Seleccione una opción...</option>
                            <?php 
                            $db = new Database();
                            $pdo = $db->conectar();
                            $roles = $pdo->query("SELECT id_rol, nombre FROM roles")->fetchAll(PDO::FETCH_ASSOC);
                            foreach($roles as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">Registrar</button>
                        <a href="usuarios.php" class="btn btn-link text-decoration-none text-secondary">Volver atrás</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>