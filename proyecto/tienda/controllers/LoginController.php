<?php
require_once __DIR__.'/../models/Usuario.php';
class LoginController {
 function registrar(){
   $nombre=trim($_POST['nombre']??''); $email=trim($_POST['email']??''); $pass=$_POST['password']??''; $rol='cliente';
   if($nombre==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($pass)<6){ $error='Completa todos los datos correctamente. La contraseña debe tener al menos 6 caracteres.'; require __DIR__.'/../views/registro.php'; return; }
   $c=Conexion::getInstance()->getConn(); $st=$c->prepare('SELECT id FROM usuarios WHERE email=? LIMIT 1'); $st->bind_param('s',$email); $st->execute();
   if($st->get_result()->fetch_assoc()){ $error='Ese correo ya está registrado.'; require __DIR__.'/../views/registro.php'; return; }
   $hash=password_hash($pass,PASSWORD_DEFAULT); $ins=$c->prepare('INSERT INTO usuarios(nombre,email,password,rol) VALUES(?,?,?,?)'); $ins->bind_param('ssss',$nombre,$email,$hash,$rol);
   if($ins->execute()){ header('Location:index.php'); exit; } $error='No se pudo crear la cuenta.'; require __DIR__.'/../views/registro.php';
 } function autenticar(){ $email=trim($_POST['email']??'');$pass=$_POST['password']??'';$u=(new Usuario())->login($email,$pass); if($u){$_SESSION['usuario_id']=$u['id'];$_SESSION['usuario_nombre']=$u['nombre'];$_SESSION['usuario_correo']=$u['email'];$_SESSION['usuario_rol']=$u['rol'];header('Location:index.php?controller=producto&action=listar');exit;} $error='Correo o contraseña incorrectos.'; require __DIR__.'/../views/login.php';} function logout(){session_destroy();header('Location:index.php');exit;} }
?>
