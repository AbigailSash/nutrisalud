<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "CREATE TABLE IF NOT EXISTS Informe_Educativo (
        IdInforme INT AUTO_INCREMENT PRIMARY KEY,
        IdPaciente INT NOT NULL,
        IdNutri INT NOT NULL,
        Fecha DATE NOT NULL,
        Contenido_JSON TEXT NOT NULL
    )";
    $pdo->exec($sql);
    echo "Tabla Informe_Educativo creada exitosamente.";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}
?>
