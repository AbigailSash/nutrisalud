<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    $sql = "CREATE TABLE IF NOT EXISTS Plan_Informe_Educativo (
        IdPlan INT NOT NULL,
        IdInforme INT NOT NULL,
        PRIMARY KEY (IdPlan, IdInforme),
        FOREIGN KEY (IdPlan) REFERENCES Plan_Alimentario(IdPlan) ON DELETE CASCADE,
        FOREIGN KEY (IdInforme) REFERENCES Informe_Educativo(IdInforme) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "Tabla Plan_Informe_Educativo creada correctamente.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
