<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../controladores/PedidoController.php';

$database = new Database();
$db = $database->getConnection();
$pedidoCtrl = new PedidoController($db);

$method = $_SERVER['REQUEST_METHOD'];
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($method === 'POST') {
    // Crear un pedido
    if (!empty($data['id_usuario']) && !empty($data['detalles']) && isset($data['total'])) {
        $detallesString = is_array($data['detalles']) ? json_encode($data['detalles']) : $data['detalles'];
        $id_pedido = $pedidoCtrl->crear($data['id_usuario'], $detallesString, $data['total']);
        
        if ($id_pedido) {
            http_response_code(201);
            echo json_encode(["message" => "Pedido creado exitosamente.", "id_pedido" => $id_pedido]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo registrar el pedido."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos para crear el pedido."]);
    }
} elseif ($method === 'GET') {
    $estado = $_GET['estado'] ?? 'Pendiente';
    $pedidos = $pedidoCtrl->obtenerPorEstado($estado);
    echo json_encode($pedidos);
}
?>