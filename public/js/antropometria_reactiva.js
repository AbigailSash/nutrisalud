document.addEventListener('DOMContentLoaded', function() {
    const inputTalla = document.getElementById('antropo_talla');
    const inputPeso = document.getElementById('antropo_peso');
    const inputMuneca = document.getElementById('antropo_muneca');
    const inputPesoUsual = document.getElementById('antropo_peso_usual');
    const sexoPaciente = document.getElementById('app-container').getAttribute('data-sexo') || 'M';

    // Elements to update
    const resImc = document.getElementById('res_imc');
    const badgeImc = document.getElementById('badge_imc');
    
    const resPi = document.getElementById('res_pi');
    const resPic = document.getElementById('res_pic');
    
    const resPctPi = document.getElementById('res_pct_pi');
    const badgePctPi = document.getElementById('badge_pct_pi');
    
    const resContextura = document.getElementById('res_contextura');
    const resPctPu = document.getElementById('res_pct_pu');

    function calcularAntropometria() {
        let talla = parseFloat(inputTalla.value);
        let peso = parseFloat(inputPeso.value);
        let muneca = parseFloat(inputMuneca.value);
        let pesoUsual = parseFloat(inputPesoUsual.value);

        if (isNaN(talla) || talla <= 0) return; // Need at least height to do some calculations
        
        let imc = 0;
        let pesoIdeal = 0;
        let pctPi = 0;
        let pesoCorregido = 0;
        let tallaCm = talla * 100;

        // 1. IMC
        if (!isNaN(peso) && peso > 0) {
            imc = peso / (talla * talla);
            resImc.textContent = imc.toFixed(2);
            
            let diagImc = '';
            let claseImc = 'bg-secondary';
            if (imc < 18.5) { diagImc = 'Bajo Peso'; claseImc = 'bg-warning text-dark'; }
            else if (imc <= 24.99) { diagImc = 'Normal'; claseImc = 'bg-success'; }
            else if (imc <= 29.99) { diagImc = 'Sobrepeso'; claseImc = 'bg-warning text-dark'; }
            else if (imc <= 34.99) { diagImc = 'Obesidad Grado I'; claseImc = 'bg-danger'; }
            else if (imc <= 39.99) { diagImc = 'Obesidad Grado II'; claseImc = 'bg-danger'; }
            else { diagImc = 'Obesidad Grado III'; claseImc = 'bg-danger'; }
            
            badgeImc.textContent = diagImc;
            badgeImc.className = 'badge mt-1 ' + claseImc;
        } else {
            resImc.textContent = '--';
            badgeImc.textContent = 'N/A';
            badgeImc.className = 'badge bg-secondary mt-1';
        }

        // 2. Peso Ideal & % Peso Ideal & Corregido
        if (tallaCm > 150) {
            if (sexoPaciente === 'M') {
                pesoIdeal = ((tallaCm - 150) * 2.72 / 2.5) + 47.7;
            } else {
                pesoIdeal = ((tallaCm - 150) * 2.27 / 2.5) + 45.5;
            }
            resPi.textContent = pesoIdeal.toFixed(2) + ' kg';

            if (!isNaN(peso) && peso > 0) {
                pctPi = (peso / pesoIdeal) * 100;
                resPctPi.textContent = pctPi.toFixed(2) + ' %';
                
                let diagPct = '';
                let clasePct = 'bg-secondary';
                if (pctPi < 90) { diagPct = 'Desnutrición'; clasePct = 'bg-danger'; }
                else if (pctPi <= 110) { diagPct = 'Normal'; clasePct = 'bg-success'; }
                else if (pctPi <= 120) { diagPct = 'Sobrepeso'; clasePct = 'bg-warning text-dark'; }
                else { diagPct = 'Obesidad'; clasePct = 'bg-danger'; }
                
                badgePctPi.textContent = diagPct;
                badgePctPi.className = 'badge mt-1 ' + clasePct;

                if (imc > 24.99 || pctPi > 110) {
                    pesoCorregido = ((peso - pesoIdeal) * 0.25) + pesoIdeal;
                    resPic.textContent = pesoCorregido.toFixed(2) + ' kg';
                } else {
                    resPic.textContent = '--';
                }
            } else {
                resPctPi.textContent = '-- %';
                badgePctPi.textContent = 'N/A';
                badgePctPi.className = 'badge bg-secondary mt-1';
                resPic.textContent = '--';
            }
        } else {
            resPi.textContent = '-- kg';
            resPic.textContent = '--';
            resPctPi.textContent = '-- %';
            badgePctPi.textContent = 'N/A';
            badgePctPi.className = 'badge bg-secondary mt-1';
        }

        // 3. Contextura
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
            resContextura.textContent = ctx;
        } else {
            resContextura.textContent = '--';
        }

        // 4. % Peso Usual
        if (!isNaN(peso) && peso > 0 && !isNaN(pesoUsual) && pesoUsual > 0) {
            let pctPu = (peso / pesoUsual) * 100;
            resPctPu.textContent = pctPu.toFixed(2);
        } else {
            resPctPu.textContent = '--';
        }
    }

    if (inputTalla && inputPeso && inputMuneca && inputPesoUsual) {
        inputTalla.addEventListener('input', calcularAntropometria);
        inputPeso.addEventListener('input', calcularAntropometria);
        inputMuneca.addEventListener('input', calcularAntropometria);
        inputPesoUsual.addEventListener('input', calcularAntropometria);
        
        // Ejecutar cálculo inicial si hay datos
        calcularAntropometria();
    }
});
