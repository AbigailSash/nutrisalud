<?php
// router.php para servidor de desarrollo integrado de PHP
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Si la ruta solicitada es un archivo o directorio existente en public/, lo servimos
if (file_exists(__DIR__ . $path) && is_file(__DIR__ . $path)) {
    return false;
}

// Emular el comportamiento de mod_rewrite
$action = ltrim($path, '/');
if (!empty($action)) {
    $_GET['action'] = $action;
}

include_once 'index.php';
