<?php
require 'config/config.php';
require 'config/Conexion.php';
$pdo = Conexion::conectar();
try {
    $stmt = $pdo->prepare("INSERT INTO Nutricionista (DNI, Nombre, Apellido, Email, Password_Hash, Matricula, Rol, Estado_Cuenta) VALUES ('111', 'Test', 'Test', 'test@test.com', '123', 'MAT123', 'nutricionista', 'A')");
    $stmt->execute();
    echo 'OK';
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
