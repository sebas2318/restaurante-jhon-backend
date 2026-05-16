<?php
/**
 * @file database.php
 * @description Configuración y conexión segura a MySQL mediante PDO.
 * @author Omar Sebastián Trujillo Pérez
 */

class Database {
    private $host = "localhost";
    private $db_name = "restaurante_jhon";
    private $username = "root"; // Cambiar según tu configuración local
    private $password = "";     // Cambiar según tu configuración local
    public $conn;

    // Obtener la conexión a la base de datos
    public function getConnection() {
        $this->conn = null;

        try {
            // Configuración de la conexión PDO con codificación UTF-8
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Si hay error, devuelve el fallo en formato JSON
            echo json_encode(["error" => "Error de conexión: " . $exception->getMessage()]);
            exit;
        }

        return $this->conn;
    }
}
?>