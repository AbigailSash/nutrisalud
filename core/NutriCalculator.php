<?php
// core/NutriCalculator.php

class NutriCalculator {
    
    /**
     * Calcula el Índice de Masa Corporal (IMC) y devuelve el valor y el diagnóstico.
     * @param float $peso en kg
     * @param float $estatura en metros
     * @return array ['valor' => float, 'diagnostico' => string]
     */
    public static function calcularIMC($peso, $estatura) {
        if ($estatura <= 0) return ['valor' => 0, 'diagnostico' => 'N/A'];
        
        $imc = $peso / ($estatura * $estatura);
        $imc = round($imc, 1);
        
        $diagnostico = 'N/A';
        if ($imc < 18.5) {
            $diagnostico = 'Bajo peso';
        } elseif ($imc >= 18.5 && $imc <= 24.9) {
            $diagnostico = 'Normopeso';
        } elseif ($imc >= 25 && $imc <= 29.9) {
            $diagnostico = 'Sobrepeso';
        } elseif ($imc >= 30 && $imc <= 34.9) {
            $diagnostico = 'Obesidad I';
        } elseif ($imc >= 35 && $imc <= 39.9) {
            $diagnostico = 'Obesidad II';
        } elseif ($imc >= 40) {
            $diagnostico = 'Obesidad III';
        }
        
        return [
            'valor' => $imc,
            'diagnostico' => $diagnostico
        ];
    }

    /**
     * Calcula el Peso Ideal (Lorentz).
     * @param float $estatura_cm
     * @param string $sexo 'M' para masculino, 'F' para femenino
     * @return float
     */
    public static function calcularPesoIdeal($estatura_cm, $sexo) {
        if ($estatura_cm <= 100) return 0;
        
        if ($sexo === 'M') {
            return round(($estatura_cm - 100) - (($estatura_cm - 150) / 4), 1);
        } else {
            return round(($estatura_cm - 100) - (($estatura_cm - 150) / 2.5), 1);
        }
    }

    /**
     * Calcula el Porcentaje de Peso Habitual (%PH)
     * @param float $pesoActual
     * @param float $pesoHabitual
     * @return float
     */
    public static function calcularPorcentajePesoHabitual($pesoActual, $pesoHabitual) {
        if ($pesoHabitual <= 0) return 0;
        return round(($pesoActual / $pesoHabitual) * 100, 1);
    }

    /**
     * Calcula el Índice Cintura-Cadera (ICC)
     * @param float $cintura cm
     * @param float $cadera cm
     * @return float
     */
    public static function calcularICC($cintura, $cadera) {
        if ($cadera <= 0) return 0;
        return round($cintura / $cadera, 2);
    }

    /**
     * Calcula el Gasto Energético Basal (GEB) usando Mifflin-St. Jeor.
     * @param float $peso en kg
     * @param float $estatura_cm
     * @param int $edad
     * @param string $sexo 'M' o 'F'
     * @return float
     */
    public static function calcularGEB($peso, $estatura_cm, $edad, $sexo) {
        if ($sexo === 'M') {
            return round((10 * $peso) + (6.25 * $estatura_cm) - (5 * $edad) + 5, 0);
        } else {
            return round((10 * $peso) + (6.25 * $estatura_cm) - (5 * $edad) - 161, 0);
        }
    }

    /**
     * Calcula el Gasto Energético Total (GET).
     * @param float $geb
     * @param float $naf
     * @return float
     */
    public static function calcularGET($geb, $naf) {
        return round($geb * $naf, 0);
    }
}
?>
