<?php
// config/Conexion.php
// Archivo de conexión segura a MySQL utilizando PDO con soporte completo utf8mb4 (emojis y caracteres especiales).

class Conexion {
    public static function conectar() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '-03:00'"
            ];
            
            $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            return $conexion;
            
        } catch(PDOException $e) {
            die("Error crítico de Conexión a Base de Datos: " . $e->getMessage());
        }
    }
}
?>
