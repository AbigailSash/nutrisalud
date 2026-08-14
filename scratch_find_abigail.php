<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();
$stmt = $pdo->query("SELECT * FROM Paciente WHERE Nombre LIKE '%Abigail%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
