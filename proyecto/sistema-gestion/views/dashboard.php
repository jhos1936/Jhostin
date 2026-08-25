<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$rol = $_SESSION['usuario_rol'] ?? 'cliente';
$es_admin = ($rol == 'admin');
$es_gerente = ($rol == 'gerente');
$es_editor = ($rol == 'admin' || $rol == 'gerente');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/svg+xml" href="assets/gestion_icon.svg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SENATI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 20px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .logo img {
            max-width: 180px;
            height: auto;
            background: white;
            padding: 8px 10px;
            border-radius: 8px;
            display: block;
            margin: 0 auto 8px;
        }

        .sidebar .logo h2 {
            font-size: 13px;
            color: #aaa;
            font-weight: 400;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 5px;
        }

        .sidebar ul li a {
            color: #ccc;
            text-decoration: none;
            padding: 12px 15px;
            display: block;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .sidebar ul li a.active {
            background: #e94560;
            color: white;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 40px);
        }

        .sidebar .logout a {
            color: #e94560;
            border: 1px solid #e94560;
            text-align: center;
        }

        .sidebar .logout a:hover {
            background: #e94560;
            color: white;
        }

        /* Main content */
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            color: #333;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }

        .header .user-avatar {
            width: 40px;
            height: 40px;
            background: #e94560;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: #00b894;
            color: white;
        }

        .btn-danger {
            background: #d63031;
            color: white;
        }

        .btn-edit {
            background: #fdcb6e;
            color: #333;
            padding: 5px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            margin-right: 5px;
        }

        .btn-delete {
            background: #d63031;
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
        }

        /* Tabla */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .data-table th,
        .data-table td {
            padding: 12px 15px;
            text-align: left;
        }

        .data-table th {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .data-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Estados */
        .status-pendiente {
            background: #ffeaa7;
            color: #d63031;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-en_progreso {
            background: #81ecec;
            color: #00b894;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-completado {
            background: #55efc4;
            color: #00b894;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-actions h2 {
            color: #333;
        }

        /* Formularios */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #667eea;
            outline: none;
        }

        /* Mensaje de permiso */
        .msg-permiso {
            background: #ffeaa7;
            color: #d63031;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }

            body {
                flex-direction: column;
            }

            .sidebar .logout {
                position: relative;
                margin-top: 20px;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="logo">
            <img src="assets/senati_logo.png" alt="SENATI" />
            <h2>TechSolutions</h2>
        </div>
        <ul>
            <?php if ($es_editor): ?><li><a href="index.php?controller=cliente&action=listar">👥 Clientes</a></li><?php endif; ?>
            <li><a href="index.php?controller=proyecto&action=listar">📊 Proyectos</a></li>
            <li><a href="reports/reporte_proyectos.php" target="_blank" onclick="window.open('reports/reporte_proyectos.php','_blank'); return false;">📄 Reporte Proyectos</a></li>
            <?php if ($es_editor): ?><li><a href="reports/reporte_clientes.php" target="_blank" onclick="window.open('reports/reporte_clientes.php','_blank'); return false;">📄 Reporte Clientes</a></li><?php endif; ?>
        </ul>
        <div class="logout">
            <a href="index.php?controller=login&action=logout">🚪 Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h2>Bienvenido, <?php echo isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : 'Usuario'; ?></h2>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo isset($_SESSION['usuario_nombre']) ? strtoupper(substr($_SESSION['usuario_nombre'], 0, 1)) : 'U'; ?>
                </div>
                <span><?php echo isset($_SESSION['usuario_rol']) ? ucfirst($_SESSION['usuario_rol']) : ''; ?></span>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-permiso">⚠️ <?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>