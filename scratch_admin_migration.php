<?php
require_once 'config/config.php';
require_once 'config/Conexion.php';

try {
    $pdo = Conexion::conectar();
    
    // Check if Rol exists
    $stmt = $pdo->query("SHOW COLUMNS FROM Nutricionista LIKE 'Rol'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE Nutricionista ADD COLUMN Rol ENUM('admin', 'nutricionista') DEFAULT 'nutricionista' AFTER Password_Hash");
        echo "Columna Rol agregada.\n";
    }

    // Insert Default SuperAdmin
    $dniAdmin = '123456789';
    $emailAdmin = 'admin@admin.com';
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $matricula = 'ADMIN-01';

    $stmt = $pdo->prepare("SELECT IdNutri FROM Nutricionista WHERE DNI = ? OR Email = ?");
    $stmt->execute([$dniAdmin, $emailAdmin]);
    if ($stmt->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO Nutricionista (DNI, Matricula, Nombre, Apellido, Email, Password_Hash, Rol, Estado_Cuenta) VALUES (?, ?, 'Super', 'Admin', ?, ?, 'admin', 'A')");
        $insert->execute([$dniAdmin, $matricula, $emailAdmin, $password]);
        echo "SuperAdmin creado. DNI: 123456789, Pass: admin123\n";
    } else {
        echo "SuperAdmin ya existe.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
