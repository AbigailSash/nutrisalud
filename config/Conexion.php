<?php
// config/Conexion.php
// Archivo de conexión segura a MySQL utilizando PDO.

class Conexion {
    /**
     * Retorna una instancia segura de PDO conectada a la base de datos.
     * Patrón Singleton / Factory Method simplificado.
     */
    public static function conectar() {
        try {
            // Construimos el DSN (Data Source Name) asegurando el charset utf8
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
            
            // Opciones críticas de seguridad y manejo de errores para PDO
            $opciones = [
                // Lanzar excepciones (fatal errors capturables) cuando ocurra un error SQL
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Las consultas devolverán por defecto un Array Asociativo limpio
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Apagamos la emulación de sentencias preparadas para forzar al motor MySQL 
                // a prepararlas, evitando al 100% las Inyecciones SQL de segundo orden.
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Aseguramos que la DB opere en la misma zona horaria
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-03:00'"
            ];
            
            // Instanciamos y devolvemos la conexión
            $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            return $conexion;
            
        } catch(PDOException $e) {
            // En caso de fallo crítico, detenemos la ejecución.
            // NOTA: En un entorno de producción real, esto debería ir a un archivo de logs.
            die("Error crítico de Conexión a Base de Datos: " . $e->getMessage());
        }
    }
}
?>
