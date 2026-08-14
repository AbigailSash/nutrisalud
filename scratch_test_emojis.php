<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/models/PlanAlimentario.php';

$model = new PlanAlimentario();
$pdo = Conexion::conectar();

// Test saving emojis
$testEmojiString = "💧 PAUTA DE HIDRATACIÓN 🥗🍳🍽️\n- Beber 2L de agua al día.";
$stmt = $pdo->query("SELECT IdPlan FROM plan_alimentario LIMIT 1");
$row = $stmt->fetch();

if ($row) {
    $idPlan = $row['IdPlan'];
    $ok = $model->guardarRecomendaciones($idPlan, $testEmojiString);
    echo "Guardado con emojis en plan $idPlan: " . ($ok ? "EXITOSO ✅" : "FALLÓ ❌") . "\n";
    
    $plan = $model->obtenerPorId($idPlan);
    echo "Contenido recuperado:\n" . $plan['Recomendaciones'] . "\n";
} else {
    echo "No hay planes para probar.\n";
}
