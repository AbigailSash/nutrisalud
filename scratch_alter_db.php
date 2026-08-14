<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
try {
    $conexion = Conexion::conectar();
    $sql = "ALTER TABLE Paciente ADD COLUMN Contrasena VARCHAR(255) NULL AFTER Email";
    $conexion->exec($sql);
    echo "SUCCESS: Column added.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "SUCCESS: Column already exists.";
    } else {
        echo "ERROR: " . $e->getMessage();
    }
}
?>
