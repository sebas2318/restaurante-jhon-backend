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
    public function registrar($nombre, $correo, $password, $rol) {

        $queryExistente = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmtExist = $this->db->prepare($queryExistente);
        $stmtExist->bindParam(":email", $correo);
        $stmtExist->execute();

        if($stmtExist->rowCount() > 0) {
            return false; 
        }

        $query = "INSERT INTO " . $this->table . " (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->db->prepare($query);

        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $correo);
        $stmt->bindParam(":password", $password_hashed);
        $stmt->bindParam(":rol", $rol);

        if($stmt->execute()) {
            return true; 
        }
        return false; 
    }

    /**
     * Valida las credenciales del usuario
     */
    public function login($correo, $password) {

        $query = "SELECT id, nombre, password, rol FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $correo);
        $stmt->execute();

        if($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($password, $row['password'])) {

                return [
                    "id" => $row['id'],
                    "nombre" => $row['nombre'],
                    "correo" => $correo,
                    "rol" => $row['rol']
                ];
            }
        }

        return false;
    }
}
?>