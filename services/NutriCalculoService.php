<?php
// services/NutriCalculoService.php
// Servicio clínico de cálculo y evaluación antropométrica basada en consensos internacionales (OMS / FAO).

class NutriCalculoService {

    public static function evaluarAntropometria($talla, $peso, $muneca, $pesoUsual, $sexo, $cintura = 0, $cadera = 0, $relacionCCManual = null) {
        $resultados = [
            'imc' => null,
            'imc_diagnostico' => null,
            'peso_ideal' => null,
            'peso_ideal_corregido' => null,
            'pct_peso_ideal' => null,
            'pct_peso_ideal_diagnostico' => null,
            'contextura' => null,
            'pct_peso_usual' => null,
            'relacion_cc' => null,
            'relacion_cc_diagnostico' => null,
            'error' => null
        ];

        try {
            $talla = (float) $talla;
            $peso = (float) $peso;
            $muneca = (float) $muneca;
            $pesoUsual = (float) $pesoUsual;
            $cintura = (float) $cintura;
            $cadera = (float) $cadera;
            $relacionCCManual = !empty($relacionCCManual) ? (float) $relacionCCManual : 0;

            if ($talla > 0 && $peso > 0) {
                // 1. IMC
                $imc = $peso / ($talla * $talla);
                $resultados['imc'] = round($imc, 2);
                $resultados['imc_diagnostico'] = self::diagnosticoIMC($imc);

                // 2. Peso Ideal (Fórmula Robinson/Hamwi adaptada)
                $tallaCm = $talla * 100;
                if ($tallaCm > 150) {
                    if ($sexo === 'M') {
                        $pesoIdeal = (($tallaCm - 150) * 2.72 / 2.5) + 47.7;
                    } else {
                        $pesoIdeal = (($tallaCm - 150) * 2.27 / 2.5) + 45.5;
                    }
                    $resultados['peso_ideal'] = round($pesoIdeal, 2);

                    // % Peso Ideal
                    if ($pesoIdeal > 0) {
                        $pctPI = ($peso / $pesoIdeal) * 100;
                        $resultados['pct_peso_ideal'] = round($pctPI, 2);
                        $resultados['pct_peso_ideal_diagnostico'] = self::diagnosticoPctPI($pctPI);

                        // Peso Ideal Corregido (Si IMC > 24.99 o %PI > 110%)
                        if ($imc > 24.99 || $pctPI > 110) {
                            $pesoCorregido = (($peso - $pesoIdeal) * 0.25) + $pesoIdeal;
                            $resultados['peso_ideal_corregido'] = round($pesoCorregido, 2);
                        }
                    }
                } else {
                    // Fallback para Talla <= 150 (Fórmula de Broca simple)
                    $pesoIdeal = $tallaCm - 100;
                    if ($pesoIdeal > 0) {
                        $resultados['peso_ideal'] = round($pesoIdeal, 2);
                        $pctPI = ($peso / $pesoIdeal) * 100;
                        $resultados['pct_peso_ideal'] = round($pctPI, 2);
                        $resultados['pct_peso_ideal_diagnostico'] = self::diagnosticoPctPI($pctPI);
                    }
                }
            }

            // 3. Contextura
            if ($talla > 0 && $muneca > 0) {
                $tallaCm = $talla * 100;
                $r = $tallaCm / $muneca;
                $resultados['contextura'] = self::diagnosticoContextura($r, $sexo);
            }

            // 4. % Peso Usual
            if ($peso > 0 && $pesoUsual > 0) {
                $pctPU = ($peso / $pesoUsual) * 100;
                $resultados['pct_peso_usual'] = round($pctPU, 2);
            }

            // 5. Relación Cintura / Cadera (ICC) - Manual o Calculado
            if ($relacionCCManual > 0) {
                $resultados['relacion_cc'] = round($relacionCCManual, 2);
                $resultados['relacion_cc_diagnostico'] = self::diagnosticoICC($relacionCCManual, $sexo);
            } elseif ($cintura > 0 && $cadera > 0) {
                $icc = $cintura / $cadera;
                $resultados['relacion_cc'] = round($icc, 2);
                $resultados['relacion_cc_diagnostico'] = self::diagnosticoICC($icc, $sexo);
            }

        } catch (Exception $e) {
            $resultados['error'] = $e->getMessage();
        }

        return $resultados;
    }

    public static function diagnosticoICC($icc, $sexo) {
        if ($sexo === 'M') {
            if ($icc < 0.90) return 'Bajo Riesgo';
            if ($icc <= 0.94) return 'Riesgo Moderado';
            return 'Riesgo Alto (Androide)';
        } else {
            if ($icc < 0.80) return 'Bajo Riesgo';
            if ($icc <= 0.84) return 'Riesgo Moderado';
            return 'Riesgo Alto (Androide)';
        }
    }

    public static function calcularCatabolismo($urea, $diuresis, $protExogenas) {
        try {
            $urea = (float) $urea;
            $diuresis = (float) $diuresis;
            $protExogenas = (float) $protExogenas;

            if ($urea <= 0 || $diuresis <= 0) return null;

            $nuu = $urea * 0.467 * $diuresis;
            
            $ic = null;
            if ($protExogenas > 0) {
                $ic = $nuu - (0.5 * ($protExogenas / 6.25) + 3);
            } else {
                $ic = $nuu - 3;
            }

            return [
                'nuu' => round($nuu, 2),
                'ic' => round($ic, 2)
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    public static function cocienteCetoAnticetogenico($ch, $prot, $grasas) {
        $ch = (float) $ch;
        $prot = (float) $prot;
        $grasas = (float) $grasas;
        
        $ceto = (0.46 * $prot) + (0.9 * $grasas);
        $anticeto = (1 * $ch) + (0.58 * $prot) + (0.1 * $grasas);
        
        if ($anticeto == 0) return null;
        
        return round($ceto / $anticeto, 2);
    }

    private static function diagnosticoIMC($imc) {
        if ($imc < 18.5) return 'Bajo Peso';
        if ($imc >= 18.5 && $imc <= 24.99) return 'Normal';
        if ($imc >= 25 && $imc <= 29.99) return 'Sobrepeso';
        if ($imc >= 30 && $imc <= 34.99) return 'Obesidad Grado I';
        if ($imc >= 35 && $imc <= 39.99) return 'Obesidad Grado II';
        return 'Obesidad Grado III';
    }

    private static function diagnosticoPctPI($pct) {
        if ($pct < 90) return 'Desnutrición';
        if ($pct >= 90 && $pct <= 110) return 'Normal';
        if ($pct > 110 && $pct <= 120) return 'Sobrepeso';
        return 'Obesidad';
    }

    private static function diagnosticoContextura($r, $sexo) {
        if ($sexo === 'M') {
            if ($r > 10.4) return 'Pequeña';
            if ($r >= 9.6 && $r <= 10.4) return 'Mediana';
            return 'Grande';
        } else {
            if ($r > 11.0) return 'Pequeña';
            if ($r >= 10.1 && $r <= 11.0) return 'Mediana';
            return 'Grande';
        }
    }
}
?>
