<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: /app/views/auth/login.php");
    exit();
}

require_once __DIR__ . '/app/controllers/RegistroController.php';
$ctrl = new RegistroController();

$error       = '';
$exito       = '';
$usuario_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario   = trim($_POST['usuario']  ?? '');
    $password  = $_POST['password']      ?? '';
    $confirmar = $_POST['confirmar']     ?? '';
    $usuario_val = htmlspecialchars($usuario);

    if (empty($usuario) || empty($password)) {
        $error = 'Por favor completa todos los campos.';
    } elseif ($password !== $confirmar) {
        $error = 'Las contrasenas no coinciden.';
    } else {
        $resultado = $ctrl->registrar($usuario, $password);
        if ($resultado['exito']) {
            $exito = $resultado['mensaje'];
        } else {
            $error = $resultado['mensaje'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Academico</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; }
        .registro-wrapper { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; display: flex; width: 800px; margin: auto; }
        .registro-sidebar { background: #0d0216; padding: 40px; color: white; width: 40%; display: flex; flex-direction: column; justify-content: center; }
        .registro-form { padding: 50px; width: 60%; }
        .form-control { border: 1px solid #ddd; padding: 12px; border-radius: 8px; }
        .btn-registro { background: #0d0216; color: white; padding: 12px; border-radius: 8px; width: 100%; border: none; font-weight: bold; }
        .btn-registro:hover { background: #1a0b2e; color: white; }
    </style>
</head>
<body>
<div class="registro-wrapper">
    <div class="registro-sidebar">
        <h3>Unete al Sistema</h3>
        <p>Sistema Academico</p>
        <hr style="border-color: #bc80ff;">
        <small>Crea tu cuenta como <strong>Estudiante</strong> y accede a tus cursos y calificaciones.</small>
    </div>
    <div class="registro-form">
        <h4>Crear Cuenta</h4>
        <?php if ($error): ?><div class="alert alert-danger py-2">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($exito): ?>
            <div class="alert alert-success py-2">✅ <?= htmlspecialchars($exito) ?></div>
            <a href="/app/views/auth/login.php" class="btn btn-registro">Ir al Login</a>
        <?php else: ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario</label>
                    <input type="text" name="usuario" class="form-control" value="<?= $usuario_val ?>" placeholder="Ej. juan.perez" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Contrasena</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimo 6 caracteres" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Confirmar Contrasena</label>
                    <input type="password" name="confirmar" class="form-control" placeholder="Repite tu contrasena" required>
                </div>
                <button type="submit" class="btn btn-registro">CREAR CUENTA</button>
            </form>
        <?php endif; ?>
        <div style="text-align:center; margin-top:20px; font-size:14px; color:#666;">
            Ya tienes cuenta?
            <a href="/app/views/auth/login.php" style="color:#9d4edd; font-weight:600; text-decoration:none;">Inicia sesion aqui</a>
        </div>
    </div>
</div>
</body>
</html>
