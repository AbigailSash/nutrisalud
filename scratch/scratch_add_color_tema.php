<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';

$pdo = Conexion::conectar();
$cols = $pdo->query("SHOW COLUMNS FROM nutricionista")->fetchAll(PDO::FETCH_COLUMN);

echo "Columnas actuales: " . implode(', ', $cols) . "\n";

if (!in_array('Color_Tema', $cols)) {
    $pdo->exec("ALTER TABLE nutricionista ADD COLUMN Color_Tema VARCHAR(20) DEFAULT '#2ecc71'");
    echo "Columna Color_Tema añadida exitosamente a tabla nutricionista.\n";
} else {
    echo "Columna Color_Tema ya existe.\n";
}
