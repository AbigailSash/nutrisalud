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

    // Comprobar si estamos en la historia clínica para mostrar botones de exportar
    const inHistoriaClinica = document.getElementById('app-container') !== null;
    if (inHistoriaClinica && btnExpVct && btnExpNuu) {
        btnExpVct.classList.remove('d-none');
        btnExpNuu.classList.remove('d-none');
    }

    function calcularTMB() {
        let sexo = calcSexo ? calcSexo.value : '';
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
        // FAO/OMS
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
        
        // Guardar para exportación
        resVct.setAttribute('data-val', vct.toFixed(0));
    }

    function calcularMineral() {
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

        // NUU = Urea * 0.467 * Diuresis
        let nuu = urea * 0.467 * diuresis;
        resNuu.textContent = nuu.toFixed(2);
        resNuu.setAttribute('data-val', nuu.toFixed(2));

        if (!isNaN(prot) && prot > 0) {
            // IC = NUU - (0.5 * (ProteínasExógenas / 6.25) + 3)
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

    // Exportar
    document.querySelectorAll('.btn-exportar').forEach(btn => {
        btn.addEventListener('click', function() {
            let target = this.getAttribute('data-target');
            if (target === 'vct') {
                let val = resVct.getAttribute('data-val');
                let obsArea = document.querySelector('textarea[name="seg_observaciones"]');
                if (obsArea && val) {
                    obsArea.value += '\nVCT Estimado (Calc Rápida): ' + val + ' kcal.';
                    alert('VCT exportado a Observaciones Generales de Seguimiento');
                }
            } else if (target === 'nuu') {
                let val = resNuu.getAttribute('data-val');
                let obsArea = document.querySelector('textarea[name="seg_observaciones"]');
                if (obsArea && val) {
                    obsArea.value += '\nNUU Calculado: ' + val + ' g/día.';
                    let ic = resIc.textContent;
                    if (ic !== '--') obsArea.value += ' | IC: ' + ic;
                    alert('NUU exportado a Observaciones Generales de Seguimiento');
                }
            }
            
            // Cerrar modal
            let modalEl = document.getElementById('modalCalculadoraRapida');
            let modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
            if (modalInst) modalInst.hide();
        });
    });
});
