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
    
    if (!empty($_POST['nombre']) && !empty($_POST['correo']) && !empty($_POST['contrasena']) && !empty($_POST['rol'])) {
        
        $database = new Database();
        $db = $database->getConnection();
        
        $usuarioController = new UsuarioController($db);
        
        // Asignar los 4 valores recibidos
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena'];
        $rol = $_POST['rol']; 
        
        if ($usuarioController->registrar($nombre, $correo, $contrasena, $rol)) {
            http_response_code(201);
            echo json_encode(["message" => "Usuario registrado exitosamente."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "No se pudo registrar el usuario. El correo ya existe o hubo un error en el servidor."]);
        }
        
    } else {
        http_response_code(400);
        echo json_encode(["error" => "No se pudo registrar el usuario. Datos incompletos (Falta nombre, correo, contrasena o rol)."]);
    }
}
?>