<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
try {
    $pdo = Conexion::conectar();
    // Add Password and FotoPerfil if they don't exist
    $sql1 = "ALTER TABLE Paciente ADD COLUMN Password VARCHAR(255) NULL DEFAULT NULL";
    $sql2 = "ALTER TABLE Paciente ADD COLUMN FotoPerfil VARCHAR(255) NULL DEFAULT NULL";
    
    try { $pdo->exec($sql1); echo "Added Password. "; } catch(Exception $e) { echo "Password exists. "; }
    try { $pdo->exec($sql2); echo "Added FotoPerfil. "; } catch(Exception $e) { echo "FotoPerfil exists. "; }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
