<?php
// core/preflight.php
// Script de comprobación previa antes del lanzamiento a producción

// 1. Configuración global de zona horaria (UTC-3)
date_default_timezone_set('America/Argentina/Buenos_Aires');

// 2. Configuración de Sesiones Seguras
// Evitar ataques XSS mediante cookies de sesión
ini_set('session.cookie_httponly', '1');

// Forzar cookies seguras si estamos en HTTPS (Producción)
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
if ($is_https) {
    ini_set('session.cookie_secure', '1');
}

// Prevenir fijación de sesión y cookies de terceros
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
if (PHP_VERSION_ID >= 70300) {
    ini_set('session.cookie_samesite', 'Lax');
}

// 3. Ocultamiento de display_errors en Producción y Registro de Logs
global $is_local;
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

if (isset($is_local) && !$is_local) {
    // Entorno de Producción: ocultar errores al usuario final y guardarlos en logs
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
    ini_set('error_log', $logDir . '/php_errors.log');
} else {
    // Entorno de Desarrollo (Local): mostrar errores detallados
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}
