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
    <title>Recuperar contraseña - SENATI</title>
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

        .recuperar-container {
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

        .recuperar-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .recuperar-header .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }

        .recuperar-header .logo img {
            max-width: 200px;
            height: auto;
        }

        .recuperar-header h1 {
            color: #333;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .recuperar-header p {
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

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .enlace-demo {
            background: #eef1ff;
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 20px;
            font-size: 13px;
            word-break: break-all;
        }

        .enlace-demo a {
            color: #4a3f9c;
            font-weight: 600;
            text-decoration: none;
        }

        .enlace-demo a:hover {
            text-decoration: underline;
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

        .btn-enviar {
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

        .btn-enviar:hover {
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

        .recuperar-footer {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 12px;
        }

        .recuperar-footer span { color: #667eea; }
    </style>
</head>
<body>

<div class="recuperar-container">

    <div class="recuperar-header">
        <div class="logo">
            <img src="assets/senati_logo.png" alt="SENATI" />
        </div>
        <h1>¿Olvidaste tu contraseña?</h1>
        <p>Ingresa tu correo y te enviaremos un enlace para recuperarla</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($exito)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($exito); ?></div>
    <?php endif; ?>

    <?php if (isset($enlaceDemo)): ?>
        <div class="enlace-demo">
            📧 Este entorno no tiene un correo real configurado, así que aquí tienes tu enlace de recuperación:<br><br>
            <a href="<?php echo htmlspecialchars($enlaceDemo); ?>"><?php echo htmlspecialchars($enlaceDemo); ?></a>
        </div>
    <?php endif; ?>

    <?php if (!isset($exito)): ?>
    <form action="index.php?controller=recuperar&action=enviar" method="POST" novalidate>
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

        <button type="submit" class="btn-enviar">
            📨 Enviar enlace de recuperación
        </button>
    </form>
    <?php endif; ?>

    <div class="volver-login">
        <a href="index.php">← Volver a iniciar sesión</a>
    </div>

    <div class="recuperar-footer">
        <p>SENATI <span>v1.0</span></p>
        <p>&copy; <?php echo date('Y'); ?> Todos los derechos reservados</p>
    </div>

</div>
</body>
</html>
