<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    // Check and add missing columns to Paciente table
    $columnsToAdd = [
        "ADD COLUMN Peso DECIMAL(5,2) NULL",
        "ADD COLUMN Estatura INT NULL",
        "ADD COLUMN Sexo VARCHAR(10) NULL DEFAULT 'M'",
        "ADD COLUMN Actividad DECIMAL(4,3) NULL DEFAULT 1.200"
    ];

    foreach ($columnsToAdd as $col) {
        try {
            $sql = "ALTER TABLE Paciente " . $col;
            $pdo->exec($sql);
            echo "Successfully executed: " . $sql . "\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Column already exists: " . $col . "\n";
            } else {
                echo "Error executing $col: " . $e->getMessage() . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Connection error: " . $e->getMessage() . "\n";
}
?>
