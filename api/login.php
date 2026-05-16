<?php
/**
 * @file login.php
 * @route POST /api/login.php
 */

// Cabeceras HTTP obligatorias para una API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';
include_once '../controladores/UsuarioController.php';

$database = new Database();
$db = $database->getConnection();
$controller = new UsuarioController($db);

// Capturar el cuerpo de la petición POST
$data = json_decode(file_get_contents("php://input"));

// Validar parámetros requeridos por la guía (usuario/email y contraseña)
if(!empty($data->email) && !empty($data->password)) {
    
    $autenticacion = $controller->login($data->email, $data->password);
    
    if($autenticacion['status']) {
        http_response_code(200); // Código 200: Éxito
        // Mensaje de autenticación satisfactoria requerido por la guía
        echo json_encode([
            "success" => true,
            "mensaje" => $autenticacion['message'],
            "usuario" => $autenticacion['usuario']
        ]);
    } else {
        http_response_code(401); // Código 401: No autorizado
        // Mensaje de error en la autenticación requerido por la guía
        echo json_encode([
            "success" => false,
            "error" => $autenticacion['message']
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Por favor provea un email y una contraseña."]);
}
?>