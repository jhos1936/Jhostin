<?php
session_start();

// Solo admin y gerente pueden ver el reporte de clientes
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}
$rol = $_SESSION['usuario_rol'] ?? 'cliente';
if ($rol === 'cliente') {
    header('Location: ../index.php?controller=proyecto&action=listar');
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$conn = Conexion::getInstance()->getConn();
$clientes = $conn->query("SELECT id, nombre, email, telefono, direccion FROM clientes WHERE activo = 1 ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .reporte {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #003366;
            padding-bottom: 15px;
        }
        .header h1 { color: #003366; }
        .header p { color: #666; font-size: 14px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #003366;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        tr:hover { background: #f0f0f0; }
        tr:nth-child(even) { background: #fafafa; }
        
        .acciones { display:flex; gap:8px; margin-bottom:15px; flex-wrap:wrap; }
        .btn { display: inline-block; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; cursor: pointer; font-size: 14px; border: none; }
        .btn-print  { background: #003366; }
        .btn-csv    { background: #2980b9; }
        .btn-volver { background: #7f8c8d; }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 11px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @media print {
            body { background: white; }
            .reporte { box-shadow: none; padding: 10px; }
            .acciones { display: none; }
        }
    </style>
</head>
<body>

<div class="reporte">
    <div class="acciones">
        <a href="../index.php?controller=cliente&action=listar" class="btn btn-volver" onclick="if(window.opener){window.close(); return false;}">← Volver</a>
        <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir</button>
        <a href="descargar_clientes.php" class="btn btn-csv">💾 Guardar</a>
    </div>
    
    <div class="header">
        <h1>📋 REPORTE DE CLIENTES</h1>
        <p>Fecha: <?php echo date('d/m/Y H:i:s'); ?> | Total: <?php echo $clientes->num_rows; ?> clientes</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $clientes->fetch_assoc()): ?>
            <tr>
                <td><strong><?php echo $row['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                <td><?php echo htmlspecialchars($row['direccion']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>TechSolutions&copy; <?php echo date('Y'); ?> - Reporte generado el <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
</div>
<script>
if (new URLSearchParams(window.location.search).get('imprimir') === '1') {
    window.onload = function () { window.print(); };
}
</script>
</body>
</html>