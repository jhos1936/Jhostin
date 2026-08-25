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
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'cliente';

// Obtener cliente_id directo de la BD
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
    $sufijo = '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nombre_usuario);
} else {
    $result    = $conn->query("
        SELECT p.*, c.nombre as cliente_nombre 
        FROM proyectos p 
        LEFT JOIN clientes c ON p.cliente_id = c.id 
        ORDER BY p.id ASC
    ");
    $proyectos = [];
    while ($row = $result->fetch_assoc()) $proyectos[] = $row;
    $sufijo = '_todos';
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Reporte_Proyectos' . $sufijo . '_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['ID', 'Nombre', 'Cliente', 'Estado', 'Presupuesto', 'Inicio', 'Fin', 'Descripcion']);

foreach ($proyectos as $row) {
    fputcsv($output, [
        $row['id'],
        $row['nombre'],
        $row['cliente_nombre'] ?? 'N/A',
        str_replace('_', ' ', $row['estado']),
        $row['presupuesto'],
        $row['fecha_inicio'],
        $row['fecha_fin'] ?? 'N/A',
        $row['descripcion'] ?? ''
    ]);
}

fclose($output);
exit;
