<?php
require_once __DIR__ . "/../../config/Database.php";

class Model {
    protected $conexion;

    public function __construct() {
        $database = new Database();
        $this->conexion = $database->conectar();
    }
}
