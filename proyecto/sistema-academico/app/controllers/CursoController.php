<?php
require_once __DIR__ . '/../../config/Database.php';

class CursoController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }
 
    public function getError() { return $this->error; }

    public function listar() {
        try {
            $stmt = $this->pdo->query("SELECT id_curso, nombre_curso FROM cursos ORDER BY id_curso DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function crear($nombre) {
        try {
            $sql = "INSERT INTO cursos (nombre_curso) VALUES (:nombre)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':nombre' => $nombre]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}