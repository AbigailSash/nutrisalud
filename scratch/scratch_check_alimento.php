<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();
$stmt = $pdo->query('SHOW COLUMNS FROM alimento');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
