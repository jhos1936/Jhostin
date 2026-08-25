<?php
ini_set('display_errors','1');ini_set('display_startup_errors','1');error_reporting(E_ALL);session_start();
$controller=$_GET['controller']??'';$action=$_GET['action']??'';
if($controller==='login'&&$action==='autenticar'){require 'controllers/LoginController.php';(new LoginController())->autenticar();exit;}
if($controller==='login'&&$action==='registro'){require 'views/registro.php';exit;}
if($controller==='login'&&$action==='registrar'){require 'controllers/LoginController.php';(new LoginController())->registrar();exit;}
if($controller==='login'&&$action==='logout'){require 'controllers/LoginController.php';(new LoginController())->logout();exit;}
if(empty($_SESSION['usuario_id'])){require 'views/login.php';exit;}

if(!empty($_SESSION['usuario_id']) && ($_SESSION['usuario_rol']??'')==='cliente'){
  $accion=$_GET['accion']??'';
  if(!isset($_SESSION['carrito'])) $_SESSION['carrito']=[];
  if($accion==='agregar_carrito' && $_SERVER['REQUEST_METHOD']==='POST'){
    require 'config/conexion.php';
    $db=Conexion::getInstance()->getConn(); $id=(int)($_POST['id']??0);
    $st=$db->prepare('SELECT * FROM productos WHERE id=? AND stock>0'); $st->bind_param('i',$id); $st->execute(); $p=$st->get_result()->fetch_assoc();
    if($p){ if(isset($_SESSION['carrito'][$id])) $_SESSION['carrito'][$id]['cantidad']++; else $_SESSION['carrito'][$id]=['id'=>$p['id'],'nombre'=>$p['nombre'],'precio'=>(float)$p['precio'],'imagen'=>$p['imagen'],'cantidad'=>1]; }
    header('Location:index.php'); exit;
  }
  if($accion==='quitar_carrito'){
    $id=(int)($_GET['id']??0); unset($_SESSION['carrito'][$id]); header('Location:index.php?accion=carrito'); exit;
  }
  if($accion==='vaciar_carrito'){ $_SESSION['carrito']=[]; header('Location:index.php?accion=carrito'); exit; }
  if($accion==='carrito'){
    $carrito=$_SESSION['carrito']; $total=0; foreach($carrito as $item) $total += $item['precio']*$item['cantidad'];
    require 'views/carrito.php'; exit;
  }
  if($accion==='pagar'){
    $carrito=$_SESSION['carrito']??[];
    if(!$carrito){ header('Location:index.php?accion=carrito'); exit; }
    $total=0; foreach($carrito as $item) $total += $item['precio']*$item['cantidad'];
    require 'views/pago.php'; exit;
  }
  if($accion==='procesar_pago' && $_SERVER['REQUEST_METHOD']==='POST'){
    $carrito=$_SESSION['carrito']??[];
    if(!$carrito){ header('Location:index.php?accion=carrito'); exit; }
    $metodo=trim($_POST['metodo']??'');
    $permitidos=['yape','plin','tarjeta','transferencia','contra_entrega'];
    if(!in_array($metodo,$permitidos,true)){ header('Location:index.php?accion=pagar&error=metodo'); exit; }

    // Descontar el stock de forma segura al confirmar la compra.
    require 'config/conexion.php';
    $db=Conexion::getInstance()->getConn();
    $db->begin_transaction();
    try {
      $total=0;
      $cantidadTotal=0;
      foreach($carrito as $item){
        $id=(int)$item['id'];
        $cantidad=(int)$item['cantidad'];
        if($cantidad<1){ throw new Exception('Cantidad inválida.'); }

        // Bloquea la fila para evitar vender más unidades de las disponibles.
        $st=$db->prepare('SELECT precio, stock, nombre FROM productos WHERE id=? AND publicado=1 FOR UPDATE');
        $st->bind_param('i',$id); $st->execute();
        $producto=$st->get_result()->fetch_assoc();
        if(!$producto || (int)$producto['stock'] < $cantidad){
          throw new Exception('Stock insuficiente para: '.($producto['nombre']??$item['nombre']));
        }

        $upd=$db->prepare('UPDATE productos SET stock=stock-? WHERE id=? AND stock>=?');
        $upd->bind_param('iii',$cantidad,$id,$cantidad);
        if(!$upd->execute() || $upd->affected_rows!==1){
          throw new Exception('No se pudo actualizar el stock.');
        }
        $total += (float)$producto['precio']*$cantidad;
        $cantidadTotal += $cantidad;
      }
      $db->commit();
    } catch(Throwable $e) {
      $db->rollback();
      $_SESSION['error_pago']='No hay suficiente stock disponible para completar la compra. Revisa tu carrito e inténtalo nuevamente.';
      header('Location:index.php?accion=carrito'); exit;
    }

    $_SESSION['ultimo_pedido']=['numero'=>'PED-'.date('YmdHis').'-'.random_int(100,999),'metodo'=>$metodo,'total'=>$total,'cantidad'=>$cantidadTotal];
    $_SESSION['carrito']=[];
    header('Location:index.php?accion=pago_exitoso'); exit;
  }
  if($accion==='pago_exitoso'){
    $pedido=$_SESSION['ultimo_pedido']??null;
    if(!$pedido){ header('Location:index.php?accion=carrito'); exit; }
    require 'views/pago_exitoso.php'; exit;
  }
  if($accion==='contactar'){
    $mensaje_enviado=false;
    if($_SERVER['REQUEST_METHOD']==='POST'){
      $nombre=trim($_POST['nombre']??'');
      $correo=trim($_POST['correo']??'');
      $motivo=trim($_POST['motivo']??'');
      $prioridad=trim($_POST['prioridad']??'Normal');
      $detalle=trim($_POST['detalle']??'');
      if($nombre!=='' && filter_var($correo,FILTER_VALIDATE_EMAIL) && $motivo!=='' && $detalle!==''){
        $mensaje_enviado=true;
      }
    }
    require 'views/contactar_asesor.php'; exit;
  }
}

$rol=$_SESSION['usuario_rol']??'cliente';
if($rol==='cliente'){ require 'views/cliente.php'; exit; }
require 'controllers/ProductoController.php';$c=new ProductoController();
switch($action){case 'crear':$c->crear();break;case 'guardar':$c->guardar();break;case 'editar':$c->editar();break;case 'actualizar':$c->actualizar();break;case 'publicar':$c->publicar();break;case 'eliminar':$c->eliminar();break;default:$c->listar();}
?>
