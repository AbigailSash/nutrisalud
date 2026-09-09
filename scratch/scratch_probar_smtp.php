<?php
// scratch_probar_smtp.php
// Script interactivo para probar el envío real de correo vía SMTP

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/services/EmailService.php';

$config = require __DIR__ . '/config/email.php';

echo "========================================================\n";
echo "       DIAGNÓSTICO Y PRUEBA DE ENVÍO DE EMAIL SMTP       \n";
echo "========================================================\n\n";

echo "1. Estado de Configuración:\n";
echo " - SMTP Habilitado: " . ($config['smtp_enabled'] ? "SÍ ✅" : "NO ⚠️ (Modifica config/email.php)") . "\n";
echo " - Servidor Host:   " . ($config['smtp_host'] ?: "No definido") . "\n";
echo " - Puerto:          " . ($config['smtp_port'] ?: "587") . " (" . ($config['smtp_secure'] ?: "tls") . ")\n";
echo " - Usuario/Emisor:  " . ($config['smtp_username'] ?: "[Vacío - Coloca tu correo en config/email.php]") . "\n";
echo " - Contraseña:      " . (!empty($config['smtp_password']) ? "[Configurada ✅]" : "[Vacía ⚠️]") . "\n\n";

if (empty($config['smtp_username']) || empty($config['smtp_password']) || !$config['smtp_enabled']) {
    echo "ℹ️  Para que los correos lleguen a bandejas reales (Gmail, Outlook, Yahoo):\n";
    echo "   1. Abre el archivo `config/email.php`\n";
    echo "   2. Establece 'smtp_enabled' => true\n";
    echo "   3. Coloca tu correo en 'smtp_username' y tu contraseña de aplicación en 'smtp_password'\n";
    echo "   4. Vuelve a ejecutar este script o da de alta un paciente en la web.\n\n";
} else {
    echo "2. Probando conexión y envío real de prueba...\n";
    $testDestinatario = $config['smtp_username']; // Enviarse a sí mismo para probar
    
    $resultado = EmailService::enviar(
        $testDestinatario,
        "Prueba de Conexión NutriSalud SMTP - " . date('H:i:s'),
        "<h2>¡Conexión Exitosa!</h2><p>Este correo confirma que tu servidor SMTP está correctamente configurado en NutriSalud y los pacientes recibirán sus credenciales reales.</p>"
    );

    if ($resultado) {
        echo "✅ ¡CORREO ENVIADO CON ÉXITO a $testDestinatario!\n";
        echo "Revisa tu bandeja de entrada o spam.\n";
    } else {
        echo "❌ Error en el envío. Revisa logs/emails_enviados.log para más detalles.\n";
    }
}
