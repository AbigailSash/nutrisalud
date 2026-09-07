<?php
// scratch_test_password_reset.php
// Suite de pruebas automatizadas para el flujo de recuperación de contraseña.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/models/PasswordReset.php';
require_once __DIR__ . '/models/Nutricionista.php';
require_once __DIR__ . '/models/Paciente.php';
require_once __DIR__ . '/services/EmailService.php';

echo "========================================================\n";
echo "    TEST SUITE: RECUPERACIÓN SEGURA DE CONTRASEÑA       \n";
echo "========================================================\n\n";

$resetModel = new PasswordReset();
$nutriModel = new Nutricionista();
$pacienteModel = new Paciente();

// 1. Asegurar usuario de prueba para nutricionista
$emailNutri = 'test.recovery.nutri@nutrisalud.com';
$conexion = Conexion::conectar();
$conexion->exec("DELETE FROM nutricionista WHERE Email = '$emailNutri'");
$conexion->exec("INSERT INTO nutricionista (DNI, Matricula, Nombre, Apellido, Email, Password_Hash, Rol, Estado_Cuenta) 
                 VALUES ('99887766', 'MN-TEST-REC', 'Dra. Test', 'Recuperacion', '$emailNutri', '" . password_hash('claveInicial123', PASSWORD_BCRYPT) . "', 'nutricionista', 'A')");

$nutriTest = $nutriModel->obtenerPorEmail($emailNutri);
assert($nutriTest !== false, "El nutricionista de prueba debe existir");
echo "[ PASS ] 1. Nutricionista de prueba registrado ✅\n";

// 2. Generación de Token Criptográfico
$tokenData = $resetModel->crearToken($emailNutri, 'nutricionista', 30);
assert($tokenData !== false, "El token debe generarse correctamente");
assert(strlen($tokenData['token']) === 64, "El token debe ser de 64 caracteres hex");
echo "[ PASS ] 2. Generación de token criptográfico (64 caracteres, 256 bits) ✅\n";

// 3. Validación de Token Vigente
$tokenValido = $resetModel->validarToken($tokenData['token']);
assert($tokenValido !== false, "El token recién generado debe ser válido");
assert($tokenValido['Email'] === $emailNutri, "El email del token debe coincidir");
assert($tokenValido['Tipo_Usuario'] === 'nutricionista', "El tipo de usuario debe ser nutricionista");
echo "[ PASS ] 3. Validación de token vigente y metadatos correctos ✅\n";

// 4. Envío de Correo Institucional con el Enlace
$resetUrl = BASE_URL . 'index.php?action=restablecer_password&token=' . $tokenData['token'];
$emailEnviado = EmailService::enviarRecuperacionPassword(
    $emailNutri,
    'Dra. Test Recuperacion',
    $resetUrl,
    'nutricionista',
    30
);
assert($emailEnviado === true, "EmailService debe procesar el envío");
echo "[ PASS ] 4. Plantilla de correo con botón y enlace temporal despachada ✅\n";

// 5. Restablecimiento con Nueva Contraseña
$nuevaClave = 'SuperSegura999!';
$cambiado = $nutriModel->cambiarPassword($nutriTest['IdNutri'], $nuevaClave);
assert($cambiado === true, "La contraseña debe actualizarse en la BD");
$resetModel->marcarComoUsado($tokenData['token']);
echo "[ PASS ] 5. Contraseña actualizada con hash bcrypt y token marcado como usado ✅\n";

// 6. Verificación de Autenticación con la Nueva Contraseña
$loginExitoso = $nutriModel->autenticar($emailNutri, $nuevaClave);
assert($loginExitoso !== false, "El login con la nueva contraseña debe ser exitoso");
$loginViejo = $nutriModel->autenticar($emailNutri, 'claveInicial123');
assert($loginViejo === false, "El login con la clave anterior debe ser rechazado");
echo "[ PASS ] 6. Autenticación exitosa con nueva clave y rechazo de clave anterior ✅\n";

// 7. Intento de Reutilización del Token (Debe fallar)
$reintentoToken = $resetModel->validarToken($tokenData['token']);
assert($reintentoToken === false, "El token usado no debe ser reutilizable");
echo "[ PASS ] 7. Blindaje de seguridad: Token de un solo uso no reutilizable ✅\n";

// 8. Flujo para Paciente
$emailPaciente = 'test.recovery.paciente@nutrisalud.com';
$conexion->exec("DELETE FROM paciente WHERE Email = '$emailPaciente'");
$idNutriExistente = $conexion->query("SELECT IdNutri FROM nutricionista LIMIT 1")->fetchColumn();
$conexion->exec("INSERT INTO paciente (DNI, Nombre, Apellido, Fecha_Nacimiento, Email, IdNutri, Password) 
                 VALUES ('88776655', 'Juan', 'PacienteTest', '1995-05-10', '$emailPaciente', $idNutriExistente, '" . password_hash('clavePac123', PASSWORD_BCRYPT) . "')");

$tokenPacData = $resetModel->crearToken($emailPaciente, 'paciente', 30);
$tokenPacValido = $resetModel->validarToken($tokenPacData['token']);
assert($tokenPacValido !== false && $tokenPacValido['Tipo_Usuario'] === 'paciente', "Token de paciente válido");

$pacienteTest = $pacienteModel->obtenerPorEmail($emailPaciente);
$nuevaClavePac = 'MiNuevaClavePaciente456!';
$pacienteModel->cambiarPassword($pacienteTest['IdPaciente'], $nuevaClavePac);
$resetModel->marcarComoUsado($tokenPacData['token']);

$authPac = $pacienteModel->autenticar($pacienteTest['DNI'], $nuevaClavePac);
assert($authPac !== false, "Paciente autenticado con nueva clave");
echo "[ PASS ] 8. Flujo completo de recuperación para Pacientes verificado ✅\n";

// Limpieza de datos de prueba
$conexion->exec("DELETE FROM nutricionista WHERE Email = '$emailNutri'");
$conexion->exec("DELETE FROM paciente WHERE Email = '$emailPaciente'");
$conexion->exec("DELETE FROM password_resets WHERE Email IN ('$emailNutri', '$emailPaciente')");

echo "\n========================================================\n";
echo "RESULTADOS: Todas las pruebas de seguridad pasaron (8/8) ✅\n";
echo "========================================================\n";
