<?php
// models/PasswordReset.php
// Modelo de gestión segura de tokens temporales para restablecimiento de contraseñas.

require_once 'config/Conexion.php';

class PasswordReset {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
        $this->asegurarTablaExiste();
    }

    /**
     * Asegura que la tabla de restablecimiento de contraseñas exista en la BD.
     */
    public function asegurarTablaExiste() {
        $sql = "CREATE TABLE IF NOT EXISTS `password_resets` (
            `IdReset` INT(11) NOT NULL AUTO_INCREMENT,
            `Email` VARCHAR(150) NOT NULL,
            `Token` VARCHAR(64) NOT NULL,
            `Tipo_Usuario` ENUM('nutricionista', 'paciente') NOT NULL,
            `Expires_At` DATETIME NOT NULL,
            `Used_At` DATETIME NULL DEFAULT NULL,
            `Created_At` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`IdReset`),
            UNIQUE KEY `uq_token` (`Token`),
            KEY `idx_email_tipo` (`Email`, `Tipo_Usuario`),
            KEY `idx_token_expires` (`Token`, `Expires_At`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        try {
            $this->conexion->exec($sql);
        } catch (Throwable $e) {
            // Manejo silencioso si ya existe o por permisos
        }
    }

    /**
     * Genera un token criptográficamente seguro de 64 caracteres con 30 min de expiración.
     */
    public function crearToken($email, $tipoUsuario, $minutosExpiracion = 30) {
        $email = trim(strtolower($email));
        $tipoUsuario = ($tipoUsuario === 'paciente') ? 'paciente' : 'nutricionista';

        // 1. Invalidar tokens anteriores activos para este email
        $sqlInvalidar = "UPDATE password_resets 
                         SET Used_At = NOW() 
                         WHERE Email = :email AND Tipo_Usuario = :tipo AND Used_At IS NULL";
        $stmtInv = $this->conexion->prepare($sqlInvalidar);
        $stmtInv->execute([':email' => $email, ':tipo' => $tipoUsuario]);

        // 2. Generar token criptográfico (256 bits de entropía)
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$minutosExpiracion minutes"));

        // 3. Registrar el token en la BD
        $sql = "INSERT INTO password_resets (Email, Token, Tipo_Usuario, Expires_At) 
                VALUES (:email, :token, :tipo, :expires_at)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipoUsuario, PDO::PARAM_STR);
        $stmt->bindParam(':expires_at', $expiresAt, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return [
                'token' => $token,
                'expires_at' => $expiresAt,
                'minutos' => $minutosExpiracion
            ];
        }
        return false;
    }

    /**
     * Valida si un token existe, no ha sido usado y aún no ha expirado.
     */
    public function validarToken($token) {
        $token = trim($token);
        if (empty($token) || strlen($token) !== 64) {
            return false;
        }

        $sql = "SELECT * FROM password_resets 
                WHERE Token = :token AND Used_At IS NULL AND Expires_At > NOW() 
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Invalida el token tras un cambio exitoso de contraseña.
     */
    public function marcarComoUsado($token) {
        $sql = "UPDATE password_resets SET Used_At = NOW() WHERE Token = :token";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
