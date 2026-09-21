<?php
// controllers/HeartsController.php
// Controlador para el evaluador cardiovascular oficial OPS/OMS HEARTS.

require_once 'core/HeartsRiskCalculator.php';
require_once 'models/EvaluacionRiesgoCV.php';

class HeartsController {
    private $model;

    public function __construct() {
        $this->model = new EvaluacionRiesgoCV();
    }

    /**
     * API JSON para calcular y opcionalmente guardar la evaluación cardiovascular.
     */
    public function api_calcular() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $idNutri = $_SESSION['IdNutri'] ?? 1;

        $datos = [
            'edad' => (int)($input['edad'] ?? 50),
            'sexo' => $input['sexo'] ?? 'M',
            'fuma' => !empty($input['fuma']) && ($input['fuma'] === '1' || $input['fuma'] === true || $input['fuma'] === 'true'),
            'diabetes' => !empty($input['diabetes']) && ($input['diabetes'] === '1' || $input['diabetes'] === true || $input['diabetes'] === 'true'),
            'pas' => (int)($input['pas'] ?? 120),
            'tiene_colesterol' => !empty($input['tiene_colesterol']) && ($input['tiene_colesterol'] === '1' || $input['tiene_colesterol'] === true || $input['tiene_colesterol'] === 'true'),
            'colesterol_total' => !empty($input['colesterol_total']) ? (float)$input['colesterol_total'] : null,
            'imc' => !empty($input['imc']) ? (float)$input['imc'] : null,
            'antecedente_ecv' => !empty($input['antecedente_ecv']),
            'tiene_erc' => !empty($input['tiene_erc'])
        ];

        $resultado = HeartsRiskCalculator::evaluarRiesgo($datos);

        // Si se especificó id_paciente y guardar=true
        if (!empty($input['id_paciente']) && !empty($input['guardar'])) {
            $idEvaluacion = $this->model->guardarEvaluacion([
                'id_paciente' => (int)$input['id_paciente'],
                'id_nutri' => $idNutri,
                'edad' => $datos['edad'],
                'sexo' => $datos['sexo'],
                'fuma' => $datos['fuma'] ? 1 : 0,
                'diabetes' => $datos['diabetes'] ? 1 : 0,
                'pas' => $datos['pas'],
                'colesterol_total' => $datos['colesterol_total'],
                'imc' => $datos['imc'],
                'antecedente_ecv' => $datos['antecedente_ecv'] ? 1 : 0,
                'tiene_erc' => $datos['tiene_erc'] ? 1 : 0,
                'metodo_evaluacion' => $resultado['metodo'],
                'porcentaje_riesgo' => $resultado['porcentaje_riesgo'],
                'estrato_riesgo' => $resultado['estrato'],
                'color_estrato' => $resultado['color'],
                'es_exclusion_directa' => $resultado['es_exclusion_directa'] ? 1 : 0,
                'recomendaciones_clinicas' => implode(' | ', $resultado['recomendaciones'] ?? []),
                'simulacion_datos' => !empty($input['simulacion_datos']) ? json_encode($input['simulacion_datos']) : null,
                'origen' => $input['origen'] ?? 'Calculadora'
            ]);
            $resultado['id_evaluacion'] = $idEvaluacion;
        }

        echo json_encode(['success' => true, 'resultado' => $resultado]);
        exit();
    }
}
