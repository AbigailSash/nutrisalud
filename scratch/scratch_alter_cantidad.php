<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "ALTER TABLE Detalle_Plan_Alimento MODIFY Cantidad_Gramos VARCHAR(100) NULL";
    $pdo->exec($sql);
    
    // Y debemos actualizar la vista Vista_Menu_Paciente
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
    echo "Base de datos actualizada.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
