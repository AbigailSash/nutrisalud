<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';
try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->query("SELECT IdPaciente, DNI FROM Paciente WHERE Password IS NULL OR Password = ''");
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pacientes as $p) {
        $hash = password_hash($p['DNI'], PASSWORD_DEFAULT);
        $update = $conexion->prepare("UPDATE Paciente SET Password = ? WHERE IdPaciente = ?");
        $update->execute([$hash, $p['IdPaciente']]);
    }
    echo "Updated " . count($pacientes) . " patients with default passwords.";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
