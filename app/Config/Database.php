<?php
class Conexion {
    // Atributos de configuración
    private $host = "localhost";
    private $db_name = "NutriSalud";
    private $usuario = "root";      // Por defecto en XAMPP es root
    private $password = "";         // Por defecto en XAMPP está vacío

    public $conn;

    // Método que realiza la conexión
    public function conectar() {
        $this->conn = null;

        try {
            // Instancia de PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                $this->usuario, 
                $this->password
            );
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

        } catch(PDOException $exception) {
            echo "Error crítico de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
