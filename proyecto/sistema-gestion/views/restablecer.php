<?php
if (isset($_SESSION["usuario_id"])) {
    header("Location: index.php?controller=cliente&action=listar");
    exit;
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/svg+xml" href="assets/gestion_icon.svg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña - SENATI</title>
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

        .restablecer-container {
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

        .restablecer-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .restablecer-header .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }

        .restablecer-header .logo img {
            max-width: 200px;
            height: auto;
        }

        .restablecer-header h1 {
            color: #333;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .restablecer-header p {
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

        .btn-guardar {
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

        .btn-guardar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102,126,234,0.4);
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

        .restablecer-footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }

        .restablecer-footer span { color: #667eea; }
    </style>
</head>
<body>

<div class="restablecer-container">

    <div class="restablecer-header">
        <div class="logo">
            <img src="assets/senati_logo.png" alt="SENATI" />
        </div>
        <h1>Crear nueva contraseña</h1>
        <p>Elige una nueva contraseña para tu cuenta</p>
    </div>

    <?php if (isset($tokenInvalido)): ?>
        <div class="alert alert-error">⚠️ Este enlace no es válido o ya venció. Solicita uno nuevo.</div>
        <div class="volver-login">
            <a href="index.php?controller=recuperar">← Solicitar un nuevo enlace</a>
        </div>
    <?php else: ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?controller=recuperar&action=actualizar" method="POST" novalidate>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="form-group">
                <label for="password">🔒 Nueva contraseña</label>
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
                           placeholder="Repite la nueva contraseña" required>
                </div>
            </div>

            <button type="submit" class="btn-guardar">
                💾 Guardar nueva contraseña
            </button>
        </form>

        <div class="volver-login">
            <a href="index.php">← Volver a iniciar sesión</a>
        </div>

    <?php endif; ?>

    <div class="restablecer-footer">
        <p>SENATI <span>v1.0</span></p>
        <p>&copy; <?php echo date('Y'); ?> Todos los derechos reservados</p>
    </div>

</div>
</body>
</html>
