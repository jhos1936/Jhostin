<?php
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php?controller=cliente&action=listar");
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/svg+xml" href="assets/gestion_icon.svg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SENATI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .registro-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .registro-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .registro-header .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }

        .registro-header .logo img {
            max-width: 200px;
            height: auto;
        }

        .registro-header h1 {
            color: #333;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .registro-header p {
            color: #888;
            font-size: 14px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-error {
            background: #ffe0e0;
            color: #d63031;
            border-left: 4px solid #d63031;
            animation: shake 0.4s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-8px); }
            75%      { transform: translateX(8px); }
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 44px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .ico {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 17px;
        }

        .password-hint {
            font-size: 12px;
            color: #aaa;
            margin-top: 5px;
        }

        .btn-registrar {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 6px;
        }

        .btn-registrar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102,126,234,0.4);
        }

        .btn-registrar:active {
            transform: translateY(0);
        }

        .volver-login {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #888;
        }

        .volver-login a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }

        .volver-login a:hover {
            text-decoration: underline;
        }

        .registro-footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }

        .registro-footer span { color: #667eea; }
    </style>
</head>
<body>

<div class="registro-container">

    <div class="registro-header">
        <div class="logo">
            <img src="assets/senati_logo.png" alt="SENATI" />
        </div>
        <h1>Crear Cuenta</h1>
        <p>Regístrate para acceder al sistema</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($exito)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($exito); ?></div>
    <?php endif; ?>

    <?php if (!isset($exito)): ?>
    <form action="index.php?controller=registro&action=registrar" method="POST" novalidate>

        <div class="form-group">
            <label for="nombre">👤 Nombre completo</label>
            <div class="input-wrap">
                <span class="ico">👤</span>
                <input type="text" id="nombre" name="nombre"
                       placeholder="Ej. Juan Pérez"
                       value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>"
                       required autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <label for="email">📧 Correo electrónico</label>
            <div class="input-wrap">
                <span class="ico">📧</span>
                <input type="email" id="email" name="email"
                       placeholder="correo@ejemplo.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       required autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <label for="password">🔒 Contraseña</label>
            <div class="input-wrap">
                <span class="ico">🔒</span>
                <input type="password" id="password" name="password"
                       placeholder="Mínimo 6 caracteres" required>
            </div>
            <p class="password-hint">Mínimo 6 caracteres</p>
        </div>

        <div class="form-group">
            <label for="confirmar">🔑 Confirmar contraseña</label>
            <div class="input-wrap">
                <span class="ico">🔑</span>
                <input type="password" id="confirmar" name="confirmar"
                       placeholder="Repite tu contraseña" required>
            </div>
        </div>

        <button type="submit" class="btn-registrar">
            ✨ Crear Cuenta
        </button>

    </form>
    <?php endif; ?>

    <div class="volver-login">
        ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
    </div>

    <div class="registro-footer">
        <p>SENATI <span>v1.0</span></p>
        <p>&copy; <?php echo date('Y'); ?> Todos los derechos reservados</p>
    </div>

</div>
</body>
</html>
