<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();
$stmt = $pdo->query('SELECT COUNT(*) FROM alimento');
echo $stmt->fetchColumn();
?>
