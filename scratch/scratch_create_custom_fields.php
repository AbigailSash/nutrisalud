<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    $sql = "CREATE TABLE IF NOT EXISTS historia_clinica_campos_custom (
        id INT AUTO_INCREMENT PRIMARY KEY,
        paciente_id INT NOT NULL,
        seccion VARCHAR(50) NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        contenido TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (paciente_id) REFERENCES Paciente(IdPaciente) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "Tabla historia_clinica_campos_custom creada exitosamente.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
