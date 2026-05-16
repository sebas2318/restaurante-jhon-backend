<?php
/**
 * @file registro.php
 * @route POST /api/registro.php
 */

// Cabeceras HTTP obligatorias para una API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';
include_once '../controladores/UsuarioController.php';

// Instanciar base de datos y controlador
$database = new Database();
$db = $database->getConnection();
$controller = new UsuarioController($db);

// Leer los datos JSON del cuerpo de la petición (Body)
$data = json_decode(file_get_contents("php://input"));

// Validar que los campos requeridos existan
if(!empty($data->nombre) && !empty($data->email) && !empty($data->password) && !empty($data->rol)) {
    
    $resultado = $controller->registrar($data->nombre, $data->email, $data->password, $data->rol);
    
    if($resultado['status']) {
        http_response_code(201); // Código 201: Creado
        echo json_encode(["mensaje" => $resultado['message']]);
    } else {
        http_response_code(400); // Código 400: Solicitud incorrecta
        echo json_encode(["error" => $resultado['message']]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "No se pudo registrar el usuario. Datos incompletos."]);
}
?>