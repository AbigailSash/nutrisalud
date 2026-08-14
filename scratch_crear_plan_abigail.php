<?php
require 'config/config.php';
require 'config/Conexion.php';
require 'models/PlanAlimentario.php';

$pdo = Conexion::conectar();
$planModel = new PlanAlimentario();

$idPaciente = 1; // Abigail Sash Olmedo (IdNutri 12)
$nombre = "Plan Completo de Descenso Fase 1";
$fecha_inicio = date('Y-m-d');
$fecha_fin = date('Y-m-d', strtotime('+30 days'));
$objetivo = "Descenso de peso paulatino, recomposición corporal.";

$idPlan = $planModel->crearEncabezado($nombre, $fecha_inicio, $fecha_fin, $objetivo, $idPaciente);

if ($idPlan) {
    // Recomendaciones
    $recomendaciones = "¡Hola Abigail!\n\nEste es tu plan de alimentación diseñado específicamente para lograr tus objetivos. Aquí tienes algunas pautas generales importantes:\n\n- Agua: Intenta consumir al menos 2.5 litros de agua al día (ideal 8 a 10 vasos).\n- Infusiones: Puedes tomar mate, té o café sin azúcar, idealmente endulzados con stevia o sin edulcorante.\n- Condimentos: Usa aceite de oliva en crudo (1 cucharada sopera por comida principal). Condimenta libremente con orégano, pimienta, ajo, perejil y jugo de limón. Modera la sal.\n- Actividad Física: Complementa este plan con tus rutinas de entrenamiento 3 a 4 veces por semana.\n\n¡Mucho éxito en esta etapa! Nos vemos en el control.";
    $planModel->guardarRecomendaciones($idPlan, $recomendaciones);

    // Días de la semana (1 a 7)
    $dias = [1, 2, 3, 4, 5, 6, 7];
    
    // IdMomento: 1=Desayuno, 2=Media Mañana, 3=Almuerzo, 4=Merienda, 5=Cena
    $comidas = [
        1 => [
            ['idMomento' => 1, 'alimento' => 'Huevos revueltos (2 unid.) con tostada de pan integral (1 rebanada)', 'cantidad' => '1 porción', 'indic' => 'Acompañar con infusión sin azúcar'],
            ['idMomento' => 3, 'alimento' => 'Pechuga de pollo a la plancha con ensalada mixta y arroz integral', 'cantidad' => '1 plato mediano', 'indic' => 'Arroz: 1/4 del plato. Ensalada: 1/2 plato'],
            ['idMomento' => 4, 'alimento' => 'Yogur natural descremado con arándanos y almendras', 'cantidad' => '1 vaso', 'indic' => 'Almendras: 1 puñado pequeño'],
            ['idMomento' => 5, 'alimento' => 'Filet de merluza al horno con puré de calabaza', 'cantidad' => '1 plato', 'indic' => 'Agregar 1 cdta de aceite de oliva en crudo']
        ],
        2 => [
            ['idMomento' => 1, 'alimento' => 'Avena cocida (Porridge) con manzana rallada y canela', 'cantidad' => '1 bowl pequeño', 'indic' => 'Usar leche descremada o bebida vegetal'],
            ['idMomento' => 3, 'alimento' => 'Wok de fideos integrales con carne magra y vegetales', 'cantidad' => '1 plato hondo', 'indic' => 'Salsa de soja baja en sodio'],
            ['idMomento' => 4, 'alimento' => 'Batido de proteína con banana (media)', 'cantidad' => '1 vaso', 'indic' => 'Ideal post-entreno'],
            ['idMomento' => 5, 'alimento' => 'Tarta individual de espinaca y ricota magra (sin tapa)', 'cantidad' => '1/4 de tarta o 2 porciones', 'indic' => 'Acompañar con tomate en rodajas']
        ],
        3 => [
            ['idMomento' => 1, 'alimento' => 'Tostadas de pan integral con queso untable descremado y mermelada sin azúcar', 'cantidad' => '2 rebanadas', 'indic' => ''],
            ['idMomento' => 3, 'alimento' => 'Milanesa de soja o carne al horno con ensalada de zanahoria, tomate y huevo duro', 'cantidad' => '1 plato', 'indic' => ''],
            ['idMomento' => 4, 'alimento' => 'Fruta fresca de estación con mantequilla de maní', 'cantidad' => '1 unidad + 1 cda', 'indic' => ''],
            ['idMomento' => 5, 'alimento' => 'Ensalada tibia de quinoa, pollo desmenuzado, palta y espinaca', 'cantidad' => '1 plato', 'indic' => 'Palta: 1/4 unidad']
        ]
    ];

    foreach ($dias as $dia) {
        $indiceComida = ($dia % 3) + 1; // Alternar entre 3 opciones
        $menuDia = $comidas[$indiceComida];
        foreach ($menuDia as $item) {
            $planModel->agregarAlimentoAlDetalle($idPlan, $dia, $item['idMomento'], $item['alimento'], $item['cantidad'], $item['indic']);
        }
    }
    echo "Plan completo creado con éxito para Abigail. IdPlan = $idPlan\n";
} else {
    echo "Error al crear el plan.\n";
}
?>
