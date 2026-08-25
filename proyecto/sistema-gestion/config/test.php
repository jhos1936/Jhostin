<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/conexion.php';

$conn = Conexion::getInstance()->getConn();
echo "Conexión exitosa";
?>