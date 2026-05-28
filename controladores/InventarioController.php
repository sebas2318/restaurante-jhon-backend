<?php
class InventarioController {
    private $db;
    private $table = "inventario";

    public function __construct($db) {
        $this->db = $db;
    }

    // Listar el inventario
    public function listar() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar la cantidad de stock
    public function modificarStock($id, $cantidad, $operacion = 'establecer') {
        if ($operacion === 'restar') {
            $query = "UPDATE " . $this->table . " SET cantidad = cantidad - :cantidad WHERE id = :id AND cantidad >= :cantidad";
        } elseif ($operacion === 'sumar') {
            $query = "UPDATE " . $this->table . " SET cantidad = cantidad + :cantidad WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . " SET cantidad = :cantidad WHERE id = :id";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>