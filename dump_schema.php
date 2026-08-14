<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
$pdo = Conexion::conectar();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $t) {
    echo $pdo->query('SHOW CREATE TABLE '.$t)->fetchColumn(1)."\n\n";
}
?>
