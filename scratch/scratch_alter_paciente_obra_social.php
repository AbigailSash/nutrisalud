<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    // Add Obra_Social to Paciente
    $sql = "ALTER TABLE Paciente ADD COLUMN Obra_Social VARCHAR(100) NULL DEFAULT 'Particular'";
    $pdo->exec($sql);
    
    echo "Added Obra_Social to Paciente.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column Obra_Social already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
