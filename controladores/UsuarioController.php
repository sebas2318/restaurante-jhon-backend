<?php
/**
 * @file UsuarioController.php
 * @description Maneja la lógica de registro y autenticación cifrada.
 */

class UsuarioController {
    private $db;
    private $table = "usuarios";

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Registra un nuevo usuario aplicando encriptación de contraseña (Estándar de seguridad RNF04)
     */
    public function registrar($nombre, $email, $password, $rol) {
        // Validar si el email ya existe en el sistema
        $queryExistente = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmtExist = $this->db->prepare($queryExistente);
        $stmtExist->bindParam(":email", $email);
        $stmtExist->execute();

        if($stmtExist->rowCount() > 0) {
            return ["status" => false, "message" => "El correo electrónico ya está registrado."];
        }

        // Preparar inserción de la entidad Usuario (Clase Usuario)
        $query = "INSERT INTO " . $this->table . " (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->db->prepare($query);

        // Encriptar la contraseña mediante BCRYPT antes de guardarla
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Vincular parámetros
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hashed);
        $stmt->bindParam(":rol", $rol);

        if($stmt->execute()) {
            return ["status" => true, "message" => "Usuario registrado exitosamente."];
        }
        return ["status" => false, "message" => "Error interno al intentar registrar al usuario."];
    }

    /**
     * Valida las credenciales del usuario (Inicio de Sesión)
     */
    public function login($email, $password) {
        // Buscar el usuario por email
        $query = "SELECT id, nombre, password, rol FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si la contraseña ingresada coincide con la encriptada en BD
            if(password_verify($password, $row['password'])) {
                return [
                    "status" => true,
                    "message" => "Autenticación satisfactoria.",
                    "usuario" => [
                        "id" => $row['id'],
                        "nombre" => $row['nombre'],
                        "rol" => $row['rol']
                    ]
                ];
            }
        }
        // Retorno unificado si el correo o la contraseña fallan (Seguridad de API)
        return ["status" => false, "message" => "Error en la autenticación. Credenciales inválidas."];
    }
}
?>