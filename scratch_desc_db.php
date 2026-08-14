<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->query("DESCRIBE Paciente");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
