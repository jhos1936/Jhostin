<?php
mysqli_report(MYSQLI_REPORT_OFF);
define('DB_HOST','localhost'); define('DB_USER','root'); define('DB_PASS',''); define('DB_NAME','tienda_tecnologica');
class Conexion {
 private static $instancia=null; private $conn;
 private function __construct(){
  $server=new mysqli(DB_HOST,DB_USER,DB_PASS); if($server->connect_error) die('No se pudo conectar a MySQL: '.$server->connect_error);
  $server->set_charset('utf8mb4'); $server->query("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); $server->close();
  $this->conn=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME); if($this->conn->connect_error) die('No se pudo abrir la base de datos: '.$this->conn->connect_error); $this->conn->set_charset('utf8mb4'); $this->init();
 }
 private function init(){
  $this->conn->query("CREATE TABLE IF NOT EXISTS usuarios(id INT AUTO_INCREMENT PRIMARY KEY,nombre VARCHAR(100) NOT NULL,email VARCHAR(150) UNIQUE NOT NULL,password VARCHAR(255) NOT NULL,rol ENUM('admin','asesor','cliente') NOT NULL DEFAULT 'cliente')");
  $this->conn->query("CREATE TABLE IF NOT EXISTS productos(id INT AUTO_INCREMENT PRIMARY KEY,nombre VARCHAR(150) NOT NULL,categoria VARCHAR(80) NOT NULL,precio DECIMAL(10,2) NOT NULL,stock INT NOT NULL DEFAULT 0,imagen VARCHAR(255) DEFAULT '',descripcion TEXT,publicado TINYINT(1) NOT NULL DEFAULT 1,creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
  $col=$this->conn->query("SHOW COLUMNS FROM productos LIKE 'publicado'"); if($col && $col->num_rows===0){$this->conn->query("ALTER TABLE productos ADD publicado TINYINT(1) NOT NULL DEFAULT 1");}
  $r=$this->conn->query('SELECT COUNT(*) c FROM productos');$n=$r?$r->fetch_assoc()['c']:0;
  if((int)$n===0){$items=[['Laptop HP 15.6" Core i5','Laptops',2299,8,'💻','Potente laptop para estudio y trabajo.'],['Samsung Galaxy A32','Celulares',899,15,'📱','Smartphone con excelente pantalla y cámara.'],['PC Gamer RGB','Computadoras',3499,5,'🖥️','Equipo gamer con iluminación RGB.'],['Smart TV 50" 4K','Televisores',1799,7,'📺','Televisor 4K para entretenimiento en casa.'],['Monitor LG 24"','Monitores',599,12,'🖥️','Monitor Full HD para oficina y gaming.'],['Monitor Samsung 27" Curvo','Monitores',999,9,'🖥️','Monitor curvo de 27 pulgadas.']];$st=$this->conn->prepare('INSERT INTO productos(nombre,categoria,precio,stock,imagen,descripcion) VALUES(?,?,?,?,?,?)');foreach($items as $x){$st->bind_param('ssdiss',$x[0],$x[1],$x[2],$x[3],$x[4],$x[5]);$st->execute();}}
 }
 public static function getInstance(){if(self::$instancia===null)self::$instancia=new Conexion();return self::$instancia;} public function getConn(){return $this->conn;}
}
?>
