<?php

class NutriReglasClinicasService {
    
    /**
     * Mapea los signos clínicos (NFPE) activos a reglas dietoterápicas.
     * @param array $signos_activos Array de claves de signos (ej: ['nfpe_edema', 'nfpe_glositis'])
     * @return array Reglas y alertas generadas.
     */
    public static function evaluarReglasPorSignos($signos_activos) {
        $alertas = [];
        $restricciones = [
            'sodio_max' => null,
            'prot_min' => null,
            'requiere_hierro' => false,
            'requiere_complejo_b' => false,
            'verificar_acidos_grasos' => false
        ];

        if (empty($signos_activos) || !is_array($signos_activos)) {
            return ['alertas' => $alertas, 'restricciones' => $restricciones];
        }

        // 1. Edema / Ascitis
        if (in_array('nfpe_edema', $signos_activos) || in_array('nfpe_ascitis', $signos_activos)) {
            $alertas[] = [
                'tipo' => 'danger',
                'mensaje' => '⚠️ Paciente con Edema/Ascitis activo: Sugerencia de restricción de Sodio (< 2000mg/día). Considerar descuento de peso seco.'
            ];
            $restricciones['sodio_max'] = 2000;
        }

        // 2. Pérdida de Masa Muscular / Atrofia Temporal / Bichat
        if (in_array('nfpe_temporal', $signos_activos) || in_array('nfpe_bichat', $signos_activos)) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => '⚠️ Riesgo de Sarcopenia/Consunción: Elevar meta de Proteínas (sugerido > 1.2g - 1.5g/kg peso). Monitorear relación Calorías/Nitrógeno.'
            ];
            $restricciones['prot_min'] = 1.2;
        }

        // 3. Glositis / Coiloniquia (Micro-nutrientes)
        if (in_array('nfpe_glositis', $signos_activos) || in_array('nfpe_coiloniquia', $signos_activos)) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => '⚠️ Signos carenciales (Glositis/Coiloniquia): Enriquecer con Hierro, Folato y Complejo B.'
            ];
            $restricciones['requiere_hierro'] = true;
            $restricciones['requiere_complejo_b'] = true;
        }

        // 4. Piel seca / Xantelasmas (Lípidos)
        if (in_array('nfpe_xantelasmas', $signos_activos)) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => '⚠️ Xantelasmas detectados: Verificar perfil lipídico y proporción de Ácidos Grasos (Saturados vs Poliinsaturados).'
            ];
            $restricciones['verificar_acidos_grasos'] = true;
        }

        return [
            'alertas' => $alertas,
            'restricciones' => $restricciones
        ];
    }
}
?>
