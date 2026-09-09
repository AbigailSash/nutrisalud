<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Conexion.php';
require_once __DIR__ . '/models/Nutricionista.php';

$model = new Nutricionista();
$pdo = Conexion::conectar();

$stmt = $pdo->query("SELECT IdNutri FROM nutricionista LIMIT 1");
$nutri = $stmt->fetch();

if ($nutri) {
    $id = $nutri['IdNutri'];
    
    // Probar actualizar a azul
    $testColor = '#3498db';
    $res = $model->actualizarColorTema($id, $testColor);
    echo "Actualización a color #3498db: " . ($res ? "OK ✅" : "ERROR ❌") . "\n";
    
    $perfil = $model->obtenerPorId($id);
    echo "Color recuperado de BD: " . $perfil['Color_Tema'] . " " . ($perfil['Color_Tema'] === $testColor ? "OK ✅" : "ERROR ❌") . "\n";
    
    // Restablecer a verde original
    $model->actualizarColorTema($id, '#2ecc71');
    echo "Restablecido a #2ecc71: OK ✅\n";
} else {
    echo "No hay nutricionistas para probar.\n";
}
