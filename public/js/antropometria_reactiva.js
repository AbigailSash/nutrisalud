document.addEventListener('DOMContentLoaded', function() {
    const inputTalla = document.getElementById('antropo_talla');
    const inputPeso = document.getElementById('antropo_peso');
    const inputMuneca = document.getElementById('antropo_muneca');
    const inputPesoUsual = document.getElementById('antropo_peso_usual');
    
    // Circunferencias Cintura / Cadera
    const inputCintura = document.getElementById('antropo_cintura');
    const inputCadera = document.getElementById('antropo_cadera');
    const inputRcc = document.getElementById('antropo_rcc');
    const badgeRcc = document.getElementById('badge_rcc');

    const appContainer = document.getElementById('app-container');
    const sexoPaciente = (appContainer ? appContainer.getAttribute('data-sexo') : 'M') || 'M';

    // Elements to update in stats panel
    const resImc = document.getElementById('res_imc');
    const badgeImc = document.getElementById('badge_imc');
    
    const resPi = document.getElementById('res_pi');
    const resPic = document.getElementById('res_pic');
    
    const resPctPi = document.getElementById('res_pct_pi');
    const badgePctPi = document.getElementById('badge_pct_pi');
    
    const resContextura = document.getElementById('res_contextura');
    const resPctPu = document.getElementById('res_pct_pu');

    // Función para actualizar el badge de diagnóstico de RCC / ICC
    function actualizarDiagnosticoRcc(rcc) {
        if (!badgeRcc) return;
        if (!isNaN(rcc) && rcc > 0) {
            let diagRcc = '';
            let claseRcc = 'bg-secondary text-white';
            if (sexoPaciente === 'M') {
                if (rcc < 0.90) { diagRcc = 'Bajo Riesgo'; claseRcc = 'bg-success text-white'; }
                else if (rcc <= 0.94) { diagRcc = 'Riesgo Moderado'; claseRcc = 'bg-warning text-dark'; }
                else { diagRcc = 'Alto Riesgo'; claseRcc = 'bg-danger text-white'; }
            } else {
                if (rcc < 0.80) { diagRcc = 'Bajo Riesgo'; claseRcc = 'bg-success text-white'; }
                else if (rcc <= 0.84) { diagRcc = 'Riesgo Moderado'; claseRcc = 'bg-warning text-dark'; }
                else { diagRcc = 'Alto Riesgo'; claseRcc = 'bg-danger text-white'; }
            }
            badgeRcc.textContent = diagRcc;
            badgeRcc.className = 'badge d-flex align-items-center ' + claseRcc;
        } else {
            badgeRcc.textContent = '--';
            badgeRcc.className = 'badge d-flex align-items-center bg-secondary text-white';
        }
    }

    // Cálculo automático de RCC a partir de Cintura y Cadera
    function calcularRccDesdeCircunferencias() {
        let cintura = inputCintura ? parseFloat(inputCintura.value) : 0;
        let cadera = inputCadera ? parseFloat(inputCadera.value) : 0;

        if (!isNaN(cintura) && cintura > 0 && !isNaN(cadera) && cadera > 0) {
            let rcc = cintura / cadera;
            if (inputRcc) inputRcc.value = rcc.toFixed(2);
            actualizarDiagnosticoRcc(rcc);
        } else {
            // Si el campo tiene un valor manual previo, lo evaluamos
            let manualRcc = inputRcc ? parseFloat(inputRcc.value) : 0;
            actualizarDiagnosticoRcc(manualRcc);
        }
    }

    function calcularAntropometria() {
        let talla = inputTalla ? parseFloat(inputTalla.value) : 0;
        let peso = inputPeso ? parseFloat(inputPeso.value) : 0;
        let muneca = inputMuneca ? parseFloat(inputMuneca.value) : 0;
        let pesoUsual = inputPesoUsual ? parseFloat(inputPesoUsual.value) : 0;

        // 1. Relación Cintura / Cadera
        calcularRccDesdeCircunferencias();

        // 2. IMC y Métricas de Talla/Peso
        if (isNaN(talla) || talla <= 0) return;
        
        let imc = 0;
        let pesoIdeal = 0;
        let pctPi = 0;
        let pesoCorregido = 0;
        let tallaCm = talla * 100;

        // IMC
        if (!isNaN(peso) && peso > 0) {
            imc = peso / (talla * talla);
            if (resImc) resImc.textContent = imc.toFixed(2);
            
            let diagImc = '';
            let claseImc = 'bg-secondary';
            if (imc < 18.5) { diagImc = 'Bajo Peso'; claseImc = 'bg-warning text-dark'; }
            else if (imc <= 24.99) { diagImc = 'Normal'; claseImc = 'bg-success'; }
            else if (imc <= 29.99) { diagImc = 'Sobrepeso'; claseImc = 'bg-warning text-dark'; }
            else if (imc <= 34.99) { diagImc = 'Obesidad Grado I'; claseImc = 'bg-danger'; }
            else if (imc <= 39.99) { diagImc = 'Obesidad Grado II'; claseImc = 'bg-danger'; }
            else { diagImc = 'Obesidad Grado III'; claseImc = 'bg-danger'; }
            
            if (badgeImc) {
                badgeImc.textContent = diagImc;
                badgeImc.className = 'badge mt-1 ' + claseImc;
            }
        } else {
            if (resImc) resImc.textContent = '--';
            if (badgeImc) {
                badgeImc.textContent = 'N/A';
                badgeImc.className = 'badge bg-secondary mt-1';
            }
        }

        // Peso Ideal & % Peso Ideal & Corregido
        if (tallaCm > 150) {
            if (sexoPaciente === 'M') {
                pesoIdeal = ((tallaCm - 150) * 2.72 / 2.5) + 47.7;
            } else {
                pesoIdeal = ((tallaCm - 150) * 2.27 / 2.5) + 45.5;
            }
            if (resPi) resPi.textContent = pesoIdeal.toFixed(2) + ' kg';

            if (!isNaN(peso) && peso > 0) {
                pctPi = (peso / pesoIdeal) * 100;
                if (resPctPi) resPctPi.textContent = pctPi.toFixed(2) + ' %';
                
                let diagPct = '';
                let clasePct = 'bg-secondary';
                if (pctPi < 90) { diagPct = 'Desnutrición'; clasePct = 'bg-danger'; }
                else if (pctPi <= 110) { diagPct = 'Normal'; clasePct = 'bg-success'; }
                else if (pctPi <= 120) { diagPct = 'Sobrepeso'; clasePct = 'bg-warning text-dark'; }
                else { diagPct = 'Obesidad'; clasePct = 'bg-danger'; }
                
                if (badgePctPi) {
                    badgePctPi.textContent = diagPct;
                    badgePctPi.className = 'badge mt-1 ' + clasePct;
                }

                if (imc > 24.99 || pctPi > 110) {
                    pesoCorregido = ((peso - pesoIdeal) * 0.25) + pesoIdeal;
                    if (resPic) resPic.textContent = pesoCorregido.toFixed(2) + ' kg';
                } else {
                    if (resPic) resPic.textContent = '--';
                }
            } else {
                if (resPctPi) resPctPi.textContent = '-- %';
                if (badgePctPi) {
                    badgePctPi.textContent = 'N/A';
                    badgePctPi.className = 'badge bg-secondary mt-1';
                }
                if (resPic) resPic.textContent = '--';
            }
        } else {
            if (resPi) resPi.textContent = '-- kg';
            if (resPic) resPic.textContent = '--';
            if (resPctPi) resPctPi.textContent = '-- %';
            if (badgePctPi) {
                badgePctPi.textContent = 'N/A';
                badgePctPi.className = 'badge bg-secondary mt-1';
            }
        }

        // Contextura
        if (!isNaN(muneca) && muneca > 0) {
            let r = tallaCm / muneca;
            let ctx = '';
            if (sexoPaciente === 'M') {
                if (r > 10.4) ctx = 'Pequeña';
                else if (r >= 9.6) ctx = 'Mediana';
                else ctx = 'Grande';
            } else {
                if (r > 11.0) ctx = 'Pequeña';
                else if (r >= 10.1) ctx = 'Mediana';
                else ctx = 'Grande';
            }
            if (resContextura) resContextura.textContent = ctx;
        } else {
            if (resContextura) resContextura.textContent = '--';
        }

        // % Peso Usual
        if (!isNaN(peso) && peso > 0 && !isNaN(pesoUsual) && pesoUsual > 0) {
            let pctPu = (peso / pesoUsual) * 100;
            if (resPctPu) resPctPu.textContent = pctPu.toFixed(2);
        } else {
            if (resPctPu) resPctPu.textContent = '--';
        }
    }

    // Event listeners para cálculo automático base
    const inputs = [inputTalla, inputPeso, inputMuneca, inputPesoUsual, inputCintura, inputCadera];
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', calcularAntropometria);
            input.addEventListener('change', calcularAntropometria);
        }
    });

    // Event listener para cuando el profesional edita MANUALMENTE la Relación Cintura/Cadera
    if (inputRcc) {
        inputRcc.addEventListener('input', function() {
            let valManual = parseFloat(this.value);
            actualizarDiagnosticoRcc(valManual);
        });
        inputRcc.addEventListener('change', function() {
            let valManual = parseFloat(this.value);
            actualizarDiagnosticoRcc(valManual);
        });
    }
    
    // Cálculo inicial reactivo al cargar
    calcularAntropometria();
    if (inputRcc && inputRcc.value) {
        actualizarDiagnosticoRcc(parseFloat(inputRcc.value));
    }
});
