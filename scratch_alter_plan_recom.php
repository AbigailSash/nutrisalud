<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    $pdo->exec("ALTER TABLE Plan_Alimentario ADD COLUMN Recomendaciones TEXT NULL AFTER Objetivo");
    echo "Columna Recomendaciones agregada.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
