<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../config/database.php';
require_once '../controladores/InventarioController.php';

$database = new Database();
$db = $database->getConnection();
$inventarioCtrl = new InventarioController($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    echo json_encode($inventarioCtrl->listar());
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!empty($data['id']) && isset($data['cantidad'])) {
        $operacion = $data['operacion'] ?? 'establecer'; 
        
        if ($inventarioCtrl->modificarStock($data['id'], $data['cantidad'], $operacion)) {
            echo json_encode(["message" => "Inventario actualizado con éxito."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo actualizar el stock. Verifique fondos/cantidades."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos insuficientes (id, cantidad)."]);
    }
}
?>