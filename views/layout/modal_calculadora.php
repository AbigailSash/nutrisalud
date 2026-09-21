<!-- Modal Calculadora Rápida -->
<div class="modal fade" id="modalCalculadoraRapida" tabindex="-1" aria-labelledby="modalCalculadoraRapidaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                <h5 class="modal-title fw-bold" id="modalCalculadoraRapidaLabel"><i class="fa-solid fa-calculator me-2"></i> Suite Clínica Rápida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-justified" id="calcTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="tmb-tab" data-bs-toggle="tab" data-bs-target="#tmb-pane" type="button" role="tab" style="color: var(--dark-green);">TMB y Gasto</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="minerales-tab" data-bs-toggle="tab" data-bs-target="#minerales-pane" type="button" role="tab" style="color: var(--dark-green);">Conversor Mineral</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="catabolismo-tab" data-bs-toggle="tab" data-bs-target="#catabolismo-pane" type="button" role="tab" style="color: var(--dark-green);">Catabolismo (NUU)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold" id="riesgocv-tab" data-bs-toggle="tab" data-bs-target="#riesgocv-pane" type="button" role="tab" style="color: #dc2626;"><i class="fa-solid fa-heart-pulse text-danger me-1"></i> Riesgo CV (HEARTS)</button>
                    </li>
                </ul>
                <div class="tab-content p-4" id="calcTabsContent">
                    
                    <!-- TAB 1: TMB y Gasto -->
                    <div class="tab-pane fade show active" id="tmb-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Sexo</label>
                                <select id="calc_sexo" class="form-select shadow-sm border-0 bg-light">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Edad (años)</label>
                                <input type="number" id="calc_edad" class="form-control shadow-sm border-0 bg-light" placeholder="Ej: 30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Peso (kg)</label>
                                <input type="number" step="0.1" id="calc_peso" class="form-control shadow-sm border-0 bg-light" placeholder="Ej: 70">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold">Nivel de Actividad General (Factor)</label>
                                <select id="calc_naf" class="form-select shadow-sm border-0 bg-light">
                                    <option value="" selected disabled>Seleccionar nivel...</option>
                                    <option value="1.2">Sedentario / Leve (1.2)</option>
                                    <option value="1.55">Moderada (1.55)</option>
                                    <option value="1.725">Intensa (1.725)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded text-center border">
                                    <h6 class="text-muted mb-1">TMB (FAO/OMS)</h6>
                                    <h3 class="mb-0 fw-bold" id="res_tmb" style="color: var(--dark-green);">-- <small class="fs-6">kcal</small></h3>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded text-center border">
                                    <h6 class="text-muted mb-1">VCT (Gasto Total)</h6>
                                    <h3 class="mb-0 fw-bold text-primary" id="res_vct">-- <small class="fs-6">kcal</small></h3>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-end d-none" id="btn_exportar_vct_container">
                            <button class="btn btn-sm btn-outline-primary btn-exportar" data-target="vct">Exportar VCT a Consulta</button>
                        </div>
                    </div>

                    <!-- TAB 2: Conversor Mineral -->
                    <div class="tab-pane fade" id="minerales-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">Mineral</label>
                                <select id="calc_mineral" class="form-select shadow-sm border-0 bg-light">
                                    <option value="" selected disabled>Seleccionar mineral...</option>
                                    <option value="23">Sodio (Na) - x 23</option>
                                    <option value="39.1">Potasio (K) - x 39.1</option>
                                    <option value="31">Fósforo (P) - x 31</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">Valor en mEq</label>
                                <input type="number" step="0.1" id="calc_meq" class="form-control shadow-sm border-0 bg-light" placeholder="Ej: 50">
                            </div>
                        </div>
                        <div class="row mt-4 justify-content-center">
                            <div class="col-8">
                                <div class="p-4 bg-light rounded text-center border">
                                    <h6 class="text-muted mb-1">Equivalencia en mg</h6>
                                    <h2 class="mb-0 text-info" id="res_mg">-- <small class="fs-5">mg</small></h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Catabolismo (NUU) -->
                    <div class="tab-pane fade" id="catabolismo-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">Urea en orina (g/L)</label>
                                <input type="number" step="0.1" id="calc_urea" class="form-control shadow-sm border-0 bg-light" placeholder="Ej: 15">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted fw-bold">Diuresis (Litros/24h)</label>
                                <input type="number" step="0.1" id="calc_diuresis" class="form-control shadow-sm border-0 bg-light" placeholder="Ej: 1.5">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold">Proteínas Exógenas (g/día) [Opcional para IC]</label>
                                <input type="number" step="0.1" id="calc_prot_exogenas" class="form-control shadow-sm border-0 bg-light" placeholder="Proteínas recibidas">
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded text-center border h-100">
                                    <h6 class="text-muted mb-1">NUU (g/día)</h6>
                                    <h3 class="mb-0 fw-bold" id="res_nuu" style="color: var(--dark-green);">--</h3>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded text-center border h-100">
                                    <h6 class="text-muted mb-1">Índice Catabólico (IC)</h6>
                                    <h3 class="mb-0 text-danger" id="res_ic">--</h3>
                                    <span class="badge bg-secondary mt-1" id="badge_ic">N/A</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-end d-none" id="btn_exportar_nuu_container">
                            <button class="btn btn-sm btn-outline-primary btn-exportar" data-target="nuu">Exportar NUU a Consulta</button>
                        </div>
                    </div>

                    <!-- TAB 4: Riesgo Cardiovascular (HEARTS / OMS) -->
                    <div class="tab-pane fade" id="riesgocv-pane" role="tabpanel">
                        <div class="alert alert-light border d-flex align-items-center py-2 px-3 mb-3 small">
                            <i class="fa-solid fa-heart-pulse text-danger fs-4 me-3"></i>
                            <div>
                                <strong>Calculadora Oficial OPS / HEARTS en las Américas / OMS 2019</strong><br>
                                <span class="text-muted">Estimación del riesgo de sufrir un infarto o ACV a 10 años (Matriz AMR B / Cono Sur).</span>
                            </div>
                        </div>

                        <!-- Filtros de Alto Riesgo Preexistente -->
                        <div class="bg-light p-3 rounded mb-3 border">
                            <span class="fw-bold text-dark small d-block mb-2"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Condiciones de Alto Riesgo Preexistente</span>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="modal_cv_ecv">
                                        <label class="form-check-label small" for="modal_cv_ecv">Antecedente de ECV (Infarto / ACV)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="modal_cv_erc">
                                        <label class="form-check-label small" for="modal_cv_erc">Enfermedad Renal Crónica (ERC)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parámetros de la Evaluación -->
                        <div id="modal_cv_campos_grid" class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label text-muted fw-bold small mb-1">Sexo</label>
                                <select id="modal_cv_sexo" class="form-select form-select-sm bg-light">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted fw-bold small mb-1">Edad (años)</label>
                                <input type="number" id="modal_cv_edad" class="form-control form-control-sm bg-light" value="50" min="18" max="100">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted fw-bold small mb-1">Diabetes</label>
                                <select id="modal_cv_diabetes" class="form-select form-select-sm bg-light">
                                    <option value="0">No Diabético</option>
                                    <option value="1">Diabético</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted fw-bold small mb-1">Tabaquismo</label>
                                <select id="modal_cv_tabaco" class="form-select form-select-sm bg-light">
                                    <option value="0">No Fumador</option>
                                    <option value="1">Fumador Actual</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="form-label text-muted fw-bold small mb-1">Presión Sistólica (PAS mmHg)</label>
                                <input type="number" id="modal_cv_pas" class="form-control form-control-sm bg-light" value="130" min="70" max="250">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="form-label text-muted fw-bold small mb-1">Vía de Evaluación</label>
                                <select id="modal_cv_via" class="form-select form-select-sm bg-light">
                                    <option value="imc">Sin Colesterol (IMC)</option>
                                    <option value="colesterol">Con Colesterol Total</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-2" id="modal_cv_grupo_col" style="display: none;">
                                <label class="form-label text-muted fw-bold small mb-1">Colesterol Total (mg/dL)</label>
                                <input type="number" id="modal_cv_col" class="form-control form-control-sm bg-light" placeholder="Ej: 200" value="200">
                            </div>
                            <div class="col-md-4 mt-2" id="modal_cv_grupo_imc">
                                <label class="form-label text-muted fw-bold small mb-1">IMC (kg/m²)</label>
                                <input type="number" step="0.1" id="modal_cv_imc" class="form-control form-control-sm bg-light" placeholder="Ej: 26.5" value="25.0">
                            </div>
                        </div>

                        <!-- Tarjeta de Resultados del Riesgo -->
                        <div class="card mt-3 border shadow-sm">
                            <div class="card-body p-3 text-center">
                                <small class="text-uppercase text-muted fw-bold d-block mb-1" style="letter-spacing: 0.5px;">Riesgo Cardiovascular a 10 Años</small>
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    <h2 class="mb-0 fw-bold" id="modal_res_cv_pct">--</h2>
                                    <span class="badge fs-6 py-2 px-3" id="modal_res_cv_badge" style="background-color: #10b981;">Bajo (&lt;5%)</span>
                                </div>
                                <p class="small text-muted mb-0 mt-2" id="modal_res_cv_motivo">Estratificación oficial OMS 2019 AMR B (Cono Sur).</p>
                                
                                <div class="progress mt-2" style="height: 8px;">
                                    <div id="modal_res_cv_bar" class="progress-bar" role="progressbar" style="width: 15%; background-color: #10b981;"></div>
                                </div>

                                <div class="row g-2 mt-2 pt-2 border-top text-start small">
                                    <div class="col-4"><strong>Meta PAS:</strong> <span id="modal_res_cv_metapas">&lt; 140/90 mmHg</span></div>
                                    <div class="col-4"><strong>Meta LDL:</strong> <span id="modal_res_cv_metaldl">&lt; 116 mg/dL</span></div>
                                    <div class="col-4"><strong>Seguimiento:</strong> <span id="modal_res_cv_seg">Cada 3-5 años</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Simulador Interactivo What-if -->
                        <div class="card mt-2 border border-info bg-light">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-primary small"><i class="fa-solid fa-wand-magic-sparkles me-1"></i> Simulador "¿Qué pasaría si...?"</span>
                                    <span class="badge bg-primary small" id="modal_cv_delta_badge">Δ 0.0 pts</span>
                                </div>
                                <div class="row g-2 small">
                                    <div class="col-4">
                                        <label class="text-muted">Tabaquismo:</label>
                                        <select id="modal_sim_tabaco" class="form-select form-select-sm">
                                            <option value="0">No Fumador</option>
                                            <option value="1">Fumador</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="text-muted">Meta PAS:</label>
                                        <input type="number" id="modal_sim_pas" class="form-control form-control-sm" value="120">
                                    </div>
                                    <div class="col-4">
                                        <label class="text-muted">Meta IMC:</label>
                                        <input type="number" step="0.1" id="modal_sim_imc" class="form-control form-control-sm" value="23.5">
                                    </div>
                                </div>
                                <div class="mt-2 text-dark small" id="modal_sim_mensaje" style="font-size: 0.8rem;">
                                    Modifica los parámetros para visualizar el impacto motivacional del tratamiento nutricional.
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-end d-none" id="btn_exportar_cv_container">
                            <button class="btn btn-sm btn-outline-danger btn-exportar" data-target="riesgo_cv"><i class="fa-solid fa-file-export me-1"></i> Exportar Riesgo CV a Consulta</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
