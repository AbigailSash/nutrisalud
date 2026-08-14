<?php 
// views/pacientes/historia_clinica.php
// Expected variables: $paciente (array), $datosHistoria (array)

function getVal($datosHistoria, $key, $default = '') {
    return isset($datosHistoria[$key]) ? htmlspecialchars($datosHistoria[$key]) : $default;
}

function renderCustomFields($seccion, $camposCustom) {
    $html = '<div class="custom-fields-container" id="custom-fields-' . $seccion . '">';
    if (isset($camposCustom[$seccion])) {
        foreach ($camposCustom[$seccion] as $index => $campo) {
            $html .= '<div class="custom-field-row row g-3 mt-2 border rounded p-3 bg-light position-relative">';
            $html .= '<button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 rounded-circle" style="width: 32px; height: 32px; right: 10px;" onclick="this.closest(\'.custom-field-row\').remove()" title="Eliminar campo"><i class="fa-solid fa-trash"></i></button>';
            $html .= '<div class="col-md-12 pe-5">';
            $html .= '<input type="text" class="form-control fw-bold mb-2" name="campos_custom[' . $seccion . '][' . $index . '][titulo]" placeholder="Título (ej: Alergias, Observaciones, etc.)" value="' . htmlspecialchars($campo['titulo']) . '">';
            $html .= '<textarea class="form-control" name="campos_custom[' . $seccion . '][' . $index . '][contenido]" rows="2" placeholder="Escribe aquí el contenido libre...">' . htmlspecialchars($campo['contenido']) . '</textarea>';
            $html .= '</div></div>';
        }
    }
    $html .= '</div>';
    $html .= '<div class="mt-3">';
    $html .= '<button type="button" class="btn btn-sm btn-outline-primary btn-add-custom" data-seccion="' . $seccion . '"><i class="fa-solid fa-plus me-1"></i> Agregar campo personalizado</button>';
    $html .= '</div>';
    return $html;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica - <?= htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #2c3e50; }
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        .main-content { min-height: 100vh; padding: 30px; margin-left: 280px; width: calc(100% - 280px); overflow-y: auto; transition: margin-left 0.3s ease, width 0.3s ease; }
        .form-section { background: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); }
        .section-title { color: var(--dark-green); font-weight: 600; border-bottom: 2px solid var(--light-green); padding-bottom: 10px; margin-bottom: 20px; }
        .form-label { font-weight: 500; font-size: 0.9rem; color: #34495e; }
        .form-control, .form-select { border-radius: 8px; border: 1px solid #ced4da; padding: 10px 15px; font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-green) !important; box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--primary-green) 25%, transparent) !important; }
        .btn-success { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important; border: none !important; font-weight: 600; padding: 10px 25px; border-radius: 50px; }
        .btn-success:hover { opacity: 0.95; transform: translateY(-1px); }
        .nav-pills .nav-link { color: #34495e; border-radius: 50px; margin: 0 5px; font-weight: 500; }
        .nav-pills .nav-link.active { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important; color: white !important; }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include 'views/layout/sidebar.php'; ?>
        
        <div class="main-content" id="app-container" data-sexo="<?= htmlspecialchars($paciente['Sexo'] ?? 'M') ?>">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fa-solid fa-notes-medical text-success me-2"></i> Historia Clínica</h2>
                <div>
                    <a href="index.php?action=imprimir_ficha_medica&id=<?= $paciente['IdPaciente'] ?>" target="_blank" class="btn btn-outline-info rounded-pill me-2">
                        <i class="fa-solid fa-print"></i> Imprimir Ficha
                    </a>
                    <a href="index.php?action=listar_pacientes" class="btn btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                </div>
            </div>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
                    <?= $_SESSION['mensaje'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); endif; ?>

            <form action="index.php?action=guardar_historia_clinica" method="POST">
                <input type="hidden" name="id_paciente" value="<?= $paciente['IdPaciente'] ?>">

                <!-- Navegación por Pestañas -->
                <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-datos" type="button">Datos y Motivo</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-antecedentes" type="button">Antecedentes</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-antropo" type="button">Antropometría & Lab</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-habitos" type="button">Hábitos y 24hs</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-frecuencia" type="button">Frecuencia Consumo</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-seguimiento" type="button">Seguimiento</button></li>
                </ul>

                <div class="tab-content">
                    
                    <!-- TAB 1: DATOS -->
                    <div class="tab-pane fade show active" id="tab-datos">
                        <div class="form-section">
                            <h4 class="section-title">Datos del Paciente</h4>
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label">Nombre</label><input type="text" class="form-control" value="<?= htmlspecialchars($paciente['Nombre']) ?>" readonly></div>
                                <div class="col-md-3"><label class="form-label">Apellido</label><input type="text" class="form-control" value="<?= htmlspecialchars($paciente['Apellido']) ?>" readonly></div>
                                <div class="col-md-3">
                                    <label class="form-label">Sexo</label>
                                    <select name="sexo" class="form-select">
                                        <?php $sexoActual = getVal($datosHistoria, 'sexo', $paciente['Sexo'] ?? ''); ?>
                                        <option value="F" <?= $sexoActual == 'F' ? 'selected' : '' ?>>Femenino</option>
                                        <option value="M" <?= $sexoActual == 'M' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="Otro" <?= (!in_array($sexoActual, ['F', 'M'])) ? 'selected' : '' ?>>Otro / N/D</option>
                                    </select>
                                </div>
                                <div class="col-md-3"><label class="form-label">Fecha Nacimiento</label><input type="text" class="form-control" value="<?= htmlspecialchars($paciente['Fecha_Nacimiento']) ?>" readonly></div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">Fecha de Consulta</label>
                                    <input type="date" class="form-control" name="fecha_consulta" value="<?= getVal($datosHistoria, 'fecha_consulta', date('Y-m-d')) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Localidad de residencia</label>
                                    <input type="text" class="form-control" name="localidad" value="<?= getVal($datosHistoria, 'localidad') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Domicilio</label>
                                    <input type="text" class="form-control" name="domicilio" value="<?= getVal($datosHistoria, 'domicilio') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nivel de escolaridad</label>
                                    <input type="text" class="form-control" name="escolaridad" value="<?= getVal($datosHistoria, 'escolaridad') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Composición familiar</label>
                                    <input type="text" class="form-control" name="composicion_familiar" value="<?= getVal($datosHistoria, 'composicion_familiar') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Situación/Datos económicos</label>
                                    <input type="text" class="form-control" name="situacion_economica" value="<?= getVal($datosHistoria, 'situacion_economica') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Motivo de consulta</label>
                                    <textarea class="form-control" name="motivo_consulta" rows="2"><?= getVal($datosHistoria, 'motivo_consulta') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Diagnóstico clínico</label>
                                    <textarea class="form-control" name="diagnostico_clinico" rows="2"><?= getVal($datosHistoria, 'diagnostico_clinico') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('datos_motivo', $camposCustom ?? []) ?>
                        </div>
                    </div>

                    <!-- TAB 2: ANTECEDENTES -->
                    <div class="tab-pane fade" id="tab-antecedentes">
                        <div class="form-section">
                            <h4 class="section-title">Antecedentes Clínicos</h4>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Antecedentes familiares de enfermedad</label>
                                    <textarea class="form-control" name="antecedentes_familiares" rows="2"><?= getVal($datosHistoria, 'antecedentes_familiares') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">¿Presenta otras patologías?</label>
                                    <textarea class="form-control" name="otras_patologias" rows="2"><?= getVal($datosHistoria, 'otras_patologias') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">¿Ha tenido operaciones u hospitalizaciones?</label>
                                    <textarea class="form-control" name="operaciones" rows="2"><?= getVal($datosHistoria, 'operaciones') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Sintomatología Gastrointestinal (Disfagia, reflujo, náuseas, vómitos, distensión...)</label>
                                    <textarea class="form-control" name="sintomas_gastro" rows="2"><?= getVal($datosHistoria, 'sintomas_gastro') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Pérdida de peso involuntaria (>5% en un mes o >10% en 6 meses)</label>
                                    <textarea class="form-control" name="perdida_peso_involuntaria" rows="2" placeholder="Velocidad y porcentaje de la pérdida..."><?= getVal($datosHistoria, 'perdida_peso_involuntaria') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Interacción Fármaco-Nutriente (Medicamentos, suplementos de venta libre...)</label>
                                    <textarea class="form-control" name="medicamentos" rows="2" placeholder="Rosuvastatina, Losartan, Metformina, etc..."><?= getVal($datosHistoria, 'medicamentos') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('antecedentes', $camposCustom ?? []) ?>
                        </div>
                    </div>

                    <!-- TAB 3: ANTROPOMETRÍA Y BIOQUÍMICA -->
                    <div class="tab-pane fade" id="tab-antropo">
                        <div class="form-section mb-4">
                            <h4 class="section-title">Datos Antropométricos Base</h4>
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label">Talla (mts)</label><input type="number" step="0.01" class="form-control" name="talla" id="antropo_talla" value="<?= getVal($datosHistoria, 'talla') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Peso Actual (kg)</label><input type="number" step="0.1" class="form-control" name="peso_actual" id="antropo_peso" value="<?= getVal($datosHistoria, 'peso_actual') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Circ. Muñeca (cm)</label><input type="number" step="0.1" class="form-control" name="circ_muneca" id="antropo_muneca" value="<?= getVal($datosHistoria, 'circ_muneca') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Peso Usual (kg)</label><input type="number" step="0.1" class="form-control" name="peso_usual" id="antropo_peso_usual" value="<?= getVal($datosHistoria, 'peso_usual') ?>"></div>
                                
                                <!-- Circunferencias adicionales -->
                                <div class="col-md-3"><label class="form-label">Circ. Cintura (cm)</label><input type="number" step="0.1" class="form-control" name="circ_cintura" id="antropo_cintura" value="<?= getVal($datosHistoria, 'circ_cintura') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Circ. Cadera (cm)</label><input type="number" step="0.1" class="form-control" name="circ_cadera" id="antropo_cadera" value="<?= getVal($datosHistoria, 'circ_cadera') ?>"></div>
                                <div class="col-md-3">
                                    <label class="form-label">Relación Cintura/Cadera</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control" name="relacion_cc" id="antropo_rcc" value="<?= getVal($datosHistoria, 'relacion_cc') ?>" placeholder="Calculado o manual">
                                        <span class="badge d-flex align-items-center bg-secondary" id="badge_rcc" style="border-top-left-radius: 0; border-bottom-left-radius: 0; font-size: 0.75rem;">--</span>
                                    </div>
                                </div>
                                <div class="col-md-3"><label class="form-label">Circ. Media Brazo (cm)</label><input type="number" step="0.1" class="form-control" name="circ_brazo" id="antropo_brazo" value="<?= getVal($datosHistoria, 'circ_brazo') ?>"></div>
                            </div>
                            
                            <!-- Panel de Resultados Reactivos -->
                            <div class="row g-3 mt-4" id="panel-antropometria-reactiva">
                                <div class="col-md-3">
                                    <div class="card bg-light shadow-sm text-center">
                                        <div class="card-body py-2">
                                            <h6 class="text-muted mb-1">IMC</h6>
                                            <h4 class="mb-0" id="res_imc">--</h4>
                                            <span class="badge bg-secondary mt-1" id="badge_imc">N/A</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light shadow-sm text-center">
                                        <div class="card-body py-2">
                                            <h6 class="text-muted mb-1">Peso Ideal</h6>
                                            <h4 class="mb-0" id="res_pi">-- kg</h4>
                                            <small class="text-muted" style="font-size: 0.7rem;" id="res_pic_container">Corregido: <span id="res_pic">--</span></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light shadow-sm text-center">
                                        <div class="card-body py-2">
                                            <h6 class="text-muted mb-1">% Peso Ideal</h6>
                                            <h4 class="mb-0" id="res_pct_pi">-- %</h4>
                                            <span class="badge bg-secondary mt-1" id="badge_pct_pi">N/A</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light shadow-sm text-center">
                                        <div class="card-body py-2">
                                            <h6 class="text-muted mb-1">Contextura</h6>
                                            <h4 class="mb-0" id="res_contextura">--</h4>
                                            <small class="text-muted" style="font-size: 0.7rem;">% P. Usual: <span id="res_pct_pu">--</span>%</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="section-title">Datos Bioquímicos</h4>
                            <div class="row g-3">
                                <div class="col-md-3"><label class="form-label">Triglicéridos</label><input type="text" class="form-control" name="bio_tg" value="<?= getVal($datosHistoria, 'bio_tg') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Colesterol Total</label><input type="text" class="form-control" name="bio_col_total" value="<?= getVal($datosHistoria, 'bio_col_total') ?>"></div>
                                <div class="col-md-3"><label class="form-label">HDL</label><input type="text" class="form-control" name="bio_hdl" value="<?= getVal($datosHistoria, 'bio_hdl') ?>"></div>
                                <div class="col-md-3"><label class="form-label">LDL</label><input type="text" class="form-control" name="bio_ldl" value="<?= getVal($datosHistoria, 'bio_ldl') ?>"></div>
                                
                                <div class="col-md-3"><label class="form-label">Proteínas Totales</label><input type="text" class="form-control" name="bio_prot" value="<?= getVal($datosHistoria, 'bio_prot') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Albúmina</label><input type="text" class="form-control" name="bio_albumina" value="<?= getVal($datosHistoria, 'bio_albumina') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Hemoglobina</label><input type="text" class="form-control" name="bio_hemo" value="<?= getVal($datosHistoria, 'bio_hemo') ?>"></div>
                                <div class="col-md-3"><label class="form-label">Eritrocitos</label><input type="text" class="form-control" name="bio_eri" value="<?= getVal($datosHistoria, 'bio_eri') ?>"></div>
                                
                                <div class="col-md-6"><label class="form-label">Glucemia</label><input type="text" class="form-control" name="bio_glucemia" value="<?= getVal($datosHistoria, 'bio_glucemia') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Resultados PTOG</label><input type="text" class="form-control" name="bio_ptog" value="<?= getVal($datosHistoria, 'bio_ptog') ?>"></div>
                            </div>
                        </div>

                        <div class="form-section mb-4">
                            <h4 class="section-title">Signos Vitales y Capacidad Funcional</h4>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Presión Arterial (mmHg)</label><input type="text" class="form-control" name="presion_arterial" value="<?= getVal($datosHistoria, 'presion_arterial') ?>"></div>
                                <div class="col-md-4"><label class="form-label">Frecuencia Cardíaca (lpm)</label><input type="text" class="form-control" name="frecuencia_cardiaca" value="<?= getVal($datosHistoria, 'frecuencia_cardiaca') ?>"></div>
                                <div class="col-md-4"><label class="form-label">Capacidad Funcional / Dinamometría</label><input type="text" class="form-control" name="capacidad_funcional" placeholder="Independencia física, fuerza de agarre..." value="<?= getVal($datosHistoria, 'capacidad_funcional') ?>"></div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="section-title">Examen Físico Orientado a la Nutrición (NFPE)</h4>
                            <p class="text-muted">Marque los signos clínicos presentes detectados en la inspección visual.</p>
                            
                            <?php
                            $signosClinicos = [
                                'nfpe_edema' => 'Edema periférico (Hinchazón tobillos/pies, hipoalbuminemia)',
                                'nfpe_ictericia' => 'Ictericia (Coloración amarillenta piel/esclera)',
                                'nfpe_ascitis' => 'Ascitis (Acumulación líquido cavidad peritoneal)',
                                'nfpe_coiloniquia' => 'Coiloniquia (Uñas cuchara, déficit de hierro)',
                                'nfpe_acanthosis' => 'Acanthosis nigricans (Pliegues oscuros, resistencia insulina)',
                                'nfpe_glositis' => 'Glositis (Lengua lisa, déficit B12/folato/hierro)',
                                'nfpe_xantelasmas' => 'Xantelasmas / Xantomas (Depósitos lipídicos)',
                                'nfpe_bichat' => 'Pérdida de bola grasosa de Bichat (Hundimiento mejillas)',
                                'nfpe_temporal' => 'Atrofia del músculo temporal (Sarcopenia)',
                                'nfpe_capilar' => 'Llenado capilar lento (> 2 segundos)'
                            ];
                            ?>
                            <div class="row g-3">
                                <?php foreach($signosClinicos as $key => $label): ?>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="<?= $key ?>" value="Si" <?= getVal($datosHistoria, $key) == 'Si' ? 'checked' : '' ?> id="<?= $key ?>">
                                        <label class="form-check-label" for="<?= $key ?>">
                                            <?= $label ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Panel de Recomendaciones Automáticas NFPE -->
                            <div class="row mt-4 d-none" id="panel-recomendaciones-nfpe">
                                <div class="col-12">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-header bg-white fw-bold text-success border-0 pt-3">
                                            <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Recomendaciones Clínicas (NFPE)
                                        </div>
                                        <div class="card-body" id="nfpe-alertas-container">
                                            <!-- Inyectado vía JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="nfpe_recomendaciones_json" id="nfpe_recomendaciones_json" value="">
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-12">
                                    <label class="form-label">Otros hallazgos físicos (Cabello, piel, uñas, etc.)</label>
                                    <textarea class="form-control" name="nfpe_observaciones" rows="2"><?= getVal($datosHistoria, 'nfpe_observaciones') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('antropometria', $camposCustom ?? []) ?>
                        </div>
                    </div>

                    <!-- TAB 4: HÁBITOS, EVALUACIÓN ALIMENTARIA Y 24H -->
                    <div class="tab-pane fade" id="tab-habitos">
                        <div class="form-section mb-4">
                            <h4 class="section-title">Actividad Física</h4>
                            <div class="row g-3">
                                <div class="col-md-12"><label class="form-label">¿Qué actividades diarias realiza?</label><input type="text" class="form-control" name="act_diaria" value="<?= getVal($datosHistoria, 'act_diaria') ?>"></div>
                                <div class="col-md-6"><label class="form-label">¿Cuántas veces a la semana realiza actividad física?</label><input type="text" class="form-control" name="act_frecuencia" value="<?= getVal($datosHistoria, 'act_frecuencia') ?>"></div>
                                <div class="col-md-6"><label class="form-label">¿Qué tipo de entrenamiento o ejercicio realiza?</label><input type="text" class="form-control" name="act_tipo" value="<?= getVal($datosHistoria, 'act_tipo') ?>"></div>
                            </div>
                        </div>

                        <div class="form-section mb-4">
                            <h4 class="section-title">Evaluación Alimentaria y Hábitos</h4>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Horarios de trabajo/rutina</label><input type="text" class="form-control" name="hab_horarios" value="<?= getVal($datosHistoria, 'hab_horarios') ?>"></div>
                                <div class="col-md-4"><label class="form-label">¿Con quién vive?</label><input type="text" class="form-control" name="hab_convivencia" value="<?= getVal($datosHistoria, 'hab_convivencia') ?>"></div>
                                <div class="col-md-4"><label class="form-label">¿Quién cocina en el hogar?</label><input type="text" class="form-control" name="hab_cocinero" value="<?= getVal($datosHistoria, 'hab_cocinero') ?>"></div>
                                
                                <div class="col-md-4"><label class="form-label">¿A qué hora se levanta?</label><input type="time" class="form-control" name="hab_hora_levanta" value="<?= getVal($datosHistoria, 'hab_hora_levanta') ?>"></div>
                                <div class="col-md-4"><label class="form-label">¿Quién elige los alimentos a comprar?</label><input type="text" class="form-control" name="hab_comprador" value="<?= getVal($datosHistoria, 'hab_comprador') ?>"></div>
                                <div class="col-md-4"><label class="form-label">¿Cada cuánto realizan las compras?</label><input type="text" class="form-control" name="hab_frec_compra" value="<?= getVal($datosHistoria, 'hab_frec_compra') ?>"></div>
                                
                                <div class="col-md-12"><label class="form-label">¿Cómo obtienen los alimentos? (feria, huertas, súper, etc.)</label><input type="text" class="form-control" name="hab_origen_alim" value="<?= getVal($datosHistoria, 'hab_origen_alim') ?>"></div>
                                <div class="col-md-12"><label class="form-label">Número de comensales y edades</label><input type="text" class="form-control" name="hab_comensales" value="<?= getVal($datosHistoria, 'hab_comensales') ?>"></div>

                                <div class="col-md-6">
                                    <label class="form-label">¿Realiza las 4 comidas principales?</label>
                                    <select class="form-select" name="hab_4_comidas">
                                        <option value="Si" <?= getVal($datosHistoria, 'hab_4_comidas')=='Si'?'selected':'' ?>>Sí</option>
                                        <option value="No" <?= getVal($datosHistoria, 'hab_4_comidas')=='No'?'selected':'' ?>>No</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><label class="form-label">¿Alimentos fuera de horario? ¿Cuáles?</label><input type="text" class="form-control" name="hab_picoteo" value="<?= getVal($datosHistoria, 'hab_picoteo') ?>"></div>
                                
                                <div class="col-md-6"><label class="form-label">¿Realiza comidas fuera del hogar? Frecuencia</label><input type="text" class="form-control" name="hab_afuera" value="<?= getVal($datosHistoria, 'hab_afuera') ?>"></div>
                                <div class="col-md-6"><label class="form-label">¿Agrega azúcar y sal a las comidas? ¿Cantidad?</label><input type="text" class="form-control" name="hab_azucar_sal" value="<?= getVal($datosHistoria, 'hab_azucar_sal') ?>"></div>

                                <div class="col-md-12"><label class="form-label">Alergias o intolerancias</label><input type="text" class="form-control" name="hab_alergias" value="<?= getVal($datosHistoria, 'hab_alergias') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Alimentos de preferencia</label><input type="text" class="form-control" name="hab_gustos" value="<?= getVal($datosHistoria, 'hab_gustos') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Alimentos que no son de agrado</label><input type="text" class="form-control" name="hab_disgustos" value="<?= getVal($datosHistoria, 'hab_disgustos') ?>"></div>
                                
                                <div class="col-md-12"><label class="form-label">Suplementos vitamínicos/minerales (Dosis y frecuencia)</label><input type="text" class="form-control" name="hab_suplementos" value="<?= getVal($datosHistoria, 'hab_suplementos') ?>"></div>
                                
                                <div class="col-md-6"><label class="form-label">Consumo de alcohol (Frecuencia)</label><input type="text" class="form-control" name="hab_alcohol" value="<?= getVal($datosHistoria, 'hab_alcohol') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Mate/Tereré (Frecuencia)</label><input type="text" class="form-control" name="hab_mate" value="<?= getVal($datosHistoria, 'hab_mate') ?>"></div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="section-title">Recordatorio de 24 Horas</h4>
                            <p class="text-muted small">¿Qué comidas y porciones consume habitualmente?</p>
                            <div class="row g-3">
                                <div class="col-md-12"><label class="form-label">Desayuno</label><textarea class="form-control" name="rec_desayuno" rows="2"><?= getVal($datosHistoria, 'rec_desayuno') ?></textarea></div>
                                <div class="col-md-12"><label class="form-label">Almuerzo</label><textarea class="form-control" name="rec_almuerzo" rows="2"><?= getVal($datosHistoria, 'rec_almuerzo') ?></textarea></div>
                                <div class="col-md-12"><label class="form-label">Merienda</label><textarea class="form-control" name="rec_merienda" rows="2"><?= getVal($datosHistoria, 'rec_merienda') ?></textarea></div>
                                <div class="col-md-12"><label class="form-label">Cena</label><textarea class="form-control" name="rec_cena" rows="2"><?= getVal($datosHistoria, 'rec_cena') ?></textarea></div>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('habitos', $camposCustom ?? []) ?>
                        </div>
                    </div>

                    <!-- TAB 5: FRECUENCIA CONSUMO -->
                    <div class="tab-pane fade" id="tab-frecuencia">
                        <div class="form-section">
                            <h4 class="section-title">Cuestionario de Frecuencia de Consumo</h4>
                            <p class="text-muted">Escala: Siempre / A veces / Nunca</p>
                            
                            <?php 
                            $alimentos = [
                                'frec_leche_entera' => 'Leche entera', 'frec_leche_desc' => 'Leche descremada', 'frec_yogur' => 'Yogur', 
                                'frec_queso_untable' => 'Queso untable', 'frec_queso_cremoso' => 'Queso cremoso', 'frec_huevos' => 'Huevos', 
                                'frec_carne_vaca' => 'Carne de vaca', 'frec_pollo' => 'Pollo', 'frec_pescado' => 'Pescado', 
                                'frec_mariscos' => 'Mariscos', 'frec_frutos_secos' => 'Frutos secos', 'frec_legumbres' => 'Legumbres', 
                                'frec_pan_blanco' => 'Pan blanco', 'frec_pan_integral' => 'Pan integral', 
                                'frec_arroz' => 'Arroz', 'frec_fideos' => 'Fideos', 'frec_frutas' => 'Frutas', 
                                'frec_verduras_crudas' => 'Verduras crudas', 'frec_verduras_cocidas' => 'Verduras cocidas',
                                'frec_aceite' => 'Aceite', 'frec_manteca' => 'Manteca', 'frec_margarina' => 'Margarina', 
                                'frec_mani' => 'Mantequilla de maní', 'frec_palta' => 'Palta'
                            ];
                            ?>
                            
                            <div class="row g-3">
                                <?php foreach($alimentos as $key => $label): ?>
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label"><?= $label ?></label>
                                    <select class="form-select" name="<?= $key ?>">
                                        <option value="" <?= getVal($datosHistoria, $key)==''?'selected':'' ?>>- Seleccionar -</option>
                                        <option value="Siempre" <?= getVal($datosHistoria, $key)=='Siempre'?'selected':'' ?>>Siempre</option>
                                        <option value="A veces" <?= getVal($datosHistoria, $key)=='A veces'?'selected':'' ?>>A veces</option>
                                        <option value="Nunca" <?= getVal($datosHistoria, $key)=='Nunca'?'selected':'' ?>>Nunca</option>
                                    </select>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('frecuencia', $camposCustom ?? []) ?>
                        </div>
                    </div>

                    <!-- TAB 6: SEGUIMIENTO Y MONITOREO -->
                    <div class="tab-pane fade" id="tab-seguimiento">
                        <div class="form-section">
                            <h4 class="section-title">Monitoreo y Seguimiento</h4>
                            <p class="text-muted">Añade anotaciones libres de cada consulta o evolución del paciente (Módulo Opcional).</p>
                            
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label text-success fw-bold"><i class="fa-solid fa-calendar-day me-2"></i> Próxima Consulta (Agenda tentativa)</label>
                                    <input type="date" class="form-control" style="max-width: 250px;" name="proxima_consulta" value="<?= getVal($datosHistoria, 'proxima_consulta') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Evolución Antropométrica y de Peso</label>
                                    <textarea class="form-control" name="seg_antropometria" rows="4" placeholder="Ej. El paciente redujo 2cm de cintura y bajó 1.5kg..."><?= getVal($datosHistoria, 'seg_antropometria') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Adherencia al Plan Alimentario</label>
                                    <textarea class="form-control" name="seg_adherencia" rows="4" placeholder="Ej. Presenta dificultades con el desayuno por falta de tiempo..."><?= getVal($datosHistoria, 'seg_adherencia') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Modificaciones al Plan / Estrategias a futuro</label>
                                    <textarea class="form-control" name="seg_modificaciones" rows="4" placeholder="Ej. Se ajustaron los macronutrientes, incorporar más fibra..."><?= getVal($datosHistoria, 'seg_modificaciones') ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Escala de Heces de Bristol (Tránsito Intestinal)</label>
                                    <select class="form-select" name="seg_escala_bristol">
                                        <option value="" <?= getVal($datosHistoria, 'seg_escala_bristol') == '' ? 'selected' : '' ?>>- Seleccionar -</option>
                                        <option value="Tipo 1" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 1' ? 'selected' : '' ?>>Tipo 1: Trozos duros separados, como nueces (difícil de evacuar)</option>
                                        <option value="Tipo 2" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 2' ? 'selected' : '' ?>>Tipo 2: Forma de salchicha, pero grumosa</option>
                                        <option value="Tipo 3" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 3' ? 'selected' : '' ?>>Tipo 3: Como una salchicha pero con grietas en la superficie</option>
                                        <option value="Tipo 4" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 4' ? 'selected' : '' ?>>Tipo 4: Como una salchicha o serpiente, lisa y blanda (Ideal)</option>
                                        <option value="Tipo 5" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 5' ? 'selected' : '' ?>>Tipo 5: Bolas blandas con bordes definidos (fáciles de evacuar)</option>
                                        <option value="Tipo 6" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 6' ? 'selected' : '' ?>>Tipo 6: Pedazos esponjosos con bordes irregulares, heces pastosas</option>
                                        <option value="Tipo 7" <?= getVal($datosHistoria, 'seg_escala_bristol') == 'Tipo 7' ? 'selected' : '' ?>>Tipo 7: Acuosa, sin pedazos sólidos (totalmente líquida)</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Observaciones Generales</label>
                                    <textarea class="form-control" name="seg_observaciones" rows="4" placeholder="Anotaciones libres, dudas del paciente, laboratorios pendientes..."><?= getVal($datosHistoria, 'seg_observaciones') ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Campos Personalizados para esta sección -->
                            <?= renderCustomFields('seguimiento', $camposCustom ?? []) ?>
                        </div>
                    </div>

                </div> <!-- End Tab Content -->

                <!-- Submit Button -->
                <div class="text-end mb-5">
                    <button type="submit" class="btn btn-success btn-lg shadow"><i class="fa-solid fa-save me-2"></i> Guardar Ficha Médica</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/antropometria_reactiva.js"></script>
    <script src="public/js/nfpe_alertas.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar agregar campos dinámicos
            const botonesAdd = document.querySelectorAll('.btn-add-custom');
            botonesAdd.forEach(btn => {
                btn.addEventListener('click', function() {
                    const seccion = this.getAttribute('data-seccion');
                    const containerId = 'custom-fields-' + seccion;
                    const container = document.getElementById(containerId);
                    
                    // Generar un índice único basado en el tiempo
                    const index = new Date().getTime();
                    
                    const row = document.createElement('div');
                    row.className = 'custom-field-row row g-3 mt-2 border rounded p-3 bg-light position-relative';
                    row.innerHTML = `
                        <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 rounded-circle" style="width: 32px; height: 32px; right: 10px;" onclick="this.closest('.custom-field-row').remove()" title="Eliminar campo">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <div class="col-md-12 pe-5">
                            <input type="text" class="form-control fw-bold mb-2" name="campos_custom[${seccion}][${index}][titulo]" placeholder="Título (ej: Alergias, Observaciones, etc.)">
                            <textarea class="form-control" name="campos_custom[${seccion}][${index}][contenido]" rows="2" placeholder="Escribe aquí el contenido libre..."></textarea>
                        </div>
                    `;
                    container.appendChild(row);
                });
            });
        });
    </script>
</body>
</html>
