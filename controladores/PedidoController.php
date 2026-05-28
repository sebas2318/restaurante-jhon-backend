<?php
class PedidoController {
    private $db;
    private $table = "pedidos";

    public function __construct($db) {
        $this->db = $db;
    }

    public function crear($id_usuario, $detalles, $total) {
        $query = "INSERT INTO " . $this->table . " (id_usuario, detalles, total, estado) VALUES (:id_usuario, :detalles, :total, 'Pendiente')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->bindParam(":detalles", $detalles);
        $stmt->bindParam(":total", $total);

        if($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function obtenerPorEstado($estado) {
        $query = "SELECT p.*, u.nombre as mesero FROM " . $this->table . " p 
                  LEFT JOIN usuarios u ON p.id_usuario = u.id 
                  WHERE p.estado = :estado ORDER BY p.fecha_creacion DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstado($id, $nuevo_estado) {
        if ($nuevo_estado === 'Preparado') {
            $this->descontarStockDelPedido($id);
        }

        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    private function descontarStockDelPedido($id_pedido) {
        $query = "SELECT detalles FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id_pedido);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido && !empty($pedido['detalles'])) {
            $articulos = explode(', ', $pedido['detalles']);

            foreach ($articulos as $articulo) {
                $partes = explode('x ', $articulo);
                if (count($partes) == 2) {
                    $cantidad = (int)$partes[0];
                    $nombre_producto = trim($partes[1]);

                    $queryInventario = "UPDATE inventario SET cantidad = cantidad - :cantidad 
                                        WHERE nombre = :nombre AND cantidad >= :cantidad";
                    $stmtInv = $this->db->prepare($queryInventario);
                    $stmtInv->bindParam(":cantidad", $cantidad);
                    $stmtInv->bindParam(":nombre", $nombre_producto);
                    $stmtInv->execute();
                }
            }
        }
    }
}
?>