<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
$pdo = Conexion::conectar();
$stmt = $pdo->query('SHOW COLUMNS FROM Nutricionista');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
