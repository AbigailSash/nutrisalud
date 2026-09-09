<?php
// config/Conexion.php
// Archivo de conexión segura a MySQL utilizando PDO con soporte completo utf8mb4 (emojis y caracteres especiales).

class Conexion {
    public static function conectar() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '-03:00'"
            ];
            
            $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            return $conexion;
            
        } catch(PDOException $e) {
            error_log("Error de conexión PDO: " . $e->getMessage());
            global $is_local;
            
            $debugParam = isset($_GET['debug_db']) || (isset($is_local) && $is_local);
            
            if ($debugParam) {
                http_response_code(500);
                $dbHost = defined('DB_HOST') ? DB_HOST : 'N/D';
                $dbName = defined('DB_NAME') ? DB_NAME : 'N/D';
                $dbUser = defined('DB_USER') ? DB_USER : 'N/D';
                $errorMsg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                
                die("<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Diagnóstico Base de Datos - NutriSalud</title><style>body{font-family:system-ui,-apple-system,sans-serif;padding:40px 20px;background:#f1f5f9;color:#0f172a;}.card{background:white;max-width:650px;margin:0 auto;padding:30px;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.08);border-top:5px solid #ef4444;}code{background:#f8fafc;padding:3px 8px;border-radius:6px;border:1px solid #cbd5e1;font-size:0.9em;color:#e11d48;}ul{line-height:1.8;}</style></head><body><div class='card'><h2 style='color:#e11d48;margin-top:0;'>⚠️ Error de Conexión MySQL</h2><p>El servidor PHP no pudo conectarse a la Base de Datos con los parámetros actuales:</p><ul><li><strong>Host:</strong> <code>{$dbHost}</code></li><li><strong>Base de Datos:</strong> <code>{$dbName}</code></li><li><strong>Usuario:</strong> <code>{$dbUser}</code></li></ul><div style='background:#fef2f2;border-left:4px solid #ef4444;padding:12px;margin:15px 0;border-radius:4px;'><strong>Detalle MySQL:</strong><br><small><code>{$errorMsg}</code></small></div><h4 style='margin-bottom:8px;'>¿Cómo solucionarlo en Hostinger?</h4><ol style='line-height:1.6;color:#334155;'><li>Entra a tu <strong>hPanel de Hostinger</strong> &gt; <strong>Bases de Datos MySQL</strong>.</li><li>Copia el <strong>Nombre de la Base de Datos</strong>, <strong>Usuario</strong> y <strong>Contraseña</strong> exactos.</li><li>Edita el archivo <code>config/config.php</code> en el Administrador de Archivos de Hostinger con esos datos exactos.</li></ol></div></body></html>");
            } else {
                http_response_code(503);
                die("<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>NutriSalud - Servicio no disponible</title><style>body{font-family:system-ui,-apple-system,sans-serif;text-align:center;padding:60px 20px;background:#f8fafc;color:#1e293b;}.card{background:white;max-width:550px;margin:0 auto;padding:40px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.05);border-top:4px solid #2ecc71;}.btn{display:inline-block;margin-top:20px;padding:10px 20px;background:#2ecc71;color:white;text-decoration:none;border-radius:8px;font-weight:600;font-size:14px;}</style></head><body><div class='card'><h2 style='color:#27ae60;margin-bottom:10px;'>NutriSalud</h2><p style='color:#64748b;'>El sistema está en mantenimiento temporal o restableciendo la conexión con la base de datos. Por favor, reintenta en unos instantes.</p><div style='margin-top:25px;padding-top:15px;border-top:1px dashed #e2e8f0;font-size:13px;color:#94a3b8;'>¿Eres el administrador? <a href='index.php?debug_db=1' style='color:#2ecc71;text-decoration:none;font-weight:bold;'>Ver diagnóstico de conexión</a></div></div></body></html>");
            }
        }
    }
}
?>
