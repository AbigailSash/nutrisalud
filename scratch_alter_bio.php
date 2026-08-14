<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "ALTER TABLE Nutricionista ADD COLUMN Biografia TEXT NULL";
    $pdo->exec($sql);
    echo "Columna Biografia agregada exitosamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
