<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    // Desactivar restricciones de clave foránea temporalmente
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Obtener todas las tablas
    $stmt = $pdo->query('SHOW TABLES');
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tablas as $tabla) {
        // No truncar tablas maestras o diccionarios que sean esenciales, pero si no hay, vaciamos.
        // Asumiendo que Alimento, Dia_Semana, Momento_Dia son catálogos, no los truncamos.
        if (in_array($tabla, ['Alimento', 'Dia_Semana', 'Momento_Dia'])) {
            continue;
        }

        // Si es Nutricionista, eliminamos todos menos el admin
        if (strtolower($tabla) === 'nutricionista') {
            $pdo->exec("DELETE FROM Nutricionista WHERE Rol != 'admin'");
            echo "Tabla $tabla limpiada (se conservó el admin).\n";
        } else {
            // Truncar las demás
            $pdo->exec("TRUNCATE TABLE `$tabla`");
            echo "Tabla $tabla truncada.\n";
        }
    }
    
    // Reactivar restricciones de clave foránea
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "\nLimpieza completada con éxito.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
