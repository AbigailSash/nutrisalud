<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/services/EmailService.php';
require_once __DIR__ . '/models/Paciente.php';
require_once __DIR__ . '/models/Nutricionista.php';

echo "========================================================\n";
echo "    PRUEBA DE SERVICIO DE EMAIL Y ALTA DE PACIENTE     \n";
echo "========================================================\n\n";

$nutriModel = new Nutricionista();
$nutri = $nutriModel->obtenerPorId(1) ?: [
    'Nombre' => 'Leila',
    'Apellido' => 'Olmedo',
    'Especialidad' => 'Lic. en Nutrición',
    'Matricula' => 'MN-310',
    'Color_Tema' => '#9b59b6'
];

$testEmail = 'paciente.prueba@nutrisalud.com';
$testNombre = 'Carlos';
$testApellido = 'González';
$testDNI = '40123456';
$testPassword = $testDNI;

$resultado = EmailService::enviarCredencialesAltaPaciente(
    $testEmail,
    $testNombre,
    $testApellido,
    $testDNI,
    $testPassword,
    $nutri
);

if ($resultado) {
    echo "[ PASS ] EmailService procesó el envío de credenciales correctamente ✅\n";
} else {
    echo "[ FAIL ] EmailService falló ❌\n";
}

$logPath = __DIR__ . '/logs/emails_enviados.log';
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    if (strpos($logContent, $testEmail) !== false) {
        echo "[ PASS ] Log de auditoría de correos registrado exitosamente ✅\n";
    } else {
        echo "[ FAIL ] No se encontró el registro en el log ❌\n";
    }
} else {
    echo "[ FAIL ] Archivo de log no encontrado ❌\n";
}

echo "\nPrueba completada con éxito.\n";
