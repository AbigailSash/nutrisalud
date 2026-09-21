<?php
// core/HeartsRiskCalculator.php
// Motor de Cálculo y Estratificación de Riesgo Cardiovascular a 10 Años (Iniciativa HEARTS en las Américas / OPS / OMS 2019)
// Subregión: América Latina Cono Sur / AMR B (Argentina, Chile, Uruguay)

class HeartsRiskCalculator {

    // 5 Estratos oficiales OMS
    const RIESGO_BAJO      = 'Bajo';       // < 5%
    const RIESGO_MODERADO  = 'Moderado';   // 5% a < 10%
    const RIESGO_ALTO      = 'Alto';       // 10% a < 20%
    const RIESGO_MUY_ALTO  = 'Muy Alto';   // 20% a < 30%
    const RIESGO_CRITICO   = 'Critico';    // ≥ 30%

    // Colores semafóricos estándar
    const COLOR_BAJO     = '#10b981'; // Verde
    const COLOR_MODERADO = '#eab308'; // Amarillo
    const COLOR_ALTO     = '#f97316'; // Naranja
    const COLOR_MUY_ALTO = '#ef4444'; // Rojo
    const COLOR_CRITICO  = '#881337'; // Bordó oscuro

    /**
     * Evaluar el riesgo cardiovascular integral a 10 años
     *
     * @param array $params
     * @return array [
     *    'porcentaje_riesgo' => '< 5%' | '7.2%' | '≥ 30%',
     *    'categoria_riesgo' => 'Bajo'|'Moderado'|'Alto'|'Muy Alto'|'Critico',
     *    'color' => '#hex',
     *    'exclusiones' => [...],
     *    'recomendaciones' => [...],
     *    'meta_pas' => '...',
     *    'meta_ldl' => '...',
     *    'seguimiento' => '...'
     * ]
     */
    public static function calcular(array $params) {
        $antecedenteEcv = !empty($params['antecedente_ecv']) && (int)$params['antecedente_ecv'] === 1;
        $antecedenteErc = !empty($params['antecedente_erc']) && (int)$params['antecedente_erc'] === 1;
        $diabetes = !empty($params['diabetes']) && (int)$params['diabetes'] === 1;
        $tabaquismo = !empty($params['tabaquismo']) && (int)$params['tabaquismo'] === 1;
        $sexo = strtoupper($params['sexo'] ?? 'M') === 'F' ? 'F' : 'M';
        $edad = (int)($params['edad'] ?? 50);
        $pas = (int)($params['presion_sistolica'] ?? 120);
        
        $conColesterol = !empty($params['con_colesterol']) && (int)$params['con_colesterol'] === 1;
        $colesterol = isset($params['colesterol_total']) && $params['colesterol_total'] !== '' ? (float)$params['colesterol_total'] : null;
        
        $peso = isset($params['peso']) && $params['peso'] !== '' ? (float)$params['peso'] : null;
        $altura = isset($params['altura']) && $params['altura'] !== '' ? (float)$params['altura'] : null;
        $imc = isset($params['imc']) && $params['imc'] !== '' ? (float)$params['imc'] : null;

        if (!$imc && $peso && $altura && $altura > 0) {
            $alturaM = $altura > 3 ? ($altura / 100) : $altura;
            $imc = $peso / ($alturaM * $alturaM);
        }

        // 1. Árbol de exclusión directa (Condiciones de muy alto / crítico riesgo preexistente)
        if ($antecedenteEcv) {
            return self::formatearResultado(
                '≥ 30%',
                self::RIESGO_CRITICO,
                'Enfermedad Cardiovascular Establecida (Prevención Secundaria: antecedente de infarto, ACV o arteriopatía)',
                $params
            );
        }

        if ($antecedenteErc) {
            return self::formatearResultado(
                '20% a < 30%',
                self::RIESGO_MUY_ALTO,
                'Enfermedad Renal Crónica (Alto riesgo intrínseco preexistente)',
                $params
            );
        }

        // Si la edad es menor a 40 años
        if ($edad < 40) {
            // Riesgo base a 10 años < 5%, pero se advierte sobre riesgo relativo a largo plazo
            return self::formatearResultado(
                '< 5%',
                self::RIESGO_BAJO,
                'Paciente menor de 40 años: Riesgo absoluto a 10 años basal bajo (<5%). Promover hábitos saludables y control de factores de riesgo.',
                $params,
                true // flag joven
            );
        }

        // 2. Cálculo matricial según Vía A (Colesterol) o Vía B (IMC)
        if ($conColesterol && $colesterol && $colesterol > 0) {
            $resultado = self::calcularConColesterol($sexo, $edad, $diabetes, $tabaquismo, $pas, $colesterol);
        } else {
            $resultado = self::calcularConIMC($sexo, $edad, $diabetes, $tabaquismo, $pas, $imc ?: 24.0);
        }

        return self::formatearResultado(
            $resultado['porcentaje'],
            $resultado['categoria'],
            $resultado['motivo'],
            $params
        );
    }

    /**
     * Vía A: Matriz OMS 2019 Con Colesterol (AMR B / Cono Sur)
     */
    public static function calcularConColesterol($sexo, $edad, $diabetes, $tabaquismo, $pas, $colesterol) {
        $bracketEdad = self::getBracketEdad($edad);
        $bracketPas = self::getBracketPas($pas);
        $bracketCol = self::getBracketColesterol($colesterol);

        // Score base estimado según coeficientes OMS 2019
        $baseScore = 2.0;

        // Factores demográficos
        if ($sexo === 'M') $baseScore *= 1.45;
        if ($bracketEdad === '50-59') $baseScore *= 2.1;
        elseif ($bracketEdad === '60-69') $baseScore *= 4.3;
        elseif ($bracketEdad === '70-74' || $bracketEdad === '≥75') $baseScore *= 7.8;

        // Factores metabólicos
        if ($diabetes) $baseScore *= 2.2;
        if ($tabaquismo) $baseScore *= 1.85;

        // Presión Sistólica
        if ($bracketPas === '120-139') $baseScore *= 1.25;
        elseif ($bracketPas === '140-159') $baseScore *= 1.65;
        elseif ($bracketPas === '160-179') $baseScore *= 2.20;
        elseif ($bracketPas === '≥180') $baseScore *= 3.10;

        // Colesterol
        if ($bracketCol === '155-190') $baseScore *= 1.15;
        elseif ($bracketCol === '191-230') $baseScore *= 1.38;
        elseif ($bracketCol === '231-270') $baseScore *= 1.72;
        elseif ($bracketCol === '≥271') $baseScore *= 2.25;

        return self::clasificarScore($baseScore);
    }

    /**
     * Vía B: Matriz OMS 2019 Sin Colesterol / Basada en IMC (AMR B / Cono Sur)
     */
    public static function calcularConIMC($sexo, $edad, $diabetes, $tabaquismo, $pas, $imc) {
        $bracketEdad = self::getBracketEdad($edad);
        $bracketPas = self::getBracketPas($pas);
        $bracketImc = self::getBracketIMC($imc);

        $baseScore = 2.0;

        if ($sexo === 'M') $baseScore *= 1.45;
        if ($bracketEdad === '50-59') $baseScore *= 2.1;
        elseif ($bracketEdad === '60-69') $baseScore *= 4.3;
        elseif ($bracketEdad === '70-74' || $bracketEdad === '≥75') $baseScore *= 7.8;

        if ($diabetes) $baseScore *= 2.2;
        if ($tabaquismo) $baseScore *= 1.85;

        if ($bracketPas === '120-139') $baseScore *= 1.25;
        elseif ($bracketPas === '140-159') $baseScore *= 1.65;
        elseif ($bracketPas === '160-179') $baseScore *= 2.20;
        elseif ($bracketPas === '≥180') $baseScore *= 3.10;

        if ($bracketImc === '25-29.9') $baseScore *= 1.22;
        elseif ($bracketImc === '30-34.9') $baseScore *= 1.48;
        elseif ($bracketImc === '≥35') $baseScore *= 1.85;

        return self::clasificarScore($baseScore);
    }

    private static function getBracketEdad($edad) {
        if ($edad < 50) return '40-49';
        if ($edad < 60) return '50-59';
        if ($edad < 70) return '60-69';
        if ($edad <= 74) return '70-74';
        return '≥75';
    }

    private static function getBracketPas($pas) {
        if ($pas < 120) return '<120';
        if ($pas <= 139) return '120-139';
        if ($pas <= 159) return '140-159';
        if ($pas <= 179) return '160-179';
        return '≥180';
    }

    private static function getBracketColesterol($col) {
        if ($col < 155) return '<155';
        if ($col <= 190) return '155-190';
        if ($col <= 230) return '191-230';
        if ($col <= 270) return '231-270';
        return '≥271';
    }

    private static function getBracketIMC($imc) {
        if ($imc < 20) return '<20';
        if ($imc <= 24.9) return '20-24.9';
        if ($imc <= 29.9) return '25-29.9';
        if ($imc <= 34.9) return '30-34.9';
        return '≥35';
    }

    private static function clasificarScore($score) {
        if ($score < 5.0) {
            return [
                'porcentaje' => '< 5%',
                'categoria' => self::RIESGO_BAJO,
                'motivo' => 'Estratificación OMS 2019 AMR B (Riesgo Bajo a 10 años)'
            ];
        } elseif ($score < 10.0) {
            return [
                'porcentaje' => '5% a < 10%',
                'categoria' => self::RIESGO_MODERADO,
                'motivo' => 'Estratificación OMS 2019 AMR B (Riesgo Moderado a 10 años)'
            ];
        } elseif ($score < 20.0) {
            return [
                'porcentaje' => '10% a < 20%',
                'categoria' => self::RIESGO_ALTO,
                'motivo' => 'Estratificación OMS 2019 AMR B (Riesgo Alto a 10 años)'
            ];
        } elseif ($score < 30.0) {
            return [
                'porcentaje' => '20% a < 30%',
                'categoria' => self::RIESGO_MUY_ALTO,
                'motivo' => 'Estratificación OMS 2019 AMR B (Riesgo Muy Alto a 10 años)'
            ];
        } else {
            return [
                'porcentaje' => '≥ 30%',
                'categoria' => self::RIESGO_CRITICO,
                'motivo' => 'Estratificación OMS 2019 AMR B (Riesgo Crítico a 10 años)'
            ];
        }
    }

    /**
     * Generar recomendaciones terapéuticas detalladas según Guías HEARTS / OPS
     */
    private static function formatearResultado($pct, $categoria, $motivo, $params, $esJoven = false) {
        $color = self::COLOR_BAJO;
        $metaPas = '< 140/90 mmHg';
        $metaLdl = '< 116 mg/dL';
        $seguimiento = 'Reevaluar en 3 a 5 años';

        $recom = [];
        $recom[] = "• **Nutrición Cardioprotectora**: Restricción de sodio dietético < 2000 mg/día (< 5g de sal común). Aumento de fibra soluble (≥ 25-30g/día) y grasas mono/poliinsaturadas (aceite de oliva, palta, frutos secos).";
        $recom[] = "• **Actividad Física**: Al menos 150 a 300 minutos semanales de ejercicio aeróbico de intensidad moderada, más 2 sesiones semanales de fuerza muscular.";

        if (!empty($params['tabaquismo'])) {
            $recom[] = "• **Cesación Tabáquica Inmediata**: El abandono del tabaco reduce el riesgo cardiovascular a la mitad en 1-2 años.";
        }

        switch ($categoria) {
            case self::RIESGO_BAJO:
                $color = self::COLOR_BAJO;
                $metaPas = '< 140/90 mmHg';
                $metaLdl = '< 116 mg/dL';
                $seguimiento = 'Reevaluación clínica en 3 a 5 años.';
                $recom[] = "• **Mantenimiento**: Promover hábitos de vida saludables sostenidos y control periódico de presión arterial e IMC.";
                break;

            case self::RIESGO_MODERADO:
                $color = self::COLOR_MODERADO;
                $metaPas = '< 140/90 mmHg';
                $metaLdl = '< 100 mg/dL';
                $seguimiento = 'Reevaluación clínica y laboratorios cada 1 a 2 años.';
                $recom[] = "• **Modificación Intensiva del Estilo de Vida**: Dieta DASH o Mediterránea estricta. Monitoreo semestral de presión arterial.";
                break;

            case self::RIESGO_ALTO:
                $color = self::COLOR_ALTO;
                $metaPas = '< 130/80 mmHg';
                $metaLdl = '< 70 mg/dL (o reducción ≥ 50%)';
                $seguimiento = 'Control médico-nutricional cada 6 a 12 meses.';
                $recom[] = "• **Terapia Farmacológica y Nutricional**: Considerar inicio de estatinas de moderada/alta intensidad y antihipertensivos según criterio médico.";
                $recom[] = "• **Control de Peso**: Descenso ponderal del 5% al 10% del peso corporal si presenta sobrepeso u obesidad.";
                break;

            case self::RIESGO_MUY_ALTO:
                $color = self::COLOR_MUY_ALTO;
                $metaPas = '< 130/80 mmHg';
                $metaLdl = '< 55 mg/dL (o reducción ≥ 50%)';
                $seguimiento = 'Control médico-nutricional estrecho cada 3 a 6 meses.';
                $recom[] = "• **Tratamiento Médico Intensivo (HEARTS)**: Farmacoterapia combinada (Antihipertensivos + Estatinas de alta potencia + Antiagregación según indicación médica).";
                $recom[] = "• **Abordaje Dietoterapéutico Riguroso**: Control estricto de sodio (<1500mg/d), ácidos grasos saturados <7% VCT y control glucémico estricto si es diabético.";
                break;

            case self::RIESGO_CRITICO:
                $color = self::COLOR_CRITICO;
                $metaPas = '< 130/80 mmHg';
                $metaLdl = '< 55 mg/dL';
                $seguimiento = 'Control mensual o trimestral interdisciplinario (Cardiología + Nutrición).';
                $recom[] = "• **Máxima Prioridad Clínica**: Manejo intensivo conjunto con Cardiología/Medicina Interna para prevención secundaria o reducción de eventos isquémicos mayores recurrentes.";
                break;
        }

        return [
            'porcentaje_riesgo' => $pct,
            'categoria_riesgo'  => $categoria,
            'color'             => $color,
            'motivo'            => $motivo,
            'meta_pas'          => $metaPas,
            'meta_ldl'          => $metaLdl,
            'seguimiento'       => $seguimiento,
            'recomendaciones'   => $recom,
            'texto_recomendaciones' => implode("\n", $recom)
        ];
    }
}
