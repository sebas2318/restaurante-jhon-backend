<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/database.php';
require_once '../controladores/PedidoController.php';

$database = new Database();
$db = $database->getConnection();
$pedidoCtrl = new PedidoController($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ver  los pedidos "Pendientes"
    $pedidos = $pedidoCtrl->obtenerPorEstado('Pendiente');
    echo json_encode($pedidos);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['id_pedido'])) {
        // Cambiar estado a Preparado
        if ($pedidoCtrl->actualizarEstado($data['id_pedido'], 'Preparado')) {
            echo json_encode(["message" => "Pedido marcado como listo/preparado."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar el pedido."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Falta el id_pedido."]);
    }
}
?>