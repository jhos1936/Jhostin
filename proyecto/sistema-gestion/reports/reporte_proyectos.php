<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../config/conexion.php';

$conn           = Conexion::getInstance()->getConn();
$rol            = $_SESSION['usuario_rol']    ?? 'cliente';
$usuario_id     = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['usuario_nombre'] ?? '';

// Obtener cliente_id desde la BD (funciona aunque no esté en sesión)
$cliente_id = null;
$colCheck = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'cliente_id'");
if ($colCheck && $colCheck->num_rows > 0) {
    $stmtU = $conn->prepare("SELECT cliente_id FROM usuarios WHERE id = ?");
    $stmtU->bind_param("i", $usuario_id);
    $stmtU->execute();
    $stmtU->bind_result($cliente_id);
    $stmtU->fetch();
    $stmtU->close();
}

if ($rol === 'cliente' && !empty($cliente_id)) {
    $stmt = $conn->prepare("
        SELECT p.*, c.nombre as cliente_nombre 
        FROM proyectos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        WHERE p.cliente_id = ?
        ORDER BY p.id ASC
    ");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result    = $stmt->get_result();
    $proyectos = [];
    while ($row = $result->fetch_assoc()) $proyectos[] = $row;
    $stmt->close();
    $titulo_extra = " — " . htmlspecialchars($nombre_usuario);
} else {
    $result    = $conn->query("
        SELECT p.*, c.nombre as cliente_nombre 
        FROM proyectos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.id ASC
    ");
    $proyectos = [];
    while ($row = $result->fetch_assoc()) $proyectos[] = $row;
    $titulo_extra = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Proyectos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .reporte { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #003366; padding-bottom: 15px; }
        .header h1 { color: #003366; }
        .header p { color: #666; font-size: 14px; }
        .badge-cliente { display:inline-block; background: #e8f4fd; color: #2980b9; padding: 3px 10px; border-radius: 4px; font-size: 12px; margin-top:6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #003366; color: white; padding: 10px; text-align: left; font-size: 12px; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; font-size: 11px; }
        tr:hover { background: #f0f0f0; }
        .acciones { display:flex; gap:8px; margin-bottom:15px; flex-wrap:wrap; }
        .btn { display: inline-block; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; cursor: pointer; font-size: 14px; border: none; }
        .btn-print  { background: #003366; }
        .btn-csv    { background: #2980b9; }
        .btn-volver { background: #7f8c8d; }
        .footer { text-align: center; margin-top: 20px; color: #999; font-size: 11px; border-top: 1px solid #ddd; padding-top: 10px; }
        @media print { .acciones { display:none; } body { background: white; } .reporte { box-shadow: none; padding: 10px; } }
    </style>
</head>
<body>
<div class="reporte">
    <div class="acciones">
        <a href="../index.php?controller=proyecto&action=listar" class="btn btn-volver" onclick="if(window.opener){window.close(); return false;}">← Volver</a>
        <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir</button>
        <a href="descargar_proyectos.php" class="btn btn-csv">💾 Guardar</a>
    </div>
    <div class="header">
        <h1>📊 REPORTE DE PROYECTOS<?php echo $titulo_extra; ?></h1>
        <p>Fecha: <?php echo date('d/m/Y H:i:s'); ?> | Total: <?php echo count($proyectos); ?> proyecto(s)</p>
        <?php if ($rol === 'cliente'): ?>
            <span class="badge-cliente">Vista de cliente: solo tus proyectos</span>
        <?php endif; ?>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Cliente</th><th>Estado</th>
                <th>Presupuesto</th><th>Inicio</th><th>Fin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proyectos as $row): ?>
            <tr>
                <td><strong><?php echo $row['id']; ?></strong></td>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['cliente_nombre'] ?? 'N/A'); ?></td>
                <td><?php echo str_replace('_', ' ', $row['estado']); ?></td>
                <td>$<?php echo number_format($row['presupuesto'], 2); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['fecha_inicio'])); ?></td>
                <td><?php echo $row['fecha_fin'] ? date('d/m/Y', strtotime($row['fecha_fin'])) : 'N/A'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($proyectos)): ?>
        <p style="text-align:center; padding:30px; color:#888;">No hay proyectos para mostrar.</p>
    <?php endif; ?>
    <div class="footer">
        <p>TecnoSoluciones SGP &copy; <?php echo date('Y'); ?> — Generado el <?php echo date('d/m/Y H:i:s'); ?></p>
    </div>
</div>
<script>
if (new URLSearchParams(window.location.search).get('imprimir') === '1') {
    window.onload = function () { window.print(); };
}
</script>
</body>
</html>
