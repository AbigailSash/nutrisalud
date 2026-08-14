<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "ALTER TABLE Detalle_Plan_Alimento MODIFY IdAlimento INT NULL, ADD COLUMN Alimento_Personalizado VARCHAR(255) NULL";
    $pdo->exec($sql);
    
    // Y necesitamos actualizar la vista Vista_Menu_Paciente para que lea Alimento_Personalizado o el nombre del Alimento
    $sqlVista = "
    CREATE OR REPLACE VIEW Vista_Menu_Paciente AS
    SELECT 
        dp.IdDetalle,
        dp.IdPlan,
        dp.IdDia,
        dp.IdMomento,
        m.Nombre_Momento AS Momento_Comida,
        COALESCE(dp.Alimento_Personalizado, a.Nombre_Alimento) AS Alimento,
        dp.Cantidad_Gramos AS Cantidad,
        dp.Indicaciones_Especiales
    FROM Detalle_Plan_Alimento dp
    LEFT JOIN Alimento a ON dp.IdAlimento = a.IdAlimento
    JOIN Momento_Dia m ON dp.IdMomento = m.IdMomento
    ";
    $pdo->exec($sqlVista);
    
    echo "Base de datos actualizada para alimentos personalizados.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
