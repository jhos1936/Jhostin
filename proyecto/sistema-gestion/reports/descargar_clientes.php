<?php
session_start();

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

// Configurar headers para descarga CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Reporte_Clientes_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Crear archivo CSV en la salida
$output = fopen('php://output', 'w');

// Agregar BOM para UTF-8 (para que Excel lo lea bien)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabecera
fputcsv($output, ['ID', 'Nombre', 'Email', 'Telefono', 'Direccion', 'Ciudad', 'Pais']);

// Datos
while ($row = $clientes->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['nombre'],
        $row['email'],
        $row['telefono'],
        $row['direccion'],
        $row['ciudad'] ?? 'N/A',
        $row['pais'] ?? 'N/A'
    ]);
}

fclose($output);
exit;
?>