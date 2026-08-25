<?php
require_once __DIR__.'/../config/conexion.php';
class Usuario { public function login($email,$password){$c=Conexion::getInstance()->getConn();$st=$c->prepare('SELECT * FROM usuarios WHERE email=? LIMIT 1');$st->bind_param('s',$email);$st->execute();$u=$st->get_result()->fetch_assoc();return $u && password_verify($password,$u['password'])?$u:null;} }
?>
