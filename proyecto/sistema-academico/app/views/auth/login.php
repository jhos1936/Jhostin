<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Academico</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
    <link rel="apple-touch-icon" href="/favicon-180.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; align-items: center; }
        .login-wrapper { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; display: flex; width: 800px; margin: auto; }
        .login-sidebar { background: #0d0216; padding: 40px; color: white; width: 40%; display: flex; flex-direction: column; justify-content: center; }
        .login-form { padding: 50px; width: 60%; }
        .form-control { border: 1px solid #ddd; padding: 12px; border-radius: 8px; }
        .btn-login { background: #0d0216; color: white; padding: 12px; border-radius: 8px; width: 100%; border: none; font-weight: bold; }
        .btn-login:hover { background: #1a0b2e; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-sidebar">
        <h3>Bienvenido</h3>
        <p>Sistema Academico</p>
        <hr style="border-color: #bc80ff;">
        <small>Acceso exclusivo para administradores, profesores y estudiantes.</small>
    </div>
    <div class="login-form">
        <h4 class="mb-4">Iniciar Sesion</h4>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger py-2">Usuario o contrasena incorrectos.</div>
        <?php endif; ?>
        <form action="/login.php" method="POST">
            <div class="mb-3">
                <label>Usuario</label>
                <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Contrasena</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-login">INGRESAR</button>
        </form>
        <div style="text-align:center; margin-top:20px; font-size:14px; color:#666;">
            No tienes cuenta?
            <a href="/registro.php" style="color:#9d4edd; font-weight:600; text-decoration:none;">Registrate aqui</a>
        </div>
    </div>
</div>
</body>
</html>
