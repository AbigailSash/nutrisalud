<?php
// config/email.php
// Configuración de servidor de correo SMTP para NutriSalud.

return [
    /*
    |--------------------------------------------------------------------------
    | Habilitar Envío Real vía SMTP
    |--------------------------------------------------------------------------
    | Si se establece en true, el sistema enviará correos reales a través de SMTP.
    | Si se deja en false o la contraseña está vacía, se usará mail() / log local.
    */
    'smtp_enabled'  => false, // Cambiar a true al colocar tus credenciales SMTP

    /*
    |--------------------------------------------------------------------------
    | Parámetros del Servidor SMTP
    |--------------------------------------------------------------------------
    | Ejemplos comunes:
    | - Gmail: smtp.gmail.com | Puerto 587 (TLS) o 465 (SSL) (Requiere Contraseña de Aplicación)
    | - Brevo / Sendinblue: smtp-relay.brevo.com | Puerto 587 (TLS)
    | - Hostinger / cPanel: smtp.tudominio.com | Puerto 465 o 587
    */
    'smtp_host'     => 'smtp.gmail.com',
    'smtp_port'     => 587,
    'smtp_secure'   => 'tls', // 'tls' o 'ssl'
    'smtp_username' => '',    // Tu correo emisor (ej: tuemail@gmail.com)
    'smtp_password' => '',    // Tu contraseña de aplicación generada

    /*
    |--------------------------------------------------------------------------
    | Remitente por Defecto
    |--------------------------------------------------------------------------
    */
    'from_email'    => 'notificaciones@nutrisalud.com',
    'from_name'     => 'NutriSalud',
    'debug_log'     => true
];
