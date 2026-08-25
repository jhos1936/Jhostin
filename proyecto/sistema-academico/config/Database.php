<?php
class Database {
    // Credenciales cargadas desde variables de entorno.
    // Copia config.env.example a config.env (o define estas variables en tu servidor)
    // y NUNCA subas credenciales reales a un repositorio publico.
    private $host   = "";
    private $dbname = "";
    private $user   = "";
    private $pass   = "";

    public function __construct() {
        $this->host   = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'sistema_academico';
        $this->user   = getenv('DB_USER') ?: 'root';
        $this->pass   = getenv('DB_PASS') ?: '';
    }

    public function conectar() {
        try {
            $conexion = new PDO(
                "mysql:host=" . $this->host . ";port=3306;dbname=" . $this->dbname . ";charset=utf8",
                $this->user,
                $this->pass
            );
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexion: " . $e->getMessage());
        }
    }
}
