<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();
$stmt = $pdo->query('SHOW COLUMNS FROM dia_semana');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
