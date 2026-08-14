document.addEventListener('DOMContentLoaded', () => {
    const nfpeCheckboxes = document.querySelectorAll('.form-check-input[name^="nfpe_"]');
    const panelRecomendaciones = document.getElementById('panel-recomendaciones-nfpe');
    const containerAlertas = document.getElementById('nfpe-alertas-container');

    if (!panelRecomendaciones || !containerAlertas) return;

    function evaluarNFPE() {
        let alertas = [];
        
        let edema = document.getElementById('nfpe_edema')?.checked;
        let ascitis = document.getElementById('nfpe_ascitis')?.checked;
        let temporal = document.getElementById('nfpe_temporal')?.checked;
        let bichat = document.getElementById('nfpe_bichat')?.checked;
        let glositis = document.getElementById('nfpe_glositis')?.checked;
        let coiloniquia = document.getElementById('nfpe_coiloniquia')?.checked;
        let xantelasmas = document.getElementById('nfpe_xantelasmas')?.checked;

        if (edema || ascitis) {
            alertas.push({
                tipo: 'danger',
                texto: '⚠️ Paciente con Edema/Ascitis activo: Sugerencia de restricción de Sodio (< 2000mg/día). Considerar descuento de peso seco.'
            });
        }

        if (temporal || bichat) {
            alertas.push({
                tipo: 'warning',
                texto: '⚠️ Riesgo de Sarcopenia/Consunción: Elevar meta de Proteínas (sugerido > 1.2g - 1.5g/kg peso). Monitorear relación Calorías/Nitrógeno.'
            });
        }

        if (glositis || coiloniquia) {
            alertas.push({
                tipo: 'info',
                texto: '⚠️ Signos carenciales (Glositis/Coiloniquia): Enriquecer con Hierro, Folato y Complejo B.'
            });
        }

        if (xantelasmas) {
            alertas.push({
                tipo: 'info',
                texto: '⚠️ Xantelasmas detectados: Verificar perfil lipídico y proporción de Ácidos Grasos (Saturados vs Poliinsaturados).'
            });
        }

        if (alertas.length > 0) {
            panelRecomendaciones.classList.remove('d-none');
            containerAlertas.innerHTML = alertas.map(a => `<div class="alert alert-${a.tipo} py-2 mb-2 fs-6 shadow-sm border-0"><i class="fa-solid fa-circle-exclamation me-2"></i> ${a.texto}</div>`).join('');
            
            // Guardar en el input oculto para que se envíe con el formulario
            let inputOculto = document.getElementById('nfpe_recomendaciones_json');
            if(inputOculto) inputOculto.value = JSON.stringify(alertas);
        } else {
            panelRecomendaciones.classList.add('d-none');
            containerAlertas.innerHTML = '';
            let inputOculto = document.getElementById('nfpe_recomendaciones_json');
            if(inputOculto) inputOculto.value = '';
        }
    }

    nfpeCheckboxes.forEach(chk => chk.addEventListener('change', evaluarNFPE));
    evaluarNFPE(); // Evaluar al cargar
});
