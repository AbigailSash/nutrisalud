<?php
// core/preflight.php
// Script de comprobación previa antes del lanzamiento a producción

// 1. Configuración global de zona horaria (UTC-3)
date_default_timezone_set('America/Argentina/Buenos_Aires');

// 2. Configuración de Sesiones Seguras
// Evitar ataques XSS mediante cookies de sesión
ini_set('session.cookie_httponly', 1);

// Forzar cookies seguras si estamos en HTTPS (Producción)
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
    ini_set('session.cookie_secure', 1);
}

// Prevenir fijación de sesión
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// 3. Ocultamiento de display_errors en Producción
// Se asume que $is_local se definió en config.php
global $is_local;
if (isset($is_local) && !$is_local) {
    // Entorno de Producción: ocultar errores
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL); // Registrarlos internamente
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    // Entorno de Desarrollo (Local): mostrar errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
