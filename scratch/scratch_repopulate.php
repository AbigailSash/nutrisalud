<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    $pdo->exec("TRUNCATE TABLE dia_semana");
    $pdo->exec("INSERT INTO dia_semana (IdDia, Nombre_Dia) VALUES 
        (1, 'Lunes'), (2, 'Martes'), (3, 'Miércoles'), (4, 'Jueves'), (5, 'Viernes'), (6, 'Sábado'), (7, 'Domingo')");

    $pdo->exec("TRUNCATE TABLE momento_dia");
    $pdo->exec("INSERT INTO momento_dia (IdMomento, Nombre_Momento) VALUES 
        (1, 'Desayuno'), (2, 'Media Mañana'), (3, 'Almuerzo'), (4, 'Merienda'), (5, 'Cena')");

    $pdo->exec("TRUNCATE TABLE alimento");
    $pdo->exec("INSERT INTO alimento (IdAlimento, Nombre_Alimento, Calorias_100g, Proteinas_100g, Carbohidratos_100g, Grasas_100g) VALUES 
        (1, 'Manzana', 52, 0.3, 14, 0.2),
        (2, 'Pechuga de pollo', 165, 31, 0, 3.6),
        (3, 'Arroz blanco cocido', 130, 2.7, 28, 0.3),
        (4, 'Avena', 389, 16.9, 66.3, 6.9),
        (5, 'Huevo cocido', 155, 13, 1.1, 11)");
        
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "Catálogos repoblados con éxito.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
