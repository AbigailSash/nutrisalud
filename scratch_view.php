<?php
require 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    
    // Crear la vista si no existe
    $sql = "CREATE OR REPLACE VIEW Vista_Menu_Paciente AS
            SELECT 
                dp.IdPlan,
                dp.IdDetalle,
                d.IdDia,
                d.Nombre_Dia,
                m.IdMomento,
                m.Nombre_Momento AS Momento_Comida,
                '08:00 AM' AS Hora_Recomendada, /* Mock, se puede quitar o hacer dinámico */
                a.IdAlimento,
                a.Nombre_Alimento AS Alimento,
                dp.Cantidad_Gramos AS Cantidad,
                dp.Indicaciones_Especiales
            FROM Detalle_Plan_Alimento dp
            JOIN Dia_Semana d ON dp.IdDia = d.IdDia
            JOIN Momento_Dia m ON dp.IdMomento = m.IdMomento
            JOIN Alimento a ON dp.IdAlimento = a.IdAlimento";
            
    $pdo->exec($sql);
    echo "Vista creada con éxito.\n";
    
    // Insertar datos básicos en catálogos si están vacíos
    $dias = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
    foreach($dias as $i => $dia) {
        $pdo->exec("INSERT IGNORE INTO dia_semana (IdDia, Nombre_Dia) VALUES (".($i+1).", '$dia')");
    }
    
    $momentos = ['Desayuno', 'Almuerzo', 'Merienda', 'Cena'];
    foreach($momentos as $i => $mom) {
        $pdo->exec("INSERT IGNORE INTO momento_dia (IdMomento, Nombre_Momento) VALUES (".($i+1).", '$mom')");
    }
    
    // Insertar un par de alimentos
    $pdo->exec("INSERT IGNORE INTO alimento (IdAlimento, Nombre_Alimento, Calorias_100g, Proteinas_100g, Carbohidratos_100g, Grasas_100g) VALUES 
        (1, 'Avena', 389, 16.9, 66.3, 6.9),
        (2, 'Pollo (Pechuga)', 165, 31, 0, 3.6),
        (3, 'Manzana', 52, 0.3, 14, 0.2)");
    
    echo "Catálogos poblados con éxito.\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
