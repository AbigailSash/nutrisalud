<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/models/Paciente.php';
require_once __DIR__ . '/models/Nutricionista.php';
require_once __DIR__ . '/models/PlanAlimentario.php';
require_once __DIR__ . '/models/Turno.php';
require_once __DIR__ . '/controllers/PacienteController.php';

session_start();
$_SESSION['IdPaciente'] = 1;

ob_start();
$controller = new PacienteController();
$controller->dashboard_paciente();
$output = ob_get_clean();

if (strpos($output, 'Warning') !== false || strpos($output, 'Undefined variable') !== false) {
    echo "ERROR: Se encontraron warnings en la vista del dashboard del paciente:\n";
    preg_match_all('/Warning:[^\n<]+/i', $output, $matches);
    print_r($matches[0]);
} else {
    echo "OK ✅: dashboard_paciente renderizó limpiamente sin warnings ni errores de variables.\n";
}
