<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data !== null) {
    foreach ($data as $key => $value) {
        $_POST[$key] = $value;
    }
}

require_once '../config/database.php';
require_once '../controladores/UsuarioController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!empty($_POST['correo']) && !empty($_POST['contrasena'])) {
        
        $database = new Database();
        $db = $database->getConnection();
        
        $usuarioController = new UsuarioController($db);
        
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena'];
        
        $usuario = $usuarioController->login($correo, $contrasena);
        
        if ($usuario) {
            http_response_code(200);
        
            echo json_encode([
                "message" => "Inicio de sesión exitoso.",
                "usuario" => [
                    "id" => $usuario['id'] ?? $usuario['id_usuario'] ?? $usuario['ID'] ?? null,
                    "nombre" => $usuario['nombre'] ?? $usuario['nombre_completo'] ?? $usuario['Nombre'] ?? null,
                    "correo" => $usuario['correo'] ?? $usuario['email'] ?? $usuario['Correo'] ?? null,
                    "rol" => $usuario['rol'] ?? $usuario['id_rol'] ?? null
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Credenciales incorrectas. Verifique su correo y contraseña."]);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Por favor provea un email y una contraseña."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Utilice POST."]);
}
?>