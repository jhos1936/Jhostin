<?php
require_once __DIR__ . '/../../config/Database.php';

class GradoController {
    private $pdo;
    private $error = "";

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function listar() {
        try {
            $stmt = $this->pdo->query("SELECT id_grado, nombre_grado, seccion FROM grados ORDER BY id_grado ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return [];
        }
    }

    public function eliminar($id) {
        try {
            $sql = "DELETE FROM grados WHERE id_grado = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
}
