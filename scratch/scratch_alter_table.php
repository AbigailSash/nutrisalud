<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "ALTER TABLE Nutricionista 
            ADD COLUMN Especialidad VARCHAR(255) NULL,
            ADD COLUMN Logo_URL VARCHAR(255) NULL,
            ADD COLUMN Instagram VARCHAR(255) NULL,
            ADD COLUMN Whatsapp VARCHAR(255) NULL,
            ADD COLUMN Direccion VARCHAR(255) NULL";
    $pdo->exec($sql);
    echo "Tabla Nutricionista alterada exitosamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
