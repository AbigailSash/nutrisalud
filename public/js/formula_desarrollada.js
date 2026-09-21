/**
 * formula_desarrollada.js
 * Motor Matemático Reactivo en Vanilla JS para Fórmula Desarrollada y Balance Nutricional SARA 2.
 * Integración con Chart.js (Doughnut Macronutrientes y Radar Micronutrientes vs IDR).
 * Semáforos Patológicos, Momentos del Día y Medidas Caseras Dinámicas.
 * NutriSalud SaaS
 */

document.addEventListener('DOMContentLoaded', function () {
    // Estado en memoria de la planilla
    let planillaItems = [];
    let alimentosCatalogo = [];
    let vistaActual = 'clinica'; // 'clinica' o 'completa'

    // Instancias de gráficos Chart.js
    let chartMacro = null;
    let chartRadar = null;

    // Elementos del DOM
    const inputBuscar = document.getElementById('buscarAlimentoInput');
    const selectGrupo = document.getElementById('selectGrupoSara2');
    const selectMomento = document.getElementById('selectMomentoDia');
    const dropdownResultados = document.getElementById('resultadosBusqueda');
    const tablaCuerpo = document.getElementById('tablaFormulaCuerpo');
    const filaTotales = document.getElementById('filaTotales');
    const selectPaciente = document.getElementById('selectPaciente');
    const selectHistorial = document.getElementById('selectHistorial');
    const btnGuardar = document.getElementById('btnGuardarFormula');
    const btnLimpiar = document.getElementById('btnLimpiarPlanilla');
    const inputNombreFormula = document.getElementById('inputNombreFormula');
    const inputKcalObjetivo = document.getElementById('inputKcalObjetivo');
    const txtObservaciones = document.getElementById('txtObservacionesFormula');
    const btnImprimir = document.getElementById('btnImprimirFormula');
    const btnVistaClinica = document.getElementById('btnVistaClinica');
    const btnVistaCompleta = document.getElementById('btnVistaCompleta');
    const inputPacientePeso = document.getElementById('pacientePesoKg');
    let formulaIdActual = document.getElementById('formulaIdActual')?.value || null;

    // Inicializar catálogo desde variable global embebida en PHP si existe
    if (window.ALIMENTOS_BASE && Array.isArray(window.ALIMENTOS_BASE)) {
        alimentosCatalogo = window.ALIMENTOS_BASE;
    }

    // Inicializar gráficos Chart.js
    inicializarGraficos();

    // Cargar datos iniciales si vienen pre-cargados
    if (window.FORMULA_INICIAL && window.FORMULA_INICIAL.detalles) {
        cargarFormulaExistente(window.FORMULA_INICIAL);
    } else {
        renderizarTabla();
        recalcularTodo();
    }

    // Escuchar cambios en Kcal Objetivo
    if (inputKcalObjetivo) {
        inputKcalObjetivo.addEventListener('input', function () {
            recalcularTotalesEIndicadores();
        });
    }

    // =========================================================================
    // 1. INICIALIZACIÓN DE CHART.JS (DOUGHNUT & RADAR)
    // =========================================================================
    function inicializarGraficos() {
        const canvasMacro = document.getElementById('chartMacronutrientes');
        const canvasRadar = document.getElementById('chartRadarMicros');

        if (canvasMacro && typeof Chart !== 'undefined') {
            chartMacro = new Chart(canvasMacro, {
                type: 'doughnut',
                data: {
                    labels: ['Carbohidratos (% kcal)', 'Proteínas (% kcal)', 'Grasas (% kcal)'],
                    datasets: [{
                        data: [50, 20, 30],
                        backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ` ${context.label}: ${context.parsed.toFixed(1)}%`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        if (canvasRadar && typeof Chart !== 'undefined') {
            chartRadar = new Chart(canvasRadar, {
                type: 'radar',
                data: {
                    labels: ['Calcio', 'Hierro', 'Vit. C', 'Vit. A', 'Magnesio', 'Zinc', 'Potasio'],
                    datasets: [{
                        label: '% Cobertura IDR',
                        data: [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(46, 204, 113, 0.2)',
                        borderColor: '#2ecc71',
                        pointBackgroundColor: '#27ae60',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#27ae60',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            angleLines: { color: '#e2e8f0' },
                            grid: { color: '#f1f5f9' },
                            suggestedMin: 0,
                            suggestedMax: 100,
                            ticks: {
                                stepSize: 25,
                                font: { size: 9 },
                                callback: function (val) { return val + '%'; }
                            },
                            pointLabels: {
                                font: { size: 10, weight: 'bold' },
                                color: '#475569'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ` ${context.label}: ${context.parsed.r.toFixed(1)}% IDR`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // =========================================================================
    // 2. SELECTOR DE VISTA (CLÍNICA / COMPLETA SARA 2)
    // =========================================================================
    if (btnVistaClinica && btnVistaCompleta) {
        btnVistaClinica.addEventListener('click', function () {
            vistaActual = 'clinica';
            btnVistaClinica.classList.add('active');
            btnVistaCompleta.classList.remove('active');
            aplicarVisibilidadColumnas();
        });

        btnVistaCompleta.addEventListener('click', function () {
            vistaActual = 'completa';
            btnVistaCompleta.classList.add('active');
            btnVistaClinica.classList.remove('active');
            aplicarVisibilidadColumnas();
        });
    }

    function aplicarVisibilidadColumnas() {
        const completaHeaders = document.querySelectorAll('.col-completa-header');
        const clinicaHeaders = document.querySelectorAll('.col-clinica-header');
        const completaCols = document.querySelectorAll('.col-completa');
        const clinicaCols = document.querySelectorAll('.col-clinica');

        if (vistaActual === 'completa') {
            completaHeaders.forEach(el => el.classList.remove('d-none'));
            completaCols.forEach(el => el.classList.remove('d-none'));
            clinicaHeaders.forEach(el => el.classList.add('d-none'));
            clinicaCols.forEach(el => el.classList.add('d-none'));
        } else {
            completaHeaders.forEach(el => el.classList.add('d-none'));
            completaCols.forEach(el => el.classList.add('d-none'));
            clinicaHeaders.forEach(el => el.classList.remove('d-none'));
            clinicaCols.forEach(el => el.classList.remove('d-none'));
        }
    }

    // =========================================================================
    // 3. AUTOCOMPLETE Y BÚSQUEDA DE ALIMENTOS CON DEBOUNCE Y FILTRO DE GRUPO
    // =========================================================================
    let debounceTimer = null;

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            const grupoId = selectGrupo ? selectGrupo.value : '';

            if (query.length < 1 && !grupoId) {
                dropdownResultados.classList.add('d-none');
                dropdownResultados.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                buscarAlimentosSARA2(query, grupoId);
            }, 200);
        });

        document.addEventListener('click', function (e) {
            if (!inputBuscar.contains(e.target) && !dropdownResultados.contains(e.target) && !(selectGrupo && selectGrupo.contains(e.target))) {
                dropdownResultados.classList.add('d-none');
            }
        });
    }

    if (selectGrupo) {
        selectGrupo.addEventListener('change', function () {
            const grupoId = this.value;
            const query = inputBuscar ? inputBuscar.value.trim() : '';
            if (grupoId || query.length >= 1) {
                buscarAlimentosSARA2(query, grupoId);
            } else {
                dropdownResultados.classList.add('d-none');
            }
        });
    }

    function buscarAlimentosSARA2(query, grupoId) {
        let url = `index.php?action=api_buscar_sara2&q=${encodeURIComponent(query)}`;
        if (grupoId) {
            url += `&grupo_id=${encodeURIComponent(grupoId)}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    renderizarDropdown(data.data.slice(0, 20));
                } else {
                    dropdownResultados.innerHTML = '<div class="p-3 text-muted text-center small"><i class="fa-solid fa-circle-exclamation me-1"></i> No se encontraron alimentos en SARA 2 para esta búsqueda.</div>';
                    dropdownResultados.classList.remove('d-none');
                }
            })
            .catch(() => {
                dropdownResultados.classList.add('d-none');
            });
    }

    function renderizarDropdown(alimentos) {
        dropdownResultados.innerHTML = '';
        const momentoSeleccionado = selectMomento ? selectMomento.value : 'Almuerzo';

        alimentos.forEach(item => {
            const div = document.createElement('div');
            div.className = 'dropdown-item-alimento d-flex justify-content-between align-items-center p-2 border-bottom';
            div.style.cursor = 'pointer';
            
            let badges = '';
            if (parseInt(item.es_avb) === 1) badges += '<span class="badge bg-info text-dark me-1" style="font-size:0.62rem;">AVB</span>';
            if (parseInt(item.es_protector) === 1) badges += '<span class="badge bg-success me-1" style="font-size:0.62rem;">PROT</span>';
            if (parseInt(item.es_leche) === 1) badges += '<span class="badge bg-primary me-1" style="font-size:0.62rem;">LÁCTEO</span>';
            if (parseInt(item.es_hc_complejo) === 1) badges += '<span class="badge bg-warning text-dark me-1" style="font-size:0.62rem;">HC COMP</span>';

            const medidaRef = item.medida_casera_ref ? `<span class="text-secondary small ms-1">(${escapeHtml(item.medida_casera_ref)})</span>` : '';

            div.innerHTML = `
                <div>
                    <strong class="text-dark">${escapeHtml(item.nombre)}</strong>
                    ${medidaRef}
                    <span class="badge bg-light text-secondary ms-2 border" style="font-size:0.7rem;">${escapeHtml(item.grupo_nombre)}</span>
                    <div class="mt-1">${badges}</div>
                </div>
                <div class="text-muted small text-end">
                    <div>
                        <span class="me-2 text-success"><b>${parseFloat(item.energia_kcal).toFixed(1)} kcal</b></span>
                        <span class="me-2">P: <b>${parseFloat(item.proteina_g).toFixed(1)}g</b></span>
                        <span class="me-2">L: <b>${parseFloat(item.lipidos_totales_g).toFixed(1)}g</b></span>
                        <span>HC: <b>${parseFloat(item.hc_disponibles_g).toFixed(1)}g</b></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-1 py-0 px-2 rounded-pill">
                        <i class="fa-solid fa-plus me-1"></i> ${escapeHtml(momentoSeleccionado)}
                    </button>
                </div>
            `;
            div.addEventListener('click', function () {
                agregarAlimentoAPlanilla(item, 100, momentoSeleccionado);
                if (inputBuscar) inputBuscar.value = '';
                dropdownResultados.classList.add('d-none');
                if (inputBuscar) inputBuscar.focus();
            });
            dropdownResultados.appendChild(div);
        });
        dropdownResultados.classList.remove('d-none');
    }

    // =========================================================================
    // 4. GESTIÓN DE FILAS EN LA PLANILLA SARA 2
    // =========================================================================
    function agregarAlimentoAPlanilla(alimento, gramosInicial = 100, momentoDia = 'Almuerzo') {
        const idAlimento = parseInt(alimento.id);

        // Si ya existe el alimento en el MISMO momento, sumamos los gramos
        const indexExistente = planillaItems.findIndex(i => i.id === idAlimento && i.momento_dia === momentoDia);
        if (indexExistente !== -1) {
            planillaItems[indexExistente].gramos += gramosInicial;
            actualizarFilaDOM(indexExistente);
            recalcularTodo();
            mostrarToast('Se sumaron ' + gramosInicial + 'g a ' + alimento.nombre + ' en ' + momentoDia, 'info');
            return;
        }

        const item = {
            id: idAlimento,
            nombre: alimento.nombre,
            grupo_id: parseInt(alimento.grupo_id) || 1,
            grupo_nombre: alimento.grupo_nombre || '',
            medida_casera_ref: alimento.medida_casera_ref || '',
            momento_dia: momentoDia || 'Almuerzo',
            gramos: parseFloat(gramosInicial) || 100,

            // Macronutrientes y Generales (base 100g)
            energia_kcal: parseFloat(alimento.energia_kcal) || 0,
            agua_g: parseFloat(alimento.agua_g) || 0,
            proteina_g: parseFloat(alimento.proteina_g) || 0,
            lipidos_totales_g: parseFloat(alimento.lipidos_totales_g) || 0,
            colesterol_mg: parseFloat(alimento.colesterol_mg) || 0,

            // Perfil Lipídico Detallado
            ag_sat_g: parseFloat(alimento.ag_sat_g) || 0,
            ag_mono_g: parseFloat(alimento.ag_mono_g) || 0,
            ag_poli_g: parseFloat(alimento.ag_poli_g) || 0,
            ag_trans_g: parseFloat(alimento.ag_trans_g) || 0,
            ac_linoleico_g: parseFloat(alimento.ac_linoleico_g) || 0,
            ac_linolenico_g: parseFloat(alimento.ac_linolenico_g) || 0,
            ac_araquidonico_g: parseFloat(alimento.ac_araquidonico_g) || 0,
            ac_epa_g: parseFloat(alimento.ac_epa_g) || 0,
            ac_dha_g: parseFloat(alimento.ac_dha_g) || 0,

            // Carbohidratos y Alcohol
            hc_disponibles_g: parseFloat(alimento.hc_disponibles_g) || 0,
            hc_totales_g: parseFloat(alimento.hc_totales_g) || 0,
            azucar_total_g: parseFloat(alimento.azucar_total_g) || 0,
            azucar_agregado_g: parseFloat(alimento.azucar_agregado_g) || 0,
            fibra_g: parseFloat(alimento.fibra_g) || 0,
            alcohol_g: parseFloat(alimento.alcohol_g) || 0,

            // Minerales y Cenizas
            cenizas_g: parseFloat(alimento.cenizas_g) || 0,
            sodio_mg: parseFloat(alimento.sodio_mg) || 0,
            potasio_mg: parseFloat(alimento.potasio_mg) || 0,
            calcio_mg: parseFloat(alimento.calcio_mg) || 0,
            cobre_mg: parseFloat(alimento.cobre_mg) || 0,
            fosforo_mg: parseFloat(alimento.fosforo_mg) || 0,
            hierro_mg: parseFloat(alimento.hierro_mg) || 0,
            magnesio_mg: parseFloat(alimento.magnesio_mg) || 0,
            zinc_mg: parseFloat(alimento.zinc_mg) || 0,

            // Vitaminas
            niacina_mg: parseFloat(alimento.niacina_mg) || 0,
            folato_efd_ug: parseFloat(alimento.folato_efd_ug) || 0,
            ac_folico_sint_ug: parseFloat(alimento.ac_folico_sint_ug) || 0,
            vit_a_rae_ug: parseFloat(alimento.vit_a_rae_ug) || 0,
            retinol_ug: parseFloat(alimento.retinol_ug) || 0,
            tiamina_b1_mg: parseFloat(alimento.tiamina_b1_mg) || 0,
            riboflavina_b2_mg: parseFloat(alimento.riboflavina_b2_mg) || 0,
            vit_b12_ug: parseFloat(alimento.vit_b12_ug) || 0,
            vit_c_mg: parseFloat(alimento.vit_c_mg) || 0,
            vit_d_ug: parseFloat(alimento.vit_d_ug) || 0,

            // Flags Clínicos
            es_avb: parseInt(alimento.es_avb) === 1,
            es_protector: parseInt(alimento.es_protector) === 1,
            es_leche: parseInt(alimento.es_leche) === 1,
            es_hc_complejo: parseInt(alimento.es_hc_complejo) === 1
        };

        planillaItems.push(item);
        renderizarTabla();
        recalcularTodo();
        aplicarVisibilidadColumnas();
    }

    function renderizarTabla() {
        if (!tablaCuerpo) return;
        tablaCuerpo.innerHTML = '';

        if (planillaItems.length === 0) {
            tablaCuerpo.innerHTML = `
                <tr id="filaVacia">
                    <td colspan="45" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-scale-balanced fa-3x mb-3 d-block text-secondary opacity-50"></i>
                        <h6 class="fw-semibold">Planilla SARA 2 vacía</h6>
                        <p class="small text-muted mb-0">Selecciona el momento del día y busca alimentos del catálogo oficial SARA 2 para agregarlos a la planilla.</p>
                    </td>
                </tr>
            `;
            return;
        }

        planillaItems.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.id = `fila_item_${index}`;
            tr.innerHTML = generarHTMLFilaSARA2(item, index);
            tablaCuerpo.appendChild(tr);

            // Escuchar cambios de gramos en vivo
            const inputGramos = tr.querySelector('.input-gramos-fila');
            if (inputGramos) {
                inputGramos.addEventListener('input', function () {
                    const nuevoValor = parseFloat(this.value);
                    item.gramos = !isNaN(nuevoValor) && nuevoValor >= 0 ? nuevoValor : 0;
                    actualizarCalculosFila(tr, item);
                    recalcularTotalesEIndicadores();
                });
            }

            // Escuchar cambios en selector de momento del día de la fila
            const selectMomentoFila = tr.querySelector('.select-momento-fila');
            if (selectMomentoFila) {
                selectMomentoFila.addEventListener('change', function () {
                    item.momento_dia = this.value;
                });
            }

            // Botón eliminar fila
            const btnEliminar = tr.querySelector('.btn-eliminar-fila');
            if (btnEliminar) {
                btnEliminar.addEventListener('click', function () {
                    planillaItems.splice(index, 1);
                    renderizarTabla();
                    recalcularTodo();
                    aplicarVisibilidadColumnas();
                });
            }
        });

        aplicarVisibilidadColumnas();
    }

    function calcularMedidaCaseraEstimada(item) {
        if (!item.medida_casera_ref) return '';
        // Intentar parsear el peso de referencia entre paréntesis, ej: "1 taza (200g)" o "1 unidad (120g)"
        const match = item.medida_casera_ref.match(/\((\d+(?:\.\d+)?)\s*(?:g|ml)\)/i);
        if (match && parseFloat(match[1]) > 0) {
            const refGramos = parseFloat(match[1]);
            const ratio = item.gramos / refGramos;
            // Limpiar el texto de la medida quitando los gramos
            const nombreMedida = item.medida_casera_ref.replace(/\s*\(\d+(?:\.\d+)?\s*(?:g|ml)\)/i, '').trim();
            if (ratio === 1) {
                return nombreMedida;
            } else if (ratio === 0.5) {
                return `1/2 ${nombreMedida}`;
            } else if (ratio === 0.25) {
                return `1/4 ${nombreMedida}`;
            } else if (ratio === 0.75) {
                return `3/4 ${nombreMedida}`;
            } else {
                return `${ratio.toFixed(1)} x ${nombreMedida}`;
            }
        }
        return item.medida_casera_ref;
    }

    function calcularAportesFila(item) {
        const factor = item.gramos / 100;
        const hcDisp = item.hc_disponibles_g * factor;
        const prot = item.proteina_g * factor;
        const lip = item.lipidos_totales_g * factor;
        const alc = item.alcohol_g * factor;

        // Fórmula Atwater SARA 2: (HC_disp * 4) + (Prot * 4) + (Lip * 9) + (Alcohol * 7)
        const kcal = (hcDisp * 4) + (prot * 4) + (lip * 9) + (alc * 7);

        return {
            factor,
            kcal,
            agua: item.agua_g * factor,
            prot,
            lip,
            colest: item.colesterol_mg * factor,
            agSat: item.ag_sat_g * factor,
            agMono: item.ag_mono_g * factor,
            agPoli: item.ag_poli_g * factor,
            agTrans: item.ag_trans_g * factor,
            linoleico: item.ac_linoleico_g * factor,
            linolenico: item.ac_linolenico_g * factor,
            araquidonico: item.ac_araquidonico_g * factor,
            epaDha: (item.ac_epa_g + item.ac_dha_g) * factor,
            hcDisp,
            hcTot: item.hc_totales_g * factor,
            azucarTot: item.azucar_total_g * factor,
            azucarAgr: item.azucar_agregado_g * factor,
            fibra: item.fibra_g * factor,
            alc,
            cenizas: item.cenizas_g * factor,
            sodio: item.sodio_mg * factor,
            potasio: item.potasio_mg * factor,
            calcio: item.calcio_mg * factor,
            cobre: item.cobre_mg * factor,
            fosforo: item.fosforo_mg * factor,
            hierro: item.hierro_mg * factor,
            magnesio: item.magnesio_mg * factor,
            zinc: item.zinc_mg * factor,
            niacina: item.niacina_mg * factor,
            folatoEfd: item.folato_efd_ug * factor,
            acFolico: item.ac_folico_sint_ug * factor,
            vitARae: item.vit_a_rae_ug * factor,
            retinol: item.retinol_ug * factor,
            tiaminaB1: item.tiamina_b1_mg * factor,
            riboflavinaB2: item.riboflavina_b2_mg * factor,
            vitB12: item.vit_b12_ug * factor,
            vitC: item.vit_c_mg * factor,
            vitD: item.vit_d_ug * factor
        };
    }

    function generarHTMLFilaSARA2(item, index) {
        const c = calcularAportesFila(item);
        const medidaEstimada = calcularMedidaCaseraEstimada(item);

        const momentos = ['Desayuno', 'Media Mañana', 'Almuerzo', 'Merienda', 'Cena', 'Colación'];
        let optionsMomento = '';
        momentos.forEach(m => {
            optionsMomento += `<option value="${m}" ${item.momento_dia === m ? 'selected' : ''}>${m}</option>`;
        });

        return `
            <td class="text-nowrap fw-semibold text-dark sticky-col-1">
                <div>${escapeHtml(item.nombre)}</div>
                ${medidaEstimada ? `<small class="text-muted d-block font-monospace" style="font-size:0.7rem;"><i class="fa-solid fa-utensils me-1 text-secondary"></i>${escapeHtml(medidaEstimada)}</small>` : ''}
                <div class="mt-1">
                    ${item.es_avb ? '<span class="badge bg-info text-dark me-1 sara-badge" title="Proteína de Alto Valor Biológico">AVB</span>' : ''}
                    ${item.es_protector ? '<span class="badge bg-success me-1 sara-badge" title="Alimento Protector">PROT</span>' : ''}
                    ${item.es_leche ? '<span class="badge bg-primary me-1 sara-badge" title="Cobertura Láctea">LÁCTEO</span>' : ''}
                    ${item.es_hc_complejo ? '<span class="badge bg-warning text-dark sara-badge" title="HC Complejo">HC COMP</span>' : ''}
                </div>
            </td>
            <td>
                <select class="form-select form-select-sm select-momento-fila shadow-sm" style="font-size:0.75rem; padding: 2px 5px;">
                    ${optionsMomento}
                </select>
            </td>
            <td style="width: 90px;">
                <div class="input-group input-group-sm">
                    <input type="number" step="1" min="0" class="form-control form-control-sm text-end fw-bold input-gramos-fila" value="${item.gramos}">
                    <span class="input-group-text bg-light px-1 small">g</span>
                </div>
            </td>
            <td class="text-end fw-bold text-success col-kcal">${c.kcal.toFixed(1)}</td>

            <!-- Macronutrientes Generales -->
            <td class="text-end col-agua">${c.agua.toFixed(1)}</td>
            <td class="text-end fw-semibold text-danger col-prot">${c.prot.toFixed(2)}</td>
            <td class="text-end fw-semibold text-warning col-lip">${c.lip.toFixed(2)}</td>
            <td class="text-end fw-semibold text-primary col-hcdisp">${c.hcDisp.toFixed(2)}</td>

            <!-- Perfil Lipídico Detallado (Completo SARA 2) -->
            <td class="text-end col-completa d-none col-colest">${c.colest.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-agsat">${c.agSat.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-agmono">${c.agMono.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-agpoli">${c.agPoli.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-agtrans">${c.agTrans.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-linoleico">${c.linoleico.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-linolenico">${c.linolenico.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-araquidonico">${c.araquidonico.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-epadha">${c.epaDha.toFixed(2)}</td>

            <!-- Carbohidratos, Azúcares y Alcohol Completo -->
            <td class="text-end col-completa d-none col-hctot">${c.hcTot.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-azucartot">${c.azucarTot.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-azucaragr">${c.azucarAgr.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-fibra">${c.fibra.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-alc">${c.alc.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-cenizas">${c.cenizas.toFixed(2)}</td>

            <!-- Minerales Completo -->
            <td class="text-end col-completa d-none col-sodio">${c.sodio.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-potasio">${c.potasio.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-calcio">${c.calcio.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-fosforo">${c.fosforo.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-hierro">${c.hierro.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-magnesio">${c.magnesio.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-zinc">${c.zinc.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-cobre">${c.cobre.toFixed(2)}</td>

            <!-- Vitaminas Completo -->
            <td class="text-end col-completa d-none col-niacina">${c.niacina.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-folato">${c.folatoEfd.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-acfolico">${c.acFolico.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-vitarae">${c.vitARae.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-retinol">${c.retinol.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-tiamina">${c.tiaminaB1.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-riboflavina">${c.riboflavinaB2.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-vitb12">${c.vitB12.toFixed(2)}</td>
            <td class="text-end col-completa d-none col-vitc">${c.vitC.toFixed(1)}</td>
            <td class="text-end col-completa d-none col-vitd">${c.vitD.toFixed(2)}</td>

            <!-- Columnas Vista Clínica -->
            <td class="text-end col-clinica col-fibra-c">${c.fibra.toFixed(2)}</td>
            <td class="text-end col-clinica col-hierro-c">${c.hierro.toFixed(2)}</td>
            <td class="text-end col-clinica col-calcio-c">${c.calcio.toFixed(1)}</td>
            <td class="text-end col-clinica col-sodio-c">${c.sodio.toFixed(1)}</td>
            <td class="text-end col-clinica col-potasio-c">${c.potasio.toFixed(1)}</td>
            <td class="text-end col-clinica col-agsat-c">${c.agSat.toFixed(2)}</td>
            <td class="text-end col-clinica col-vitarae-c">${c.vitARae.toFixed(1)}</td>
            <td class="text-end col-clinica col-vitc-c">${c.vitC.toFixed(1)}</td>

            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill btn-eliminar-fila" title="Eliminar fila">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;
    }

    function actualizarCalculosFila(tr, item) {
        const c = calcularAportesFila(item);

        tr.querySelector('.col-kcal').textContent = c.kcal.toFixed(1);
        tr.querySelector('.col-agua').textContent = c.agua.toFixed(1);
        tr.querySelector('.col-prot').textContent = c.prot.toFixed(2);
        tr.querySelector('.col-lip').textContent = c.lip.toFixed(2);
        tr.querySelector('.col-hcdisp').textContent = c.hcDisp.toFixed(2);

        // Completo
        const elColest = tr.querySelector('.col-colest'); if (elColest) elColest.textContent = c.colest.toFixed(1);
        const elAgSat = tr.querySelector('.col-agsat'); if (elAgSat) elAgSat.textContent = c.agSat.toFixed(2);
        const elAgMono = tr.querySelector('.col-agmono'); if (elAgMono) elAgMono.textContent = c.agMono.toFixed(2);
        const elAgPoli = tr.querySelector('.col-agpoli'); if (elAgPoli) elAgPoli.textContent = c.agPoli.toFixed(2);
        const elAgTrans = tr.querySelector('.col-agtrans'); if (elAgTrans) elAgTrans.textContent = c.agTrans.toFixed(2);
        const elLinoleico = tr.querySelector('.col-linoleico'); if (elLinoleico) elLinoleico.textContent = c.linoleico.toFixed(2);
        const elLinolenico = tr.querySelector('.col-linolenico'); if (elLinolenico) elLinolenico.textContent = c.linolenico.toFixed(2);
        const elAraquidonico = tr.querySelector('.col-araquidonico'); if (elAraquidonico) elAraquidonico.textContent = c.araquidonico.toFixed(2);
        const elEpaDha = tr.querySelector('.col-epadha'); if (elEpaDha) elEpaDha.textContent = c.epaDha.toFixed(2);

        const elHcTot = tr.querySelector('.col-hctot'); if (elHcTot) elHcTot.textContent = c.hcTot.toFixed(2);
        const elAzucarTot = tr.querySelector('.col-azucartot'); if (elAzucarTot) elAzucarTot.textContent = c.azucarTot.toFixed(2);
        const elAzucarAgr = tr.querySelector('.col-azucaragr'); if (elAzucarAgr) elAzucarAgr.textContent = c.azucarAgr.toFixed(2);
        const elFibra = tr.querySelector('.col-fibra'); if (elFibra) elFibra.textContent = c.fibra.toFixed(2);
        const elAlc = tr.querySelector('.col-alc'); if (elAlc) elAlc.textContent = c.alc.toFixed(2);
        const elCenizas = tr.querySelector('.col-cenizas'); if (elCenizas) elCenizas.textContent = c.cenizas.toFixed(2);

        const elSodio = tr.querySelector('.col-sodio'); if (elSodio) elSodio.textContent = c.sodio.toFixed(1);
        const elPotasio = tr.querySelector('.col-potasio'); if (elPotasio) elPotasio.textContent = c.potasio.toFixed(1);
        const elCalcio = tr.querySelector('.col-calcio'); if (elCalcio) elCalcio.textContent = c.calcio.toFixed(1);
        const elFosforo = tr.querySelector('.col-fosforo'); if (elFosforo) elFosforo.textContent = c.fosforo.toFixed(1);
        const elHierro = tr.querySelector('.col-hierro'); if (elHierro) elHierro.textContent = c.hierro.toFixed(2);
        const elMagnesio = tr.querySelector('.col-magnesio'); if (elMagnesio) elMagnesio.textContent = c.magnesio.toFixed(1);
        const elZinc = tr.querySelector('.col-zinc'); if (elZinc) elZinc.textContent = c.zinc.toFixed(2);
        const elCobre = tr.querySelector('.col-cobre'); if (elCobre) elCobre.textContent = c.cobre.toFixed(2);

        const elNiacina = tr.querySelector('.col-niacina'); if (elNiacina) elNiacina.textContent = c.niacina.toFixed(2);
        const elFolato = tr.querySelector('.col-folato'); if (elFolato) elFolato.textContent = c.folatoEfd.toFixed(1);
        const elAcFolico = tr.querySelector('.col-acfolico'); if (elAcFolico) elAcFolico.textContent = c.acFolico.toFixed(1);
        const elVitARae = tr.querySelector('.col-vitarae'); if (elVitARae) elVitARae.textContent = c.vitARae.toFixed(1);
        const elRetinol = tr.querySelector('.col-retinol'); if (elRetinol) elRetinol.textContent = c.retinol.toFixed(1);
        const elTiamina = tr.querySelector('.col-tiamina'); if (elTiamina) elTiamina.textContent = c.tiaminaB1.toFixed(2);
        const elRiboflavina = tr.querySelector('.col-riboflavina'); if (elRiboflavina) elRiboflavina.textContent = c.riboflavinaB2.toFixed(2);
        const elVitB12 = tr.querySelector('.col-vitb12'); if (elVitB12) elVitB12.textContent = c.vitB12.toFixed(2);
        const elVitC = tr.querySelector('.col-vitc'); if (elVitC) elVitC.textContent = c.vitC.toFixed(1);
        const elVitD = tr.querySelector('.col-vitd'); if (elVitD) elVitD.textContent = c.vitD.toFixed(2);

        // Clínica
        const elFibraC = tr.querySelector('.col-fibra-c'); if (elFibraC) elFibraC.textContent = c.fibra.toFixed(2);
        const elHierroC = tr.querySelector('.col-hierro-c'); if (elHierroC) elHierroC.textContent = c.hierro.toFixed(2);
        const elCalcioC = tr.querySelector('.col-calcio-c'); if (elCalcioC) elCalcioC.textContent = c.calcio.toFixed(1);
        const elSodioC = tr.querySelector('.col-sodio-c'); if (elSodioC) elSodioC.textContent = c.sodio.toFixed(1);
        const elPotasioC = tr.querySelector('.col-potasio-c'); if (elPotasioC) elPotasioC.textContent = c.potasio.toFixed(1);
        const elAgSatC = tr.querySelector('.col-agsat-c'); if (elAgSatC) elAgSatC.textContent = c.agSat.toFixed(2);
        const elVitARaeC = tr.querySelector('.col-vitarae-c'); if (elVitARaeC) elVitARaeC.textContent = c.vitARae.toFixed(1);
        const elVitCC = tr.querySelector('.col-vitc-c'); if (elVitCC) elVitCC.textContent = c.vitC.toFixed(1);
    }

    function actualizarFilaDOM(index) {
        const tr = document.getElementById(`fila_item_${index}`);
        if (tr) {
            const input = tr.querySelector('.input-gramos-fila');
            if (input) input.value = planillaItems[index].gramos;
            actualizarCalculosFila(tr, planillaItems[index]);
        }
    }

    // =========================================================================
    // 5. MOTOR MATEMÁTICO: TOTALES, ANALÍTICA VISUAL & 7 INDICADORES SARA 2
    // =========================================================================
    function recalcularTodo() {
        recalcularTotalesEIndicadores();
    }

    function recalcularTotalesEIndicadores() {
        let totalGramos = 0;
        let totalKcal = 0;
        let totalAgua = 0;
        let totalProt = 0;
        let totalLip = 0;
        let totalColest = 0;
        let totalAgSat = 0;
        let totalAgMono = 0;
        let totalAgPoli = 0;
        let totalAgTrans = 0;
        let totalLinoleico = 0;
        let totalLinolenico = 0;
        let totalAraquidonico = 0;
        let totalEpaDha = 0;
        let totalHcDisp = 0;
        let totalHcTot = 0;
        let totalAzucarTot = 0;
        let totalAzucarAgr = 0;
        let totalFibra = 0;
        let totalAlc = 0;
        let totalCenizas = 0;
        let totalSodio = 0;
        let totalPotasio = 0;
        let totalCalcio = 0;
        let totalCobre = 0;
        let totalFosforo = 0;
        let totalHierro = 0;
        let totalMagnesio = 0;
        let totalZinc = 0;
        let totalNiacina = 0;
        let totalFolatoEfd = 0;
        let totalAcFolico = 0;
        let totalVitARae = 0;
        let totalRetinol = 0;
        let totalTiaminaB1 = 0;
        let totalRiboflavinaB2 = 0;
        let totalVitB12 = 0;
        let totalVitC = 0;
        let totalVitD = 0;

        // Acumuladores de los 7 Indicadores Diagnósticos
        let sumHcComplejos = 0;
        let sumKcalProtectores = 0;
        let sumProtAVB = 0;
        let sumKcalLeche = 0;

        planillaItems.forEach(item => {
            const c = calcularAportesFila(item);

            totalGramos += item.gramos;
            totalKcal += c.kcal;
            totalAgua += c.agua;
            totalProt += c.prot;
            totalLip += c.lip;
            totalColest += c.colest;
            totalAgSat += c.agSat;
            totalAgMono += c.agMono;
            totalAgPoli += c.agPoli;
            totalAgTrans += c.agTrans;
            totalLinoleico += c.linoleico;
            totalLinolenico += c.linolenico;
            totalAraquidonico += c.araquidonico;
            totalEpaDha += c.epaDha;
            totalHcDisp += c.hcDisp;
            totalHcTot += c.hcTot;
            totalAzucarTot += c.azucarTot;
            totalAzucarAgr += c.azucarAgr;
            totalFibra += c.fibra;
            totalAlc += c.alc;
            totalCenizas += c.cenizas;
            totalSodio += c.sodio;
            totalPotasio += c.potasio;
            totalCalcio += c.calcio;
            totalCobre += c.cobre;
            totalFosforo += c.fosforo;
            totalHierro += c.hierro;
            totalMagnesio += c.magnesio;
            totalZinc += c.zinc;
            totalNiacina += c.niacina;
            totalFolatoEfd += c.folatoEfd;
            totalAcFolico += c.acFolico;
            totalVitARae += c.vitARae;
            totalRetinol += c.retinol;
            totalTiaminaB1 += c.tiaminaB1;
            totalRiboflavinaB2 += c.riboflavinaB2;
            totalVitB12 += c.vitB12;
            totalVitC += c.vitC;
            totalVitD += c.vitD;

            if (item.es_hc_complejo) sumHcComplejos += c.hcDisp;
            if (item.es_protector) sumKcalProtectores += c.kcal;
            if (item.es_avb) sumProtAVB += c.prot;
            if (item.es_leche) sumKcalLeche += c.kcal;
        });

        // Actualizar fila de totales
        if (filaTotales) {
            filaTotales.innerHTML = `
                <tr>
                    <td class="sticky-col-1 text-dark fw-bold">TOTALES GENERALES</td>
                    <td class="text-center">-</td>
                    <td class="text-end fw-bold">${totalGramos.toFixed(0)} g</td>
                    <td class="text-end text-success fs-6 fw-bold">${totalKcal.toFixed(1)}</td>

                    <!-- Macros -->
                    <td class="text-end">${totalAgua.toFixed(1)}</td>
                    <td class="text-end text-danger fw-bold">${totalProt.toFixed(2)}</td>
                    <td class="text-end text-warning fw-bold">${totalLip.toFixed(2)}</td>
                    <td class="text-end text-primary fw-bold">${totalHcDisp.toFixed(2)}</td>

                    <!-- Completo Lipídico -->
                    <td class="text-end col-completa d-none">${totalColest.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalAgSat.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAgMono.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAgPoli.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAgTrans.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalLinoleico.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalLinolenico.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAraquidonico.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalEpaDha.toFixed(2)}</td>

                    <!-- Completo Carbs -->
                    <td class="text-end col-completa d-none">${totalHcTot.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAzucarTot.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAzucarAgr.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalFibra.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalAlc.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalCenizas.toFixed(2)}</td>

                    <!-- Completo Minerales -->
                    <td class="text-end col-completa d-none">${totalSodio.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalPotasio.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalCalcio.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalFosforo.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalHierro.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalMagnesio.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalZinc.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalCobre.toFixed(2)}</td>

                    <!-- Completo Vitaminas -->
                    <td class="text-end col-completa d-none">${totalNiacina.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalFolatoEfd.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalAcFolico.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalVitARae.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalRetinol.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalTiaminaB1.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalRiboflavinaB2.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalVitB12.toFixed(2)}</td>
                    <td class="text-end col-completa d-none">${totalVitC.toFixed(1)}</td>
                    <td class="text-end col-completa d-none">${totalVitD.toFixed(2)}</td>

                    <!-- Clínica -->
                    <td class="text-end col-clinica">${totalFibra.toFixed(2)}</td>
                    <td class="text-end col-clinica">${totalHierro.toFixed(2)}</td>
                    <td class="text-end col-clinica">${totalCalcio.toFixed(1)}</td>
                    <td class="text-end col-clinica">${totalSodio.toFixed(1)}</td>
                    <td class="text-end col-clinica">${totalPotasio.toFixed(1)}</td>
                    <td class="text-end col-clinica">${totalAgSat.toFixed(2)}</td>
                    <td class="text-end col-clinica">${totalVitARae.toFixed(1)}</td>
                    <td class="text-end col-clinica">${totalVitC.toFixed(1)}</td>

                    <td class="text-center">-</td>
                </tr>
            `;
            aplicarVisibilidadColumnas();
        }

        // -------------------------------------------------------------
        // A. RESUMEN DE META CALÓRICA & PROTEÍNAS POR KG
        // -------------------------------------------------------------
        const kcalObj = parseFloat(inputKcalObjetivo?.value) || 2000;
        setTxt('sidebarKcalObjetivo', kcalObj.toFixed(0));
        setTxt('sidebarKcalCalculadas', totalKcal.toFixed(1));

        const deltaKcal = totalKcal - kcalObj;
        const elDelta = document.getElementById('sidebarDeltaKcal');
        if (elDelta) {
            if (Math.abs(deltaKcal) < 1) {
                elDelta.textContent = 'Exacto (0 kcal)';
                elDelta.className = 'small fw-bold text-success';
            } else if (deltaKcal > 0) {
                elDelta.textContent = `+${deltaKcal.toFixed(1)} kcal (Superávit)`;
                elDelta.className = 'small fw-bold text-warning';
            } else {
                elDelta.textContent = `${deltaKcal.toFixed(1)} kcal (Déficit)`;
                elDelta.className = 'small fw-bold text-info';
            }
        }

        const porcMeta = kcalObj > 0 ? ((totalKcal / kcalObj) * 100) : 0;
        setTxt('badgePorcMeta', porcMeta.toFixed(1) + '%');
        const progressBar = document.getElementById('progressBarKcal');
        if (progressBar) {
            progressBar.style.width = Math.min(porcMeta, 100) + '%';
            if (porcMeta > 110) {
                progressBar.className = 'progress-bar bg-warning';
            } else if (porcMeta >= 90) {
                progressBar.className = 'progress-bar bg-success';
            } else {
                progressBar.className = 'progress-bar bg-primary';
            }
        }

        // Proteína g/kg peso
        const pesoKg = parseFloat(inputPacientePeso?.value) || 0;
        const protKgVal = pesoKg > 0 ? (totalProt / pesoKg) : 0;
        setTxt('sidebarProtKgVal', pesoKg > 0 ? protKgVal.toFixed(2) : '--');
        const badgeProtKg = document.getElementById('badgeProtKg');
        if (badgeProtKg) {
            if (pesoKg === 0) {
                badgeProtKg.className = 'badge bg-secondary';
                badgeProtKg.textContent = 'Sin peso paciente';
            } else if (protKgVal < 0.8) {
                badgeProtKg.className = 'badge bg-danger';
                badgeProtKg.textContent = 'Baja (<0.8 g/kg)';
            } else if (protKgVal <= 1.2) {
                badgeProtKg.className = 'badge bg-success';
                badgeProtKg.textContent = 'Normoproteica (0.8-1.2)';
            } else if (protKgVal <= 1.8) {
                badgeProtKg.className = 'badge bg-info text-dark';
                badgeProtKg.textContent = 'Moderada/Deporte (1.2-1.8)';
            } else {
                badgeProtKg.className = 'badge bg-warning text-dark';
                badgeProtKg.textContent = 'Hiperproteica (>1.8)';
            }
        }

        // -------------------------------------------------------------
        // B. ACTUALIZACIÓN DE GRÁFICOS CHART.JS
        // -------------------------------------------------------------
        // 1. Doughnut: % Kcal de Macronutrientes
        const kcalHC = totalHcDisp * 4;
        const kcalProt = totalProt * 4;
        const kcalGrasas = totalLip * 9;
        const kcalMacrosSum = kcalHC + kcalProt + kcalGrasas;

        let pctHC = kcalMacrosSum > 0 ? ((kcalHC / kcalMacrosSum) * 100) : 0;
        let pctProt = kcalMacrosSum > 0 ? ((kcalProt / kcalMacrosSum) * 100) : 0;
        let pctGrasas = kcalMacrosSum > 0 ? ((kcalGrasas / kcalMacrosSum) * 100) : 0;

        setTxt('lblPctHC', pctHC.toFixed(1) + '%');
        setTxt('lblPctProt', pctProt.toFixed(1) + '%');
        setTxt('lblPctGrasas', pctGrasas.toFixed(1) + '%');

        if (chartMacro) {
            chartMacro.data.datasets[0].data = [
                parseFloat(pctHC.toFixed(1)),
                parseFloat(pctProt.toFixed(1)),
                parseFloat(pctGrasas.toFixed(1))
            ];
            chartMacro.update();
        }

        // 2. Radar: Micronutrientes vs IDR estándar
        // Calcio: 1000mg, Hierro: 14mg, Vit C: 75mg, Vit A: 800mcg, Magnesio: 350mg, Zinc: 10mg, Potasio: 3500mg
        const idrCa = 1000, idrFe = 14, idrVitC = 75, idrVitA = 800, idrMg = 350, idrZn = 10, idrK = 3500;
        const radarValues = [
            Math.min(150, (totalCalcio / idrCa) * 100),
            Math.min(150, (totalHierro / idrFe) * 100),
            Math.min(150, (totalVitC / idrVitC) * 100),
            Math.min(150, (totalVitARae / idrVitA) * 100),
            Math.min(150, (totalMagnesio / idrMg) * 100),
            Math.min(150, (totalZinc / idrZn) * 100),
            Math.min(150, (totalPotasio / idrK) * 100)
        ];

        if (chartRadar) {
            chartRadar.data.datasets[0].data = radarValues.map(v => parseFloat(v.toFixed(1)));
            chartRadar.update();
        }

        // -------------------------------------------------------------
        // C. SEMÁFOROS DE ALERTAS PATOLÓGICAS
        // -------------------------------------------------------------
        // Sodio (Hipertensión): Límite 2000 mg
        setTxt('txtAlertaSodio', `${totalSodio.toFixed(0)} mg (Límite 2000 mg)`);
        const badgeAlertaSodio = document.getElementById('badgeAlertaSodio');
        const boxAlertaSodio = document.getElementById('boxAlertaSodio');
        if (badgeAlertaSodio && boxAlertaSodio) {
            if (totalSodio > 2000) {
                badgeAlertaSodio.className = 'badge bg-danger';
                badgeAlertaSodio.textContent = 'ALTO (>2000mg)';
                boxAlertaSodio.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border border-danger bg-danger bg-opacity-10';
            } else {
                badgeAlertaSodio.className = 'badge bg-success';
                badgeAlertaSodio.textContent = 'Adecuado (≤2000mg)';
                boxAlertaSodio.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border';
            }
        }

        // Fibra Dietética: Meta >= 25 g
        setTxt('txtAlertaFibra', `${totalFibra.toFixed(1)} g (Meta ≥ 25 g)`);
        const badgeAlertaFibra = document.getElementById('badgeAlertaFibra');
        const boxAlertaFibra = document.getElementById('boxAlertaFibra');
        if (badgeAlertaFibra && boxAlertaFibra) {
            if (totalFibra >= 25) {
                badgeAlertaFibra.className = 'badge bg-success';
                badgeAlertaFibra.textContent = 'Adecuada (≥25g)';
                boxAlertaFibra.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border border-success bg-success bg-opacity-10';
            } else if (totalFibra >= 15) {
                badgeAlertaFibra.className = 'badge bg-warning text-dark';
                badgeAlertaFibra.textContent = 'Moderada (15-25g)';
                boxAlertaFibra.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border';
            } else {
                badgeAlertaFibra.className = 'badge bg-secondary';
                badgeAlertaFibra.textContent = 'Baja (<15g)';
                boxAlertaFibra.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border';
            }
        }

        // Azúcares Agregados: OMS <= 10% VCT
        const kcalAzucarAgr = totalAzucarAgr * 4;
        const porcAzucarAgr = totalKcal > 0 ? ((kcalAzucarAgr / totalKcal) * 100) : 0;
        setTxt('txtAlertaAzucar', `${porcAzucarAgr.toFixed(1)}% VCT (${(totalAzucarAgr).toFixed(1)}g)`);
        const badgeAlertaAzucar = document.getElementById('badgeAlertaAzucar');
        const boxAlertaAzucar = document.getElementById('boxAlertaAzucar');
        if (badgeAlertaAzucar && boxAlertaAzucar) {
            if (porcAzucarAgr > 10) {
                badgeAlertaAzucar.className = 'badge bg-danger';
                badgeAlertaAzucar.textContent = 'ELEVADO (>10%)';
                boxAlertaAzucar.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 border border-danger bg-danger bg-opacity-10';
            } else {
                badgeAlertaAzucar.className = 'badge bg-success';
                badgeAlertaAzucar.textContent = 'Normal (≤10%)';
                boxAlertaAzucar.className = 'd-flex justify-content-between align-items-center p-2 rounded-2 border';
            }
        }

        // -------------------------------------------------------------
        // D. LOS 7 INDICADORES CLÍNICOS DIAGNÓSTICOS SARA 2
        // -------------------------------------------------------------
        // 1. Densidad Calórica: Total Kcal / Total Gramos
        const densCal = totalGramos > 0 ? (totalKcal / totalGramos) : 0;
        setTxt('ind_dens_cal', totalGramos > 0 ? densCal.toFixed(2) : '--');
        const badgeDensCal = document.getElementById('badge_dens_cal');
        if (badgeDensCal) {
            if (totalGramos === 0) {
                badgeDensCal.className = 'badge bg-secondary sara-badge';
                badgeDensCal.textContent = 'Sin datos';
            } else if (densCal < 0.90) {
                badgeDensCal.className = 'badge bg-info text-dark sara-badge';
                badgeDensCal.textContent = 'Baja (<0.90)';
            } else if (densCal <= 1.20) {
                badgeDensCal.className = 'badge bg-success sara-badge';
                badgeDensCal.textContent = 'Normal (0.90-1.20)';
            } else {
                badgeDensCal.className = 'badge bg-warning text-dark sara-badge';
                badgeDensCal.textContent = 'Alta (>1.20)';
            }
        }

        // 2. Cociente Gramo / Caloría: Total Gramos / Total Kcal
        const cocienteGC = totalKcal > 0 ? (totalGramos / totalKcal) : 0;
        setTxt('ind_cociente_gc', totalKcal > 0 ? cocienteGC.toFixed(2) : '--');
        const badgeCocienteGC = document.getElementById('badge_cociente_gc');
        if (badgeCocienteGC) {
            badgeCocienteGC.textContent = totalKcal > 0 ? `${cocienteGC.toFixed(2)} g/kcal` : 'Sin datos';
        }

        // 3. % HC Complejos: (HC Complejos / Total HC Disp) * 100 (V.N: >= 50%)
        const porcHCComp = totalHcDisp > 0 ? ((sumHcComplejos / totalHcDisp) * 100) : 0;
        setTxt('ind_porc_hc_comp', totalHcDisp > 0 ? porcHCComp.toFixed(1) + '%' : '--');
        const badgeHCComp = document.getElementById('badge_hc_comp');
        if (badgeHCComp) {
            if (totalHcDisp === 0) {
                badgeHCComp.className = 'badge bg-secondary sara-badge';
                badgeHCComp.textContent = 'Sin datos';
            } else if (porcHCComp >= 50) {
                badgeHCComp.className = 'badge bg-success sara-badge';
                badgeHCComp.textContent = 'NORMAL (≥50%)';
            } else {
                badgeHCComp.className = 'badge bg-danger sara-badge';
                badgeHCComp.textContent = 'BAJO (<50%)';
            }
        }

        // 4. Cociente Ceto-Anticetogénico (Woodyatt)
        const numWoodyatt = (totalLip * 0.90) + (totalProt * 0.42);
        const denWoodyatt = (totalLip * 0.10) + totalHcDisp + (totalProt * 0.58);
        const woodyatt = denWoodyatt > 0 ? (numWoodyatt / denWoodyatt) : 0;
        setTxt('ind_woodyatt', denWoodyatt > 0 ? woodyatt.toFixed(2) : '--');
        const badgeWoodyatt = document.getElementById('badge_woodyatt');
        if (badgeWoodyatt) {
            if (denWoodyatt === 0) {
                badgeWoodyatt.className = 'badge bg-secondary sara-badge';
                badgeWoodyatt.textContent = 'Sin datos';
            } else if (woodyatt < 0.25) {
                badgeWoodyatt.className = 'badge bg-warning text-dark sara-badge';
                badgeWoodyatt.textContent = 'BAJO (<0.25)';
            } else if (woodyatt <= 0.35) {
                badgeWoodyatt.className = 'badge bg-success sara-badge';
                badgeWoodyatt.textContent = 'NORMAL (0.25-0.35)';
            } else {
                badgeWoodyatt.className = 'badge bg-danger sara-badge';
                badgeWoodyatt.textContent = 'ALTO (>0.35)';
            }
        }

        // 5. % Alimentos Protectores: (Kcal Protectores / Total Kcal) * 100 (V.N: > 50% Óptimo)
        const porcProtectores = totalKcal > 0 ? ((sumKcalProtectores / totalKcal) * 100) : 0;
        setTxt('ind_porc_protectores', totalKcal > 0 ? porcProtectores.toFixed(1) + '%' : '--');
        const badgeProtectores = document.getElementById('badge_protectores');
        if (badgeProtectores) {
            if (totalKcal === 0) {
                badgeProtectores.className = 'badge bg-secondary sara-badge';
                badgeProtectores.textContent = 'Sin datos';
            } else if (porcProtectores < 30) {
                badgeProtectores.className = 'badge bg-danger sara-badge';
                badgeProtectores.textContent = 'BAJO (<30%)';
            } else if (porcProtectores <= 50) {
                badgeProtectores.className = 'badge bg-warning text-dark sara-badge';
                badgeProtectores.textContent = 'ACEPTABLE (30-50%)';
            } else {
                badgeProtectores.className = 'badge bg-success sara-badge';
                badgeProtectores.textContent = 'ÓPTIMO (>50%)';
            }
        }

        // 6. % Proteínas AVB: (Prot AVB / Total Prot) * 100 (V.N: > 55% Óptimo)
        const porcAVB = totalProt > 0 ? ((sumProtAVB / totalProt) * 100) : 0;
        setTxt('ind_porc_avb', totalProt > 0 ? porcAVB.toFixed(1) + '%' : '--');
        const badgeAVB = document.getElementById('badge_avb');
        if (badgeAVB) {
            if (totalProt === 0) {
                badgeAVB.className = 'badge bg-secondary sara-badge';
                badgeAVB.textContent = 'Sin datos';
            } else if (porcAVB < 30) {
                badgeAVB.className = 'badge bg-danger sara-badge';
                badgeAVB.textContent = 'BAJO (<30%)';
            } else if (porcAVB <= 55) {
                badgeAVB.className = 'badge bg-warning text-dark sara-badge';
                badgeAVB.textContent = 'ACEPTABLE (30-55%)';
            } else {
                badgeAVB.className = 'badge bg-success sara-badge';
                badgeAVB.textContent = 'ÓPTIMO (>55%)';
            }
        }

        // 7. % Cubierto por Leche: (Kcal Leche / Total Kcal) * 100 (V.N: 6% a 10%)
        const porcLeche = totalKcal > 0 ? ((sumKcalLeche / totalKcal) * 100) : 0;
        setTxt('ind_porc_leche', totalKcal > 0 ? porcLeche.toFixed(1) + '%' : '--');
        const badgeLeche = document.getElementById('badge_leche');
        if (badgeLeche) {
            if (totalKcal === 0) {
                badgeLeche.className = 'badge bg-secondary sara-badge';
                badgeLeche.textContent = 'Sin datos';
            } else if (porcLeche >= 6 && porcLeche <= 10) {
                badgeLeche.className = 'badge bg-success sara-badge';
                badgeLeche.textContent = 'NORMAL (6-10%)';
            } else if (porcLeche < 6) {
                badgeLeche.className = 'badge bg-warning text-dark sara-badge';
                badgeLeche.textContent = 'BAJO (<6%)';
            } else {
                badgeLeche.className = 'badge bg-warning text-dark sara-badge';
                badgeLeche.textContent = 'ELEVADO (>10%)';
            }
        }
    }

    // =========================================================================
    // 6. GUARDADO ASÍNCRONO DE LA FÓRMULA SARA 2 (FETCH API)
    // =========================================================================
    if (btnGuardar) {
        btnGuardar.addEventListener('click', function () {
            const idPaciente = selectPaciente ? selectPaciente.value : null;

            if (!idPaciente || parseInt(idPaciente) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un Paciente',
                    text: 'Debes asociar esta planilla a un paciente de tu lista para guardarla.',
                    confirmButtonColor: 'var(--primary-green)'
                });
                return;
            }

            if (planillaItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Planilla Vacía',
                    text: 'Agrega al menos un alimento antes de guardar la fórmula desarrollada.',
                    confirmButtonColor: 'var(--primary-green)'
                });
                return;
            }

            const payload = {
                id_paciente: parseInt(idPaciente),
                id_formula: formulaIdActual ? parseInt(formulaIdActual) : null,
                nombre_formula: inputNombreFormula ? inputNombreFormula.value.trim() : 'Plan Nutricional SARA 2',
                kcal_objetivo: inputKcalObjetivo ? (parseFloat(inputKcalObjetivo.value) || null) : null,
                observaciones: txtObservaciones ? txtObservaciones.value.trim() : '',
                detalles: planillaItems.map(item => ({
                    id_alimento: item.id,
                    momento_dia: item.momento_dia || 'Almuerzo',
                    gramos: item.gramos
                }))
            };

            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Guardando...';

            fetch('index.php?action=api_guardar_formula', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i> Guardar Fórmula';

                    if (data.success) {
                        formulaIdActual = data.id_formula;
                        if (document.getElementById('formulaIdActual')) {
                            document.getElementById('formulaIdActual').value = data.id_formula;
                        }
                        if (btnImprimir) {
                            btnImprimir.classList.remove('d-none');
                            btnImprimir.href = `index.php?action=imprimir_formula&id=${data.id_formula}`;
                        }
                        mostrarToast('Fórmula Desarrollada SARA 2 guardada exitosamente.', 'success');
                        recargarHistorialPaciente(idPaciente);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al Guardar',
                            text: data.error || 'Ocurrió un error inesperado en el servidor.',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                })
                .catch(() => {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i> Guardar Fórmula';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo comunicar con el servidor.',
                        confirmButtonColor: '#e74c3c'
                    });
                });
        });
    }

    // =========================================================================
    // 7. CARGA DE HISTORIAL Y CAMBIO DE PACIENTE
    // =========================================================================
    if (selectPaciente) {
        selectPaciente.addEventListener('change', function () {
            const idPac = this.value;
            if (idPac && parseInt(idPac) > 0) {
                window.location.href = `index.php?action=formula_desarrollada&id_paciente=${idPac}`;
            }
        });
    }

    if (selectHistorial) {
        selectHistorial.addEventListener('change', function () {
            const idFormula = this.value;
            if (idFormula && parseInt(idFormula) > 0) {
                window.location.href = `index.php?action=formula_desarrollada&id_formula=${idFormula}`;
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            if (planillaItems.length === 0) return;
            Swal.fire({
                title: '¿Limpiar Planilla?',
                text: 'Se eliminarán todos los alimentos agregados en la grilla actual.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    planillaItems = [];
                    renderizarTabla();
                    recalcularTodo();
                    mostrarToast('Planilla reiniciada.', 'info');
                }
            });
        });
    }

    function cargarFormulaExistente(formula) {
        if (!formula || !Array.isArray(formula.detalles)) return;

        formulaIdActual = formula.id;
        if (inputNombreFormula && formula.nombre_formula) {
            inputNombreFormula.value = formula.nombre_formula;
        }
        if (inputKcalObjetivo && formula.kcal_objetivo) {
            inputKcalObjetivo.value = parseFloat(formula.kcal_objetivo);
        }
        if (txtObservaciones && formula.observaciones) {
            txtObservaciones.value = formula.observaciones;
        }
        if (btnImprimir) {
            btnImprimir.classList.remove('d-none');
            btnImprimir.href = `index.php?action=imprimir_formula&id=${formula.id}`;
        }

        planillaItems = [];
        formula.detalles.forEach(d => {
            agregarAlimentoAPlanilla(d, parseFloat(d.gramos), d.momento_dia || 'Almuerzo');
        });
    }

    function recargarHistorialPaciente(idPaciente) {
        if (!selectHistorial) return;
        fetch(`index.php?action=api_historial_formulas&id_paciente=${idPaciente}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    selectHistorial.innerHTML = '<option value="">-- Cargar fórmula previa --</option>';
                    data.data.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.textContent = `${f.nombre_formula} (${f.fecha_creacion.substring(0, 10)})`;
                        if (formulaIdActual && parseInt(formulaIdActual) === parseInt(f.id)) {
                            opt.selected = true;
                        }
                        selectHistorial.appendChild(opt);
                    });
                }
            });
    }

    // =========================================================================
    // UTILIDADES Y HELPERS
    // =========================================================================
    function setTxt(id, valor) {
        const el = document.getElementById(id);
        if (el) el.textContent = valor;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function mostrarToast(mensaje, icono = 'success') {
        if (typeof Swal !== 'undefined' && Swal.mixin) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon: icono, title: mensaje });
        } else {
            alert(mensaje);
        }
    }
});
