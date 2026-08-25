<?php
// Determinamos a dónde debe ir según el rol
$rol = $_SESSION['rol'] ?? '';
$linkInicio = '#'; // Valor por defecto

switch ($rol) {
    case 'administrador':
        $linkInicio = 'admin.php';
        break;
    case 'profesor':
        $linkInicio = 'profesor.php';
        break;
    case 'estudiante':
        $linkInicio = 'estudiante.php';
        break;
}
?>

<a href="<?php echo $linkInicio; ?>" style="color:white; text-decoration:none; padding:10px;">
    <i class="bi bi-house-door"></i> Inicio
</a>