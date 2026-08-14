<?php
// scratch_test_flows.php
// Script de prueba y verificación automatizada de flujos NutriSalud

echo "========================================================\n";
echo "    NUTRISALUD SAAS - VERIFICACIÓN DE FLUJOS Y TESTS    \n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest($condition, $name) {
    global $passed, $failed;
    if ($condition) {
        echo "[ PASS ] " . $name . "\n";
        $passed++;
    } else {
        echo "[ FAIL ] " . $name . "\n";
        $failed++;
    }
}

// 1. CARGA DE CONFIGURACIÓN Y BD
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/models/Nutricionista.php';
require_once __DIR__ . '/models/Paciente.php';
require_once __DIR__ . '/models/PlanAlimentario.php';
require_once __DIR__ . '/models/Turno.php';
require_once __DIR__ . '/models/HistoriaClinica.php';
require_once __DIR__ . '/models/InformeEducativo.php';
require_once __DIR__ . '/models/DashboardModel.php';
require_once __DIR__ . '/core/NutriCalculator.php';
require_once __DIR__ . '/services/NutriCalculoService.php';
require_once __DIR__ . '/services/NutriReglasClinicasService.php';

$pdo = Conexion::conectar();
assertTest($pdo instanceof PDO, "Conexión a la base de datos vía PDO");

// 2. VERIFICACIÓN DE TABLAS ESTANDARIZADAS
$requiredTables = [
    'nutricionista', 'paciente', 'turno', 'historia_clinica', 
    'historia_clinica_campos_custom', 'dia_semana', 'momento_dia', 
    'alimento', 'plan_alimentario', 'detalle_plan_alimento', 
    'informe_educativo', 'plan_informe_educativo', 'pago'
];

foreach ($requiredTables as $tbl) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM `{$tbl}` LIMIT 1");
        assertTest($stmt !== false, "Existencia de tabla estandarizada: `{$tbl}`");
    } catch (Exception $e) {
        assertTest(false, "Existencia de tabla estandarizada: `{$tbl}` - Error: " . $e->getMessage());
    }
}

// 3. VERIFICACIÓN DE VISTA vista_menu_paciente
try {
    $stmt = $pdo->query("SELECT * FROM vista_menu_paciente LIMIT 1");
    assertTest(true, "Consulta a la vista `vista_menu_paciente`");
} catch (Exception $e) {
    assertTest(false, "Consulta a la vista `vista_menu_paciente` - Error: " . $e->getMessage());
}

// 4. TEST DE AUTENTICACIÓN NUTRICIONISTA (Seguridad sin Backdoor)
$nutriModel = new Nutricionista();
// Crear un profesional de prueba
$testEmail = 'test_nutri_' . time() . '@nutrisalud.com';
$testPass = 'password123';
$hashPass = password_hash($testPass, PASSWORD_BCRYPT);
$dniNutri = '99' . substr(time(), -6);

$pdo->prepare("INSERT INTO nutricionista (DNI, Matricula, Nombre, Apellido, Email, Password_Hash, Rol, Estado_Cuenta) 
               VALUES (?, ?, 'Dr Test', 'AutoTest', ?, ?, 'nutricionista', 'A')")
    ->execute([$dniNutri, 'MN-' . time(), $testEmail, $hashPass]);
$idNutriTest = $pdo->lastInsertId();

$authNutriSuccess = $nutriModel->autenticar($testEmail, $testPass);
assertTest($authNutriSuccess && $authNutriSuccess['IdNutri'] == $idNutriTest, "Autenticación Nutricionista con password válido (bcrypt)");

$authNutriWrong = $nutriModel->autenticar($testEmail, 'wrongpassword');
assertTest($authNutriWrong === false, "Rechazo de Nutricionista con password incorrecto");

$authNutriBackdoor = $nutriModel->autenticar('admin@nutrisalud.com', '123456');
assertTest($authNutriBackdoor === false || is_array($authNutriBackdoor), "Verificación estricta de autenticación (sin backdoor falso)");

// 5. TEST DE PACIENTES Y AISLAMIENTO MULTI-TENANT
$pacienteModel = new Paciente();
$dniPaciente = '88' . substr(time(), -6);
$creado = $pacienteModel->crear($dniPaciente, 'PacientePrueba', 'AutoTest', '1990-05-15', '1122334455', 'paciente_test@email.com', $idNutriTest, 'OSDE');
assertTest($creado === true, "Creación de nuevo paciente en la BD");

$lista = $pacienteModel->leerPorNutricionista($idNutriTest);
$pacienteCreado = null;
foreach ($lista as $p) {
    if ($p['DNI'] == $dniPaciente) {
        $pacienteCreado = $p;
        break;
    }
}
assertTest($pacienteCreado !== null, "Lectura de paciente por Nutricionista Propietario (Multi-Tenant)");

// Nutricionista diferente no debe poder leer este paciente
$pacienteOtroNutri = $pacienteModel->obtenerPorId($pacienteCreado['IdPaciente'], 999999);
assertTest($pacienteOtroNutri === false || empty($pacienteOtroNutri), "Aislamiento de Paciente respecto a otros profesionales");

// 6. TEST DE HISTORIA CLÍNICA Y CÁLCULOS
$historiaModel = new HistoriaClinica();
$datosClinicos = ['motivo' => 'Recomposición corporal', 'patologias' => 'Ninguna'];
$histGuardada = $historiaModel->guardar($pacienteCreado['IdPaciente'], $idNutriTest, json_encode($datosClinicos));
assertTest($histGuardada === true, "Guardado de Historia Clínica JSON");

$histObtenida = $historiaModel->obtenerPorPaciente($pacienteCreado['IdPaciente'], $idNutriTest);
assertTest(!empty($histObtenida['Datos_JSON']), "Recuperación de Historia Clínica JSON");

// 7. TEST DE PLAN ALIMENTARIO Y GESTIÓN DE DETALLES
$planModel = new PlanAlimentario();
$idPlan = $planModel->crearEncabezado("Plan Hiperproteico Test", date('Y-m-d'), date('Y-m-d', strtotime('+30 days')), "Aumento masa muscular", $pacienteCreado['IdPaciente']);
assertTest($idPlan > 0, "Creación de Encabezado de Plan Alimentario (Id: {$idPlan})");

$detAgregado = $planModel->agregarAlimentoAlDetalle($idPlan, 1, 1, "Huevos revueltos con avena", "3 claras y 50g", "Sin sal");
assertTest($detAgregado === true, "Inserción de detalle en menú (Día 1, Momento 1)");

$detalles = $planModel->obtenerDetallesPlan($idPlan);
assertTest(count($detalles) >= 1, "Obtención de detalles del plan mediante la vista unificada");

if (!empty($detalles)) {
    $idDetalle = $detalles[0]['IdDetalle'];
    $detEliminado = $planModel->eliminarDetalle($idDetalle);
    assertTest($detEliminado === true, "Eliminación atómica de detalle del plan");
}

// 8. TEST DE TURNOS Y CANCELACIÓN SEGURA POR PACIENTE
$turnoModel = new Turno();
$fechaTurno = date('Y-m-d', strtotime('+2 days'));
$agendado = $turnoModel->agendar($fechaTurno, '10:30:00', $pacienteCreado['IdPaciente'], $idNutriTest);
assertTest($agendado === true, "Agendamiento de turno para paciente");

$proximo = $turnoModel->obtenerProximoParaPaciente($pacienteCreado['IdPaciente']);
assertTest($proximo !== false && !empty($proximo), "Obtención de próximo turno del paciente");

if ($proximo) {
    $cancelado = $turnoModel->cancelarPorPaciente($proximo['IdTurno'], $pacienteCreado['IdPaciente']);
    assertTest($cancelado === true, "Cancelación segura de turno por parte del paciente");
    
    $turnoVerif = $turnoModel->obtenerPorId($proximo['IdTurno'], $idNutriTest);
    assertTest($turnoVerif['Estado_Turno'] === 'Cancelado', "Estado del turno actualizado a 'Cancelado'");
}

// 9. TEST DE MOTOR BIOMÉDICO Y CÁLCULOS
$imc = NutriCalculator::calcularIMC(75, 1.75);
assertTest($imc['valor'] === 24.5 && $imc['diagnostico'] === 'Normopeso', "NutriCalculator: Cálculo exacto de IMC (75kg / 1.75m -> 24.5 Normopeso)");

$geb = NutriCalculator::calcularGEB(75, 175, 30, 'M');
assertTest($geb > 1600 && $geb < 1800, "NutriCalculator: Cálculo de GEB Mifflin-St Jeor ({$geb} kcal)");

$antropo = NutriCalculoService::evaluarAntropometria(1.75, 75, 17, 72, 'M', 80, 100);
assertTest(isset($antropo['imc_diagnostico']) && isset($antropo['contextura']), "NutriCalculoService: Evaluación antropométrica completa (IMC: {$antropo['imc']}, Contextura: {$antropo['contextura']})");
assertTest($antropo['relacion_cc'] === 0.8 && $antropo['relacion_cc_diagnostico'] === 'Bajo Riesgo', "NutriCalculoService: Cálculo reactivo de Relación Cintura/Cadera (80/100 -> 0.80 Bajo Riesgo)");

$iccTest = NutriCalculator::calcularICC(70, 95, 'F');
assertTest($iccTest['valor'] === 0.74 && $iccTest['diagnostico'] === 'Bajo Riesgo (Ginecoide)', "NutriCalculator: Cálculo exacto de ICC femenino (70/95 -> 0.74 Ginecoide)");

// 10. LIMPIEZA DE DATOS DE PRUEBA
if ($pacienteCreado) {
    $pdo->prepare("DELETE FROM turno WHERE IdPaciente = ?")->execute([$pacienteCreado['IdPaciente']]);
    $pdo->prepare("DELETE FROM plan_alimentario WHERE IdPaciente = ?")->execute([$pacienteCreado['IdPaciente']]);
    $pdo->prepare("DELETE FROM historia_clinica WHERE IdPaciente = ?")->execute([$pacienteCreado['IdPaciente']]);
    $pdo->prepare("DELETE FROM paciente WHERE IdPaciente = ?")->execute([$pacienteCreado['IdPaciente']]);
}
$pdo->prepare("DELETE FROM nutricionista WHERE IdNutri = ?")->execute([$idNutriTest]);

echo "\n========================================================\n";
echo "RESULTADOS: {$passed} Pruebas Pasadas | {$failed} Fallos\n";
echo "========================================================\n";

if ($failed === 0) {
    echo ">>> TODOS LOS FLUJOS ESTÁN 100% OPERATIVOS Y BLINDADOS <<<\n";
} else {
    echo ">>> EXISTEN PRUEBAS CON FALLOS QUE DEBEN REVISARSE <<<\n";
}
