<?php
require_once __DIR__.'/../config/conexion.php';
class Producto {
 private function db(){return Conexion::getInstance()->getConn();}
 function listar(){return $this->db()->query('SELECT * FROM productos ORDER BY id DESC');}
 function obtener($id){$st=$this->db()->prepare('SELECT * FROM productos WHERE id=?');$st->bind_param('i',$id);$st->execute();return $st->get_result()->fetch_assoc();}
 function guardar($d){$st=$this->db()->prepare('INSERT INTO productos(nombre,categoria,precio,stock,imagen,descripcion,publicado) VALUES(?,?,?,?,?,?,?)');$publicado=isset($d['publicado'])?1:0; $st->bind_param('ssdissi',$d['nombre'],$d['categoria'],$d['precio'],$d['stock'],$d['imagen'],$d['descripcion'],$publicado);return $st->execute();}
 function actualizar($d){$st=$this->db()->prepare('UPDATE productos SET nombre=?,categoria=?,precio=?,stock=?,imagen=?,descripcion=?,publicado=? WHERE id=?');$publicado=isset($d['publicado'])?1:0; $st->bind_param('ssdissii',$d['nombre'],$d['categoria'],$d['precio'],$d['stock'],$d['imagen'],$d['descripcion'],$publicado,$d['id']);return $st->execute();}
 function publicar($id,$estado){$st=$this->db()->prepare('UPDATE productos SET publicado=? WHERE id=?');$st->bind_param('ii',$estado,$id);return $st->execute();}
 function eliminar($id){$st=$this->db()->prepare('DELETE FROM productos WHERE id=?');$st->bind_param('i',$id);return $st->execute();}
}
?>
