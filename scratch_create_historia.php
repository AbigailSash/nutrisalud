<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "CREATE TABLE IF NOT EXISTS Historia_Clinica (
        IdHistoria INT AUTO_INCREMENT PRIMARY KEY,
        IdPaciente INT NOT NULL,
        IdNutri INT NOT NULL,
        FechaUltimaModificacion DATETIME NOT NULL,
        Datos_JSON MEDIUMTEXT NOT NULL,
        UNIQUE KEY(IdPaciente)
    )";
    $pdo->exec($sql);
    echo "Tabla Historia_Clinica creada exitosamente.";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}
?>
