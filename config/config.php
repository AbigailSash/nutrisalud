<?php
// config/config.php
// Archivo de configuración para alternar automáticamente entre Local y Producción

// Determinamos si el entorno es local
$is_cli = php_sapi_name() === 'cli';
$is_local = $is_cli || (isset($_SERVER['HTTP_HOST']) && (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']) || strpos($_SERVER['HTTP_HOST'], 'localhost') !== false));

if ($is_local) {
    // Entorno de Desarrollo (Local)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'NutriSalud');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    // Calcular la ruta base de forma dinámica
    $base_path = $is_cli ? '' : dirname($_SERVER['SCRIPT_NAME'] ?? '');
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    }
    $host = $is_cli ? 'localhost' : ($_SERVER['HTTP_HOST'] ?? 'localhost');
    define('BASE_URL', 'http://' . $host . $base_path . '/');
} else {
    // Entorno de Producción
    define('DB_HOST', 'localhost'); // Usualmente localhost en producción también, pero cambiar si es externo
    define('DB_NAME', 'produccion_nutrisalud');
    define('DB_USER', 'produccion_usuario');
    define('DB_PASS', 'produccion_password');
    define('BASE_URL', 'https://' . $_SERVER['HTTP_HOST'] . '/');
}
