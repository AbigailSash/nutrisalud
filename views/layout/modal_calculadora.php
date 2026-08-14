<!-- Modal Calculadora Rápida -->
<div class="modal fade" id="modalCalculadoraRapida" tabindex="-1" aria-labelledby="modalCalculadoraRapidaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalCalculadoraRapidaLabel"><i class="fa-solid fa-calculator"></i> Suite Clínica Rápida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-justified" id="calcTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-success" id="tmb-tab" data-bs-toggle="tab" data-bs-target="#tmb-pane" type="button" role="tab">TMB y Gasto</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success" id="minerales-tab" data-bs-toggle="tab" data-bs-target="#minerales-pane" type="button" role="tab">Conversor Mineral</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-success" id="catabolismo-tab" data-bs-toggle="tab" data-bs-target="#catabolismo-pane" type="button" role="tab">Catabolismo (NUU)</button>
                    </li>
                </ul>
                <div class="tab-content p-4" id="calcTabsContent">
                    
                    <!-- TAB 1: TMB y Gasto -->
                    <div class="tab-pane fade show active" id="tmb-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Sexo</label>
                                <select id="calc_sexo" class="form-select shadow-sm border-0 bg-light">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Edad (años)</label>
                                <input type="number" id="calc_edad" class="form-control shadow-sm border-0 bg-light" value="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold">Peso (kg)</label>
                                <input type="number" step="0.1" id="calc_peso" class="form-control shadow-sm border-0 bg-light" value="70">
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <label class="form-label text-muted fw-bold">Nivel de Actividad General (Factor)</label>
                                <select id="calc_naf" class="form-select shadow-sm border-0 bg-light">
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
                                    <h3 class="mb-0 text-success" id="res_tmb">-- <small class="fs-6">kcal</small></h3>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded text-center border">
                                    <h6 class="text-muted mb-1">VCT (Gasto Total)</h6>
                                    <h3 class="mb-0 text-primary" id="res_vct">-- <small class="fs-6">kcal</small></h3>
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
                                    <h3 class="mb-0 text-success" id="res_nuu">--</h3>
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

                </div>
            </div>
        </div>
    </div>
</div>
