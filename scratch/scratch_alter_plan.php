<?php
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    $sql = "ALTER TABLE Plan_Alimentario ADD COLUMN Estado_Plan VARCHAR(20) NOT NULL DEFAULT 'Activo'";
    $pdo->exec($sql);
    echo "Columna Estado_Plan agregada con éxito a Plan_Alimentario.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "La columna ya existe.\n";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
