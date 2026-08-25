<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['usuario']) || strtolower($_SESSION['rol']) != 'administrador') {
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><style>
body{background:#0d0216;color:#ff4d4d;font-family:'Segoe UI',sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;text-align:center;}
.error-box{background:#1a0b2e;padding:3rem;border-radius:20px;border:2px solid #ff4d4d;box-shadow:0 0 30px rgba(255,77,77,0.3);}
h1{font-size:3rem;margin-bottom:10px;text-shadow:0 0 15px #ff4d4d;}
a{color:#bc80ff;text-decoration:none;font-weight:bold;margin-top:20px;display:block;}
</style></head><body><div class="error-box"><h1>ACCESO DENEGADO</h1><p>No tienes los privilegios necesarios para ver esta sección.</p><a href="javascript:history.back()">Volver atrás</a></div></body></html>
<?php exit; } ?>
