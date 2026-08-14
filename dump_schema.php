<?php
require 'config/Conexion.php';
$pdo = Conexion::conectar();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach($tables as $t) {
    echo $pdo->query('SHOW CREATE TABLE '.$t)->fetchColumn(1)."\n\n";
}
?>
