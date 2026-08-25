<?php
// Si ya hay sesión activa, redirigir al sistema
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
    <title>Login - SENATI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header .logo {
            margin-bottom: 14px;
            display: flex;
            justify-content: center;
        }

        .login-header .logo img {
            max-width: 220px;
            height: auto;
        }

        .login-header h1 {
            color: #333;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #888;
            font-size: 14px;
        }

        .error-message {
            background: #ffe0e0;
            color: #d63031;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #d63031;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group .input-icon {
            position: relative;
        }

        .form-group .input-icon .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            outline: none;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
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
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #aaa;
            font-size: 12px;
        }

        .login-footer span {
            color: #667eea;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <img src="assets/senati_logo.png" alt="SENATI" />
            </div>
            <h1>TechSolutions</h1>
            <p>Ingresa tus credenciales para acceder</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="error-message" style="background:#d4edda; color:#155724; border-left-color:#28a745;">
                ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=login&action=autenticar" method="POST">
            <div class="form-group">
                <label for="email">📧 Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="admin@sistema.com" required autocomplete="off">
            </div>

            <div class="form-group">
                <label for="password">🔒 Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">
                🚀 Iniciar Sesión
            </button>
        </form>

        <div style="text-align:center; margin-top:14px; font-size:14px;">
            <a href="index.php?controller=recuperar" style="color:#667eea; font-weight:600; text-decoration:none;">¿Olvidaste tu contraseña?</a>
        </div>

        <div style="text-align:center; margin-top:10px; font-size:14px; color:#888;">
            ¿No tienes cuenta? <a href="index.php?controller=registro" style="color:#667eea; font-weight:600; text-decoration:none;">Regístrate aquí</a>
        </div>

        <div class="login-footer">
            <p>SENATI <span>v1.0</span></p>
            <p>&copy; <?php echo date('Y'); ?> Todos los derechos reservados</p>
        </div>
    </div>

</body>
</html>