<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';

$pdo = Conexion::conectar();
echo "Conectado a la base de datos...\n";

// Convertir base de datos a utf8mb4
$pdo->exec("ALTER DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
echo "Base de datos convertida a utf8mb4.\n";

// Obtener todas las tablas
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $tbl) {
    echo "Convirtiendo tabla: $tbl...\n";
    try {
        $pdo->exec("ALTER TABLE `$tbl` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo " -> OK\n";
    } catch (Exception $e) {
        echo " -> Error en $tbl: " . $e->getMessage() . "\n";
    }
}

echo "Conversión finalizada exitosamente.\n";
