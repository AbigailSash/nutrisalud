<?php
// config/Conexion.php
// Archivo de conexión segura a MySQL utilizando PDO con soporte completo utf8mb4 (emojis y caracteres especiales).

require_once __DIR__ . '/config.php';

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
            
            // Diagnóstico detallado únicamente en entorno local/desarrollo
            if (isset($is_local) && $is_local) {
                if (php_sapi_name() === 'cli') {
                    $dbHost = defined('DB_HOST') ? DB_HOST : 'N/D';
                    $dbName = defined('DB_NAME') ? DB_NAME : 'N/D';
                    $dbUser = defined('DB_USER') ? DB_USER : 'N/D';
                    fwrite(STDERR, "[MySQL Error] No se pudo conectar a $dbHost/$dbName con usuario $dbUser: " . $e->getMessage() . PHP_EOL);
                    exit(1);
                }
                http_response_code(500);
                $dbHost = defined('DB_HOST') ? DB_HOST : 'N/D';
                $dbName = defined('DB_NAME') ? DB_NAME : 'N/D';
                $dbUser = defined('DB_USER') ? DB_USER : 'N/D';
                $errorMsg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                
                die("<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Diagnóstico Base de Datos (Local) - NutriSalud</title><style>body{font-family:system-ui,-apple-system,sans-serif;padding:40px 20px;background:#f1f5f9;color:#0f172a;}.card{background:white;max-width:650px;margin:0 auto;padding:30px;border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.08);border-top:5px solid #ef4444;}code{background:#f8fafc;padding:3px 8px;border-radius:6px;border:1px solid #cbd5e1;font-size:0.9em;color:#e11d48;}ul{line-height:1.8;}</style></head><body><div class='card'><h2 style='color:#e11d48;margin-top:0;'>⚠️ Error de Conexión MySQL (Modo Desarrollo)</h2><p>El servidor PHP no pudo conectarse a la Base de Datos con los parámetros actuales:</p><ul><li><strong>Host:</strong> <code>{$dbHost}</code></li><li><strong>Base de Datos:</strong> <code>{$dbName}</code></li><li><strong>Usuario:</strong> <code>{$dbUser}</code></li></ul><div style='background:#fef2f2;border-left:4px solid #ef4444;padding:12px;margin:15px 0;border-radius:4px;'><strong>Detalle MySQL:</strong><br><small><code>{$errorMsg}</code></small></div><p style='color:#64748b;font-size:13px;'>Verifica que MySQL esté iniciado en tu entorno local (XAMPP/Laragon/Docker) y que la base de datos exista.</p></div></body></html>");
            } else {
                if (php_sapi_name() === 'cli') {
                    fwrite(STDERR, "[MySQL Error] Servicio de base de datos no disponible." . PHP_EOL);
                    exit(1);
                }
                // Entorno de Producción: Respuesta HTTP 503 segura sin filtrar credenciales ni trazas
                http_response_code(503);
                die("<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>NutriSalud - Servicio Temporalmente no Disponible</title><style>body{font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#1e293b;padding:20px;box-sizing:border-box;}.card{background:white;max-width:520px;width:100%;padding:40px 30px;text-align:center;border-radius:20px;box-shadow:0 15px 35px rgba(0,0,0,0.06);border-top:5px solid #2ecc71;}.icon{font-size:48px;margin-bottom:15px;}.btn{display:inline-block;margin-top:25px;padding:12px 26px;background:#2ecc71;color:white;text-decoration:none;border-radius:10px;font-weight:600;font-size:14px;box-shadow:0 4px 12px rgba(46,204,113,0.25);}</style></head><body><div class='card'><div class='icon'>🌱</div><h2 style='color:#1e293b;margin:0 0 10px 0;'>NutriSalud Íntegra</h2><p style='color:#64748b;line-height:1.6;margin:0 0 15px 0;'>La plataforma está realizando una breve tarea de sincronización o mantenimiento de servidor. Por favor, reintenta en unos instantes.</p><a href='javascript:location.reload()' class='btn'>Reintentar Conexión</a></div></body></html>");
            }
        }
    }
}
?>
