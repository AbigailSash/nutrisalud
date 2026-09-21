<?php
// database/reset_admin.php
// Script de restablecimiento / aseguramiento de cuenta Administrador Maestro
// Uso CLI: php database/reset_admin.php
// Uso Web: https://tudominio.com/database/reset_admin.php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Conexion.php';

$is_cli = php_sapi_name() === 'cli';

try {
    $db = Conexion::conectar();
    
    // Contraseña por defecto: admin123
    $passwordPlana = 'admin123';
    $passwordHash = password_hash($passwordPlana, PASSWORD_BCRYPT);
    $emailAdmin = 'admin@nutrisalud.com';
    $dniAdmin = '00000000';
    $matriculaAdmin = 'MN-ADMIN-01';

    // 1. Verificar si ya existe un usuario con email admin o rol admin
    $stmt = $db->prepare("SELECT * FROM `nutricionista` WHERE `Email` = :email OR `Rol` = 'admin' LIMIT 1");
    $stmt->execute([':email' => $emailAdmin]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Actualizar contraseña y asegurar estado activo y rol admin
        $stmtUp = $db->prepare("UPDATE `nutricionista` 
            SET `Password_Hash` = :hash, `Rol` = 'admin', `Estado_Cuenta` = 'A', `Email` = :email 
            WHERE `IdNutri` = :id");
        $stmtUp->execute([
            ':hash'  => $passwordHash,
            ':email' => $emailAdmin,
            ':id'    => $admin['IdNutri']
        ]);
        $mensaje = "Cuenta de Administrador actualizada y restablecida con éxito (IdNutri: {$admin['IdNutri']}).";
    } else {
        // Insertar nuevo SuperAdmin
        $stmtIn = $db->prepare("INSERT INTO `nutricionista` 
            (`DNI`, `Matricula`, `Nombre`, `Apellido`, `Email`, `Password_Hash`, `Rol`, `Telefono`, `Estado_Cuenta`, `Especialidad`, `Direccion`, `Biografia`)
            VALUES (:dni, :mat, 'Administrador', 'NutriSalud', :email, :hash, 'admin', '1100000000', 'A', 'Administrador de Plataforma', 'Sede Central NutriSalud', 'Cuenta maestra de administración del sistema NutriSalud SaaS')");
        $stmtIn->execute([
            ':dni'   => $dniAdmin,
            ':mat'   => $matriculaAdmin,
            ':email' => $emailAdmin,
            ':hash'  => $passwordHash
        ]);
        $mensaje = "Cuenta de Administrador creada por primera vez.";
    }

    if ($is_cli) {
        echo "✅ " . $mensaje . PHP_EOL;
        echo "Credenciales de Acceso:" . PHP_EOL;
        echo "  - Usuario / Identificador: admin@nutrisalud.com  (o simplemente 'admin')" . PHP_EOL;
        echo "  - Contraseña: $passwordPlana" . PHP_EOL;
        echo "  - URL de Login: " . (defined('BASE_URL') ? BASE_URL : '') . "index.php?action=login_nutri" . PHP_EOL;
        exit(0);
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Restablecimiento de SuperAdmin - NutriSalud</title>
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #0f172a; padding: 40px 20px; }
                .card { max-width: 550px; margin: 0 auto; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 35px; border-top: 5px solid #2ecc71; }
                .btn { display: inline-block; background: #2ecc71; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 20px; }
                code { background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: bold; color: #0f172a; }
                ul { line-height: 1.8; }
            </style>
        </head>
        <body>
            <div class="card">
                <h2 style="color: #27ae60; margin-top: 0;">✅ Administrador Maestro Configurado</h2>
                <p><?= htmlspecialchars($mensaje) ?></p>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 15px; margin: 20px 0;">
                    <h4 style="margin: 0 0 10px 0; color: #166534;">Credenciales de Ingreso:</h4>
                    <ul>
                        <li><strong>Identificador:</strong> <code>admin@nutrisalud.com</code> o <code>admin</code></li>
                        <li><strong>Contraseña:</strong> <code>admin123</code></li>
                        <li><strong>Rol Asignado:</strong> <code>admin</code> (SuperAdmin)</li>
                    </ul>
                </div>
                <a href="../index.php?action=login_nutri" class="btn">Ir al Login de Profesionales &rarr;</a>
            </div>
        </body>
        </html>
        <?php
    }

} catch (Exception $e) {
    if ($is_cli) {
        fwrite(STDERR, "❌ Error al restablecer admin: " . $e->getMessage() . PHP_EOL);
        exit(1);
    } else {
        http_response_code(500);
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
}
