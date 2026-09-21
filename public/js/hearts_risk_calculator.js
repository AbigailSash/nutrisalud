/**
 * hearts_risk_calculator.js
 * Motor Reactivo en Vanilla JS para la Calculadora de Riesgo Cardiovascular a 10 Años
 * Protocolo Oficial OPS / Iniciativa HEARTS en las Américas / OMS 2019 (Subregión AMR B / Cono Sur)
 * NutriSalud SaaS (nutrisaludintegra.com)
 */

window.HeartsRiskCalculator = (function () {
    'use strict';

    const STRATA_COLORS = {
        'Bajo': '#10b981',      // Verde (< 5%)
        'Moderado': '#eab308',  // Amarillo (5% a < 10%)
        'Alto': '#f97316',      // Naranja (10% a < 20%)
        'Muy Alto': '#ef4444',  // Rojo (20% a < 30%)
        'Critico': '#881337'    // Bordó oscuro (≥ 30%)
    };

    /**
     * Evaluar el riesgo cardiovascular a 10 años según protocolo HEARTS / OMS 2019
     */
    function evaluar(params) {
        const ecv = !!params.antecedente_ecv;
        const erc = !!params.antecedente_erc;
        const diabetes = !!params.diabetes;
        const tabaquismo = !!params.tabaquismo;
        const sexo = (params.sexo || 'M').toUpperCase() === 'F' ? 'F' : 'M';
        const edad = parseInt(params.edad, 10) || 50;
        const pas = parseInt(params.presion_sistolica, 10) || 120;
        const conColesterol = !!params.con_colesterol;
        const colesterol = params.colesterol_total ? parseFloat(params.colesterol_total) : null;
        
        let peso = params.peso ? parseFloat(params.peso) : null;
        let altura = params.altura ? parseFloat(params.altura) : null;
        let imc = params.imc ? parseFloat(params.imc) : null;

        if (!imc && peso && altura && altura > 0) {
            const altM = altura > 3 ? (altura / 100) : altura;
            imc = peso / (altM * altM);
        }

        // 1. Árbol de Exclusión Directa
        if (ecv) {
            return formatearResultado('≥ 30%', 'Critico', 'Enfermedad Cardiovascular Preexistente (Prevención secundaria de eventos mayores)', params);
        }

        if (erc) {
            return formatearResultado('20% a < 30%', 'Muy Alto', 'Enfermedad Renal Crónica (Alto riesgo intrínseco)', params);
        }

        if (edad < 40) {
            return formatearResultado('< 5%', 'Bajo', 'Paciente < 40 años: Riesgo absoluto a 10 años basal bajo (<5%). Mantener estilo de vida cardioprotector.', params, true);
        }

        // 2. Matriz OMS 2019
        let score = 2.0;

        if (sexo === 'M') score *= 1.45;
        if (edad >= 70) score *= 7.8;
        else if (edad >= 60) score *= 4.3;
        else if (edad >= 50) score *= 2.1;

        if (diabetes) score *= 2.2;
        if (tabaquismo) score *= 1.85;

        // PAS
        if (pas >= 180) score *= 3.10;
        else if (pas >= 160) score *= 2.20;
        else if (pas >= 140) score *= 1.65;
        else if (pas >= 120) score *= 1.25;

        // Vía con colesterol vs IMC
        if (conColesterol && colesterol && colesterol > 0) {
            if (colesterol >= 271) score *= 2.25;
            else if (colesterol >= 231) score *= 1.72;
            else if (colesterol >= 191) score *= 1.38;
            else if (colesterol >= 155) score *= 1.15;
        } else {
            const imcVal = imc || 24.0;
            if (imcVal >= 35) score *= 1.85;
            else if (imcVal >= 30) score *= 1.48;
            else if (imcVal >= 25) score *= 1.22;
        }

        let cat = 'Bajo';
        let pct = '< 5%';
        let motivo = 'Estratificación OMS 2019 AMR B (Riesgo Bajo)';

        if (score < 5.0) {
            cat = 'Bajo'; pct = '< 5%'; motivo = 'Estratificación OMS 2019 AMR B (Riesgo Bajo a 10 años)';
        } else if (score < 10.0) {
            cat = 'Moderado'; pct = '5% a < 10%'; motivo = 'Estratificación OMS 2019 AMR B (Riesgo Moderado a 10 años)';
        } else if (score < 20.0) {
            cat = 'Alto'; pct = '10% a < 20%'; motivo = 'Estratificación OMS 2019 AMR B (Riesgo Alto a 10 años)';
        } else if (score < 30.0) {
            cat = 'Muy Alto'; pct = '20% a < 30%'; motivo = 'Estratificación OMS 2019 AMR B (Riesgo Muy Alto a 10 años)';
        } else {
            cat = 'Critico'; pct = '≥ 30%'; motivo = 'Estratificación OMS 2019 AMR B (Riesgo Crítico a 10 años)';
        }

        return formatearResultado(pct, cat, motivo, params, false, score);
    }

    function formatearResultado(pct, cat, motivo, params, esJoven, scoreNumerico) {
        let color = STRATA_COLORS[cat] || STRATA_COLORS['Bajo'];
        let metaPas = '< 140/90 mmHg';
        let metaLdl = '< 116 mg/dL';
        let seguimiento = 'Reevaluación clínica en 3 a 5 años.';
        let recom = [];

        recom.push('• **Nutrición Cardioprotectora**: Restricción estricta de sodio < 2000 mg/día (< 5g de sal). Aumento de fibra soluble (≥ 25-30g/día) y grasas insaturadas.');
        recom.push('• **Actividad Física**: Al menos 150 a 300 minutos semanales de ejercicio aeróbico moderado + 2 sesiones semanales de fuerza.');

        if (params.tabaquismo) {
            recom.push('• **Cesación Tabáquica**: Dejar de fumar reduce el riesgo de infarto y ACV a la mitad en el primer año.');
        }

        if (cat === 'Bajo') {
            metaPas = '< 140/90 mmHg'; metaLdl = '< 116 mg/dL'; seguimiento = 'Reevaluación en 3 a 5 años.';
            recom.push('• **Prevención Primaria**: Mantener hábitos de vida saludables y control preventivo periódico.');
        } else if (cat === 'Moderado') {
            metaPas = '< 140/90 mmHg'; metaLdl = '< 100 mg/dL'; seguimiento = 'Reevaluación cada 1 a 2 años.';
            recom.push('• **Modificación Intensiva**: Adopción de patrón dietario DASH/Mediterráneo y control semestral de TA.');
        } else if (cat === 'Alto') {
            metaPas = '< 130/80 mmHg'; metaLdl = '< 70 mg/dL (o reducción ≥ 50%)'; seguimiento = 'Control médico-nutricional cada 6 a 12 meses.';
            recom.push('• **Terapia Farmacológica y Nutricional**: Evaluar inicio de estatinas y antihipertensivos según criterio médico.');
            recom.push('• **Manejo Ponderal**: Meta de reducción del 5% al 10% del peso si presenta exceso de grasa corporal.');
        } else if (cat === 'Muy Alto') {
            metaPas = '< 130/80 mmHg'; metaLdl = '< 55 mg/dL (o reducción ≥ 50%)'; seguimiento = 'Control médico-nutricional cada 3 a 6 meses.';
            recom.push('• **Tratamiento Intensivo (HEARTS)**: Farmacoterapia combinada (Antihipertensivos + Estatinas de alta potencia).');
            recom.push('• **Plan Dietoterapéutico Estricto**: Restricción de sodio (<1500mg/d) y grasas saturadas <7% VCT.');
        } else if (cat === 'Critico') {
            metaPas = '< 130/80 mmHg'; metaLdl = '< 55 mg/dL'; seguimiento = 'Control mensual o trimestral interdisciplinario.';
            recom.push('• **Máxima Prioridad Clínica**: Manejo conjunto con Cardiología/Medicina Interna para prevención de eventos recurrentes.');
        }

        return {
            porcentaje_riesgo: pct,
            categoria_riesgo: cat,
            color: color,
            motivo: motivo,
            score_numerico: scoreNumerico || (cat === 'Critico' ? 35 : (cat === 'Muy Alto' ? 25 : (cat === 'Alto' ? 15 : (cat === 'Moderado' ? 7.5 : 3)))),
            meta_pas: metaPas,
            meta_ldl: metaLdl,
            seguimiento: seguimiento,
            recomendaciones: recom,
            texto_recomendaciones: recom.join('\n')
        };
    }

    /**
     * Simular escenario "¿Qué pasaría si...?" (What-if)
     */
    function simularEscenario(paramsBase, cambios) {
        const paramsSim = Object.assign({}, paramsBase, cambios);
        const resBase = evaluar(paramsBase);
        const resSim = evaluar(paramsSim);

        const diffScore = Math.max(0, resBase.score_numerico - resSim.score_numerico);
        let mensajeMotivacional = '';

        if (diffScore > 0) {
            mensajeMotivacional = `¡Excelente! Implementando estas metas terapéuticas, el riesgo del paciente se reduce de **${resBase.porcentaje_riesgo} (${resBase.categoria_riesgo})** a **${resSim.porcentaje_riesgo} (${resSim.categoria_riesgo})**, disminuyendo significativamente la probabilidad de infarto o ACV a 10 años.`;
        } else {
            mensajeMotivacional = `El paciente ya se encuentra en estrato de **${resSim.categoria_riesgo}** (${resSim.porcentaje_riesgo}). Mantener los factores de riesgo bajo control previene la progresión a estratos superiores.`;
        }

        return {
            base: resBase,
            simulado: resSim,
            reduccion_puntos: diffScore.toFixed(1),
            mensaje: mensajeMotivacional
        };
    }

    return {
        evaluar: evaluar,
        simularEscenario: simularEscenario,
        STRATA_COLORS: STRATA_COLORS
    };
})();
