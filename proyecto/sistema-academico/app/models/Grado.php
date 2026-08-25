<?php
require_once __DIR__ . "/Model.php";

class Grado extends Model {

    public function listar() {
        $stmt = $this->conexion->query(
            "SELECT id_grado, nombre_grado, seccion FROM grados ORDER BY id_grado ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM grados WHERE id_grado = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre_grado, $seccion) {
        $sql  = "INSERT INTO grados (nombre_grado, seccion) VALUES (:nombre_grado, :seccion)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre_grado", $nombre_grado);
        $stmt->bindParam(":seccion",      $seccion);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre_grado, $seccion) {
        $sql  = "UPDATE grados SET nombre_grado = :nombre_grado, seccion = :seccion WHERE id_grado = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":nombre_grado", $nombre_grado);
        $stmt->bindParam(":seccion",      $seccion);
        $stmt->bindParam(":id",           $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql  = "DELETE FROM grados WHERE id_grado = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
