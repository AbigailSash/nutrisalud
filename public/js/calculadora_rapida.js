document.addEventListener('DOMContentLoaded', function() {
    // Referencias Tab 1: TMB
    const calcSexo = document.getElementById('calc_sexo');
    const calcEdad = document.getElementById('calc_edad');
    const calcPeso = document.getElementById('calc_peso');
    const calcNaf = document.getElementById('calc_naf');
    const resTmb = document.getElementById('res_tmb');
    const resVct = document.getElementById('res_vct');
    const btnExpVct = document.getElementById('btn_exportar_vct_container');

    // Referencias Tab 2: Minerales
    const calcMineral = document.getElementById('calc_mineral');
    const calcMeq = document.getElementById('calc_meq');
    const resMg = document.getElementById('res_mg');

    // Referencias Tab 3: Catabolismo
    const calcUrea = document.getElementById('calc_urea');
    const calcDiuresis = document.getElementById('calc_diuresis');
    const calcProtExo = document.getElementById('calc_prot_exogenas');
    const resNuu = document.getElementById('res_nuu');
    const resIc = document.getElementById('res_ic');
    const badgeIc = document.getElementById('badge_ic');
    const btnExpNuu = document.getElementById('btn_exportar_nuu_container');

    // Referencias Tab 4: Riesgo Cardiovascular (HEARTS)
    const cvEcv = document.getElementById('modal_cv_ecv');
    const cvErc = document.getElementById('modal_cv_erc');
    const cvSexo = document.getElementById('modal_cv_sexo');
    const cvEdad = document.getElementById('modal_cv_edad');
    const cvDiabetes = document.getElementById('modal_cv_diabetes');
    const cvTabaco = document.getElementById('modal_cv_tabaco');
    const cvPas = document.getElementById('modal_cv_pas');
    const cvVia = document.getElementById('modal_cv_via');
    const cvCol = document.getElementById('modal_cv_col');
    const cvImc = document.getElementById('modal_cv_imc');
    const grupoCol = document.getElementById('modal_cv_grupo_col');
    const grupoImc = document.getElementById('modal_cv_grupo_imc');
    const gridCampos = document.getElementById('modal_cv_campos_grid');

    const resCvPct = document.getElementById('modal_res_cv_pct');
    const resCvBadge = document.getElementById('modal_res_cv_badge');
    const resCvMotivo = document.getElementById('modal_res_cv_motivo');
    const resCvBar = document.getElementById('modal_res_cv_bar');
    const resCvMetaPas = document.getElementById('modal_res_cv_metapas');
    const resCvMetaLdl = document.getElementById('modal_res_cv_metaldl');
    const resCvSeg = document.getElementById('modal_res_cv_seg');

    const simTabaco = document.getElementById('modal_sim_tabaco');
    const simPas = document.getElementById('modal_sim_pas');
    const simImc = document.getElementById('modal_sim_imc');
    const simDeltaBadge = document.getElementById('modal_cv_delta_badge');
    const simMensaje = document.getElementById('modal_sim_mensaje');
    const btnExpCv = document.getElementById('btn_exportar_cv_container');

    // Comprobar si estamos en la historia clínica para mostrar botones de exportar
    const inHistoriaClinica = document.getElementById('app-container') !== null;
    if (inHistoriaClinica) {
        if (btnExpVct) btnExpVct.classList.remove('d-none');
        if (btnExpNuu) btnExpNuu.classList.remove('d-none');
        if (btnExpCv) btnExpCv.classList.remove('d-none');
    }

    function calcularTMB() {
        if (!calcSexo) return;
        let sexo = calcSexo.value;
        let edad = parseFloat(calcEdad.value);
        let peso = parseFloat(calcPeso.value);
        let naf = parseFloat(calcNaf.value);

        if (!sexo || isNaN(edad) || isNaN(peso) || isNaN(naf) || edad <= 0 || peso <= 0 || naf <= 0) {
            resTmb.innerHTML = '-- <small class="fs-6">kcal</small>';
            resVct.innerHTML = '-- <small class="fs-6">kcal</small>';
            if (resVct) resVct.removeAttribute('data-val');
            return;
        }

        let tmb = 0;
        if (sexo === 'M') {
            if (edad < 18) tmb = (17.5 * peso) + 651;
            else if (edad <= 29) tmb = (15.3 * peso) + 679;
            else if (edad <= 59) tmb = (11.6 * peso) + 879;
            else tmb = (13.5 * peso) + 487;
        } else {
            if (edad < 18) tmb = (12.2 * peso) + 746;
            else if (edad <= 29) tmb = (14.7 * peso) + 496;
            else if (edad <= 59) tmb = (8.7 * peso) + 829;
            else tmb = (10.5 * peso) + 596;
        }

        let vct = tmb * naf;

        resTmb.innerHTML = tmb.toFixed(0) + ' <small class="fs-6">kcal</small>';
        resVct.innerHTML = vct.toFixed(0) + ' <small class="fs-6">kcal</small>';
        resVct.setAttribute('data-val', vct.toFixed(0));
    }

    function calcularMineral() {
        if (!calcMineral || !calcMeq) return;
        let factor = parseFloat(calcMineral.value);
        let meq = parseFloat(calcMeq.value);

        if (isNaN(factor) || isNaN(meq) || factor <= 0 || meq <= 0) {
            resMg.innerHTML = '-- <small class="fs-5">mg</small>';
            return;
        }
        let mg = meq * factor;
        resMg.innerHTML = mg.toFixed(2) + ' <small class="fs-5">mg</small>';
    }

    function calcularCatabolismo() {
        if (!calcUrea || !calcDiuresis) return;
        let urea = parseFloat(calcUrea.value);
        let diuresis = parseFloat(calcDiuresis.value);
        let prot = parseFloat(calcProtExo.value);

        if (isNaN(urea) || isNaN(diuresis) || urea <= 0 || diuresis <= 0) {
            resNuu.textContent = '--';
            resIc.textContent = '--';
            badgeIc.textContent = 'N/A';
            badgeIc.className = 'badge bg-secondary mt-1';
            return;
        }

        let nuu = urea * 0.467 * diuresis;
        resNuu.textContent = nuu.toFixed(2);
        resNuu.setAttribute('data-val', nuu.toFixed(2));

        if (!isNaN(prot) && prot > 0) {
            let ic = nuu - (0.5 * (prot / 6.25) + 3);
            resIc.textContent = ic.toFixed(2);

            let diag = '';
            let clase = 'bg-secondary';
            if (ic < 0) { diag = 'NORMAL'; clase = 'bg-success'; }
            else if (ic <= 5) { diag = 'HIPERCATABOLISMO LEVE'; clase = 'bg-warning text-dark'; }
            else if (ic <= 10) { diag = 'MODERADO'; clase = 'bg-warning text-dark'; }
            else { diag = 'SEVERO'; clase = 'bg-danger'; }

            badgeIc.textContent = diag;
            badgeIc.className = 'badge mt-1 ' + clase;
        } else {
            resIc.textContent = '--';
            badgeIc.textContent = 'Faltan Proteínas';
            badgeIc.className = 'badge bg-secondary mt-1';
        }
    }

    // ==========================================
    // CÁLCULO DE RIESGO CARDIOVASCULAR (HEARTS)
    // ==========================================
    function calcularRiesgoCVModal() {
        if (!resCvPct || typeof HeartsRiskCalculator === 'undefined') return;

        const ecv = cvEcv ? cvEcv.checked : false;
        const erc = cvErc ? cvErc.checked : false;

        if (ecv || erc) {
            if (gridCampos) gridCampos.style.opacity = '0.5';
        } else {
            if (gridCampos) gridCampos.style.opacity = '1';
        }

        const via = cvVia ? cvVia.value : 'imc';
        if (via === 'colesterol') {
            if (grupoCol) grupoCol.style.display = 'block';
            if (grupoImc) grupoImc.style.display = 'none';
        } else {
            if (grupoCol) grupoCol.style.display = 'none';
            if (grupoImc) grupoImc.style.display = 'block';
        }

        const params = {
            antecedente_ecv: ecv,
            antecedente_erc: erc,
            sexo: cvSexo ? cvSexo.value : 'M',
            edad: cvEdad ? parseInt(cvEdad.value, 10) : 50,
            diabetes: cvDiabetes ? parseInt(cvDiabetes.value, 10) : 0,
            tabaquismo: cvTabaco ? parseInt(cvTabaco.value, 10) : 0,
            presion_sistolica: cvPas ? parseInt(cvPas.value, 10) : 130,
            con_colesterol: via === 'colesterol',
            colesterol_total: cvCol ? parseFloat(cvCol.value) : null,
            imc: cvImc ? parseFloat(cvImc.value) : 25.0
        };

        const res = HeartsRiskCalculator.evaluar(params);

        resCvPct.textContent = res.porcentaje_riesgo;
        resCvPct.style.color = res.color;
        resCvBadge.textContent = `${res.categoria_riesgo} (${res.porcentaje_riesgo})`;
        resCvBadge.style.backgroundColor = res.color;
        resCvMotivo.textContent = res.motivo;

        let barPct = 15;
        if (res.categoria_riesgo === 'Moderado') barPct = 35;
        else if (res.categoria_riesgo === 'Alto') barPct = 60;
        else if (res.categoria_riesgo === 'Muy Alto') barPct = 85;
        else if (res.categoria_riesgo === 'Critico') barPct = 100;

        resCvBar.style.width = barPct + '%';
        resCvBar.style.backgroundColor = res.color;

        resCvMetaPas.textContent = res.meta_pas;
        resCvMetaLdl.textContent = res.meta_ldl;
        resCvSeg.textContent = res.seguimiento;

        // Guardar para exportar
        resCvPct.setAttribute('data-val', `${res.categoria_riesgo} (${res.porcentaje_riesgo})`);
        resCvPct.setAttribute('data-motivo', res.motivo);

        // Actualizar simulador
        simularWhatIfModal(params);
    }

    function simularWhatIfModal(paramsBase) {
        if (!simDeltaBadge || !simMensaje || typeof HeartsRiskCalculator === 'undefined') return;

        const cambios = {
            tabaquismo: simTabaco ? parseInt(simTabaco.value, 10) : 0,
            presion_sistolica: simPas ? parseInt(simPas.value, 10) : 120,
            imc: simImc ? parseFloat(simImc.value) : 23.5
        };

        const simRes = HeartsRiskCalculator.simularEscenario(paramsBase, cambios);
        simDeltaBadge.textContent = `Δ -${simRes.reduccion_puntos} pts`;
        simMensaje.innerHTML = simRes.mensaje;
    }

    // Listeners Tab 1
    if (calcSexo) {
        [calcSexo, calcEdad, calcPeso, calcNaf].forEach(el => {
            el.addEventListener('input', calcularTMB);
            el.addEventListener('change', calcularTMB);
        });
        calcularTMB();
    }

    // Listeners Tab 2
    if (calcMineral) {
        calcMineral.addEventListener('change', calcularMineral);
        calcMeq.addEventListener('input', calcularMineral);
    }

    // Listeners Tab 3
    if (calcUrea) {
        [calcUrea, calcDiuresis, calcProtExo].forEach(el => {
            el.addEventListener('input', calcularCatabolismo);
        });
    }

    // Listeners Tab 4: Riesgo CV
    if (cvSexo) {
        [cvEcv, cvErc, cvSexo, cvEdad, cvDiabetes, cvTabaco, cvPas, cvVia, cvCol, cvImc].forEach(el => {
            if (el) {
                el.addEventListener('input', calcularRiesgoCVModal);
                el.addEventListener('change', calcularRiesgoCVModal);
            }
        });

        if (simTabaco && simPas && simImc) {
            [simTabaco, simPas, simImc].forEach(el => {
                el.addEventListener('input', () => {
                    const via = cvVia ? cvVia.value : 'imc';
                    const params = {
                        antecedente_ecv: cvEcv ? cvEcv.checked : false,
                        antecedente_erc: cvErc ? cvErc.checked : false,
                        sexo: cvSexo ? cvSexo.value : 'M',
                        edad: cvEdad ? parseInt(cvEdad.value, 10) : 50,
                        diabetes: cvDiabetes ? parseInt(cvDiabetes.value, 10) : 0,
                        tabaquismo: cvTabaco ? parseInt(cvTabaco.value, 10) : 0,
                        presion_sistolica: cvPas ? parseInt(cvPas.value, 10) : 130,
                        con_colesterol: via === 'colesterol',
                        colesterol_total: cvCol ? parseFloat(cvCol.value) : null,
                        imc: cvImc ? parseFloat(cvImc.value) : 25.0
                    };
                    simularWhatIfModal(params);
                });
                el.addEventListener('change', () => {
                    const via = cvVia ? cvVia.value : 'imc';
                    const params = {
                        antecedente_ecv: cvEcv ? cvEcv.checked : false,
                        antecedente_erc: cvErc ? cvErc.checked : false,
                        sexo: cvSexo ? cvSexo.value : 'M',
                        edad: cvEdad ? parseInt(cvEdad.value, 10) : 50,
                        diabetes: cvDiabetes ? parseInt(cvDiabetes.value, 10) : 0,
                        tabaquismo: cvTabaco ? parseInt(cvTabaco.value, 10) : 0,
                        presion_sistolica: cvPas ? parseInt(cvPas.value, 10) : 130,
                        con_colesterol: via === 'colesterol',
                        colesterol_total: cvCol ? parseFloat(cvCol.value) : null,
                        imc: cvImc ? parseFloat(cvImc.value) : 25.0
                    };
                    simularWhatIfModal(params);
                });
            });
        }

        calcularRiesgoCVModal();
    }

    // Exportar a notas de consulta
    document.querySelectorAll('.btn-exportar').forEach(btn => {
        btn.addEventListener('click', function() {
            let target = this.getAttribute('data-target');
            let obsArea = document.querySelector('textarea[name="seg_observaciones"]');
            
            if (target === 'vct') {
                let val = resVct.getAttribute('data-val');
                if (obsArea && val) {
                    obsArea.value += '\nVCT Estimado (Calc Rápida): ' + val + ' kcal.';
                    alert('VCT exportado a Observaciones Generales de Seguimiento');
                }
            } else if (target === 'nuu') {
                let val = resNuu.getAttribute('data-val');
                if (obsArea && val) {
                    obsArea.value += '\nNUU Calculado: ' + val + ' g/día.';
                    let ic = resIc.textContent;
                    if (ic !== '--') obsArea.value += ' | IC: ' + ic;
                    alert('NUU exportado a Observaciones Generales de Seguimiento');
                }
            } else if (target === 'riesgo_cv') {
                let val = resCvPct.getAttribute('data-val');
                let motivo = resCvPct.getAttribute('data-motivo');
                if (obsArea && val) {
                    obsArea.value += `\n[Evaluación Riesgo Cardiovascular a 10 Años HEARTS/OMS]: Estrato ${val} - ${motivo}`;
                    alert('Riesgo Cardiovascular exportado a Observaciones Generales de Seguimiento');
                }
            }
            
            // Cerrar modal
            let modalEl = document.getElementById('modalCalculadoraRapida');
            if (modalEl && typeof bootstrap !== 'undefined') {
                let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
                if (modalInst) modalInst.hide();
            }
        });
    });
});
