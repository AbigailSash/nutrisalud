<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fórmula Desarrollada & Balance Nutricional SARA 2 - NutriSalud</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($_SESSION['ColorTema'] ?? '#2ecc71') ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
        }

        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #334155;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 280px;
            padding: 1.8rem;
            transition: all 0.3s ease;
        }

        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            margin-bottom: 1.25rem;
            overflow: visible;
        }

        .card-header-custom {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Tabla de fórmula con scroll horizontal limpio y fijación de columnas */
        .table-formula-container {
            overflow-x: auto;
            max-height: 620px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .table-formula {
            font-size: 0.83rem;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table-formula thead th {
            position: sticky;
            top: 0;
            background: #1e293b;
            color: #ffffff;
            font-weight: 600;
            padding: 7px 9px;
            text-align: center;
            vertical-align: middle;
            z-index: 10;
            border-bottom: 2px solid var(--primary-green);
        }

        .table-formula thead tr.group-headers th {
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 7px;
        }

        .th-cat-macro { background: #0f172a !important; }
        .th-cat-lipid { background: #1e1e38 !important; border-bottom: 2px solid #818cf8 !important; }
        .th-cat-carb { background: #2d1d0e !important; border-bottom: 2px solid #fb923c !important; }
        .th-cat-micro { background: #062c20 !important; border-bottom: 2px solid #34d399 !important; }

        .table-formula th.th-main {
            background: #0f172a;
        }

        .table-formula th small {
            display: block;
            font-size: 0.68rem;
            color: #94a3b8;
            font-weight: normal;
        }

        .table-formula tbody td {
            padding: 6px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table-formula tbody tr:hover td {
            background-color: #f8fafc;
        }

        .sticky-col-1 {
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
            box-shadow: 2px 0 6px rgba(0,0,0,0.04);
        }

        .table-formula tbody tr:hover .sticky-col-1 {
            background-color: #f8fafc;
        }

        .table-formula tfoot td {
            position: sticky;
            bottom: 0;
            background: #f1f5f9;
            font-weight: 700;
            border-top: 2px solid #cbd5e1;
            padding: 9px 8px;
            z-index: 9;
        }

        /* Tarjetas del Sidebar Clínico */
        .sidebar-card-clinical {
            border-radius: 14px;
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            padding: 1.1rem;
            margin-bottom: 1.1rem;
        }

        .indicator-mini-card {
            border-radius: 10px;
            padding: 0.75rem 0.9rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .indicator-title {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            font-weight: 700;
        }

        .indicator-val {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Buscador predictivo */
        .search-results-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: 360px;
            overflow-y: auto;
            z-index: 1050;
        }

        .dropdown-item-alimento:hover {
            background-color: color-mix(in srgb, var(--primary-green) 12%, white);
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-gradient:hover {
            opacity: 0.95;
            color: white;
            transform: translateY(-1px);
        }

        .sara-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 5px;
        }

        .view-mode-btn.active {
            background: var(--primary-green) !important;
            color: white !important;
            border-color: var(--primary-green) !important;
        }

        .badge-momento {
            font-size: 0.72rem;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .badge-desayuno { background: #fef3c7; color: #92400e; }
        .badge-mediamanana { background: #f3e8ff; color: #6b21a8; }
        .badge-almuerzo { background: #dcfce7; color: #166534; }
        .badge-merienda { background: #ffedd5; color: #9a3412; }
        .badge-cena { background: #e0e7ff; color: #3730a3; }
        .badge-colacion { background: #f1f5f9; color: #475569; }

        .chart-container-box {
            position: relative;
            height: 220px;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Sidebar Principal -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- Contenido Principal -->
    <div class="main-content">
        
        <!-- Header Superior -->
        <?php include 'views/layout/header.php'; ?>

        <!-- Encabezado del Módulo -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="index.php?action=dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">Fórmula Desarrollada SARA 2</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0 fs-3">
                        <i class="fa-solid fa-flask-vial text-success me-2"></i> Fórmula Desarrollada & Balance Nutricional
                    </h2>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                        <i class="fa-solid fa-certificate me-1"></i> Norma SARA 2 / ENNyS 2
                    </span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <?php if (!empty($formulaActual['id'])): ?>
                    <a href="index.php?action=imprimir_formula&id=<?= (int)$formulaActual['id'] ?>" target="_blank" id="btnImprimirFormula" class="btn btn-outline-secondary rounded-pill px-3">
                        <i class="fa-solid fa-print me-1"></i> Imprimir Guía A4
                    </a>
                <?php else: ?>
                    <a href="javascript:void(0)" id="btnImprimirFormula" class="btn btn-outline-secondary rounded-pill px-3 d-none" target="_blank">
                        <i class="fa-solid fa-print me-1"></i> Imprimir Guía A4
                    </a>
                <?php endif; ?>

                <button type="button" id="btnLimpiarPlanilla" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="fa-solid fa-broom me-1"></i> Limpiar
                </button>
                <button type="button" id="btnGuardarFormula" class="btn btn-gradient rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Fórmula
                </button>
            </div>
        </div>

        <!-- Fila de Configuración y Selección de Paciente -->
        <div class="card card-custom p-3">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-user me-1"></i> Seleccionar Paciente <span class="text-danger">*</span></label>
                    <select id="selectPaciente" class="form-select shadow-sm form-select-sm">
                        <option value="">-- Elige un paciente --</option>
                        <?php foreach ($pacientes as $pac): ?>
                            <option value="<?= $pac['IdPaciente'] ?>" <?= ($idPacienteSeleccionado == $pac['IdPaciente']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pac['Apellido'] . ', ' . $pac['Nombre']) ?> (DNI: <?= htmlspecialchars($pac['DNI']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-tag me-1"></i> Nombre del Plan / Prescripción</label>
                    <input type="text" id="inputNombreFormula" class="form-control form-control-sm shadow-sm" value="<?= htmlspecialchars($formulaActual['nombre_formula'] ?? 'Plan Nutricional SARA 2') ?>" placeholder="Ej: Plan Hipocalórico 1800 kcal">
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-bullseye me-1 text-danger"></i> Kcal Objetivo (Meta VCT)</label>
                    <div class="input-group input-group-sm shadow-sm">
                        <input type="number" step="10" min="500" max="8000" id="inputKcalObjetivo" class="form-control fw-bold text-end" value="<?= !empty($formulaActual['kcal_objetivo']) ? htmlspecialchars((string)(float)$formulaActual['kcal_objetivo']) : '2000' ?>" placeholder="2000">
                        <span class="input-group-text bg-light">kcal</span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-clock-rotate-left me-1"></i> Historial Guardado</label>
                    <select id="selectHistorial" class="form-select form-select-sm shadow-sm" <?= empty($idPacienteSeleccionado) ? 'disabled' : '' ?>>
                        <option value="">-- Cargar fórmula previa --</option>
                    </select>
                </div>
            </div>

            <!-- Ficha Rápida del Paciente si está seleccionado -->
            <?php if (!empty($pacienteActual)): ?>
                <div class="mt-2 p-2 bg-light rounded-3 d-flex flex-wrap gap-3 align-items-center justify-content-between border">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle p-1 px-2 bg-success bg-opacity-10 text-success fw-bold">
                            <i class="fa-solid fa-hospital-user"></i>
                        </div>
                        <div>
                            <span class="fw-bold text-dark small"><?= htmlspecialchars($pacienteActual['Nombre'] . ' ' . $pacienteActual['Apellido']) ?></span>
                            <span class="text-muted small ms-2">DNI: <?= htmlspecialchars($pacienteActual['DNI']) ?> | Obra Social: <?= htmlspecialchars($pacienteActual['Obra_Social'] ?? 'Particular') ?></span>
                        </div>
                    </div>
                    <div class="d-flex gap-3 text-center">
                        <div class="px-2">
                            <small class="text-muted d-block" style="font-size:0.7rem;">PESO REAL</small>
                            <span class="fw-bold text-dark small" id="lblPacientePesoVal"><?= !empty($pacienteActual['Peso']) ? $pacienteActual['Peso'] . ' kg' : 'N/D' ?></span>
                        </div>
                        <div class="px-2 border-start">
                            <small class="text-muted d-block" style="font-size:0.7rem;">TALLA</small>
                            <span class="fw-bold text-dark small"><?= !empty($pacienteActual['Estatura']) ? $pacienteActual['Estatura'] . ' cm' : 'N/D' ?></span>
                        </div>
                        <div class="px-2 border-start">
                            <small class="text-muted d-block" style="font-size:0.7rem;">SEXO</small>
                            <span class="fw-bold text-dark small"><?= ($pacienteActual['Sexo'] ?? 'M') === 'F' ? 'Femenino' : 'Masculino' ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- LAYOUT 70% PLANILLA / 30% SIDEBAR CLÍNICO -->
        <div class="row g-3">
            
            <!-- SECCIÓN IZQUIERDA: 70% PLANILLA MATRICIAL -->
            <div class="col-xl-8 col-lg-7">
                
                <!-- Toolbar de Búsqueda, Momento del Día y Grupos SARA 2 -->
                <div class="card card-custom p-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-clock me-1"></i> Momento al Agregar</label>
                            <select id="selectMomentoDia" class="form-select form-select-sm shadow-sm">
                                <option value="Desayuno">🌅 Desayuno</option>
                                <option value="Media Mañana">☕ Media Mañana</option>
                                <option value="Almuerzo" selected>🍽️ Almuerzo</option>
                                <option value="Merienda">🍵 Merienda</option>
                                <option value="Cena">🌙 Cena</option>
                                <option value="Colación">🍎 Colación</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-layer-group me-1"></i> Grupo SARA 2</label>
                            <select id="selectGrupoSara2" class="form-select form-select-sm shadow-sm">
                                <option value="">-- Todos los 26 Grupos SARA 2 --</option>
                                <?php foreach ($grupos as $grp): ?>
                                    <option value="<?= (int)$grp['grupo_id'] ?>">
                                        <?= (int)$grp['grupo_id'] ?>. <?= htmlspecialchars($grp['grupo_nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 position-relative">
                            <label class="form-label fw-bold text-muted small mb-1"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar Alimento</label>
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-white border-end-0 text-success"><i class="fa-solid fa-apple-whole"></i></span>
                                <input type="text" id="buscarAlimentoInput" class="form-control border-start-0 ps-0" placeholder="Escribe para buscar (Ej: Acelga, Pechuga, Avena)..." autocomplete="off">
                            </div>
                            <!-- Dropdown de resultados -->
                            <div id="resultadosBusqueda" class="search-results-dropdown d-none"></div>
                        </div>
                    </div>
                </div>

                <!-- Planilla Interactiva de Fórmula Desarrollada SARA 2 -->
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-table-list text-success me-2"></i> Planilla de Composición Química SARA 2</h6>
                            <small class="text-muted">Cálculo centesimal por porción comestible editable en tiempo real.</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary view-mode-btn active" id="btnVistaClinica" data-mode="clinica">
                                    <i class="fa-solid fa-stethoscope me-1"></i> Vista Clínica
                                </button>
                                <button type="button" class="btn btn-outline-secondary view-mode-btn" id="btnVistaCompleta" data-mode="completa">
                                    <i class="fa-solid fa-table-columns me-1"></i> SARA 2 Completa (39 Comp.)
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-0">
                        <div class="table-formula-container">
                            <table class="table table-formula table-bordered table-hover align-middle" id="tablaFormulaSARA2">
                                <thead>
                                    <tr class="group-headers">
                                        <th class="th-main sticky-col-1" rowspan="2" style="min-width: 200px;">Alimento & Porción Casera</th>
                                        <th class="th-main" rowspan="2" style="min-width: 110px;">Momento</th>
                                        <th class="th-main" rowspan="2" style="min-width: 90px;">Cantidad <small>Gramos (g)</small></th>
                                        <th class="th-main" rowspan="2" style="min-width: 85px; color:#4ade80;">Energía <small>Atwater (kcal)</small></th>
                                        
                                        <!-- Macronutrientes y Generales -->
                                        <th colspan="4" class="th-cat-macro">Macronutrientes</th>
                                        
                                        <!-- Perfil Lipídico Detallado (Completo SARA 2) -->
                                        <th colspan="9" class="th-cat-lipid col-completa-header d-none">Perfil Lipídico Detallado (g / mg)</th>
                                        
                                        <!-- Carbohidratos, Azúcares y Alcohol -->
                                        <th colspan="6" class="th-cat-carb col-completa-header d-none">Carbohidratos, Azúcares y Alcohol (g)</th>
                                        
                                        <!-- Minerales y Cenizas -->
                                        <th colspan="8" class="th-cat-micro col-completa-header d-none">Minerales (mg)</th>
                                        
                                        <!-- Vitaminas -->
                                        <th colspan="10" class="th-cat-micro col-completa-header d-none">Vitaminas (mg / µg)</th>

                                        <!-- Columnas resumidas de la vista clínica -->
                                        <th colspan="8" class="th-cat-micro col-clinica-header">Micronutrientes & Fibras Clave</th>

                                        <th class="th-main text-center" rowspan="2" style="width: 45px;"><i class="fa-solid fa-gear"></i></th>
                                    </tr>
                                    <tr>
                                        <!-- Sub-headers Vista Clínica y Completa -->
                                        <th style="min-width: 70px;" class="text-end">Agua <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end">Prot. <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end">Lípidos <small>g</small></th>
                                        <th style="min-width: 75px;" class="text-end">HC Disp. <small>g</small></th>

                                        <!-- Detalle Lipídico Completo -->
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Colest. <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">AG Sat <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">AG Mono <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">AG Poli <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">AG Trans <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Linoleico <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">ALA <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Araquid. <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">EPA+DHA <small>g</small></th>

                                        <!-- Carbohidratos Completo -->
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">HC Tot <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Azúcar Tot <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Azúcar Agr <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Fibra <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Alcohol <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Cenizas <small>g</small></th>

                                        <!-- Minerales Completo -->
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Sodio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Potasio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Calcio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Fósforo <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Hierro <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Magnesio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Zinc <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Cobre <small>mg</small></th>

                                        <!-- Vitaminas Completo -->
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Niacina <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Folato <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Ác. Fólico <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit A <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Retinol <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit B1 <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit B2 <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit B12 <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit C <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-completa d-none">Vit D <small>µg</small></th>

                                        <!-- Columnas Vista Clínica -->
                                        <th style="min-width: 70px;" class="text-end col-clinica">Fibra <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Hierro <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Calcio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Sodio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Potasio <small>mg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">AG Sat <small>g</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Vit A <small>µg</small></th>
                                        <th style="min-width: 70px;" class="text-end col-clinica">Vit C <small>mg</small></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaFormulaCuerpo">
                                    <!-- Filas generadas reactivamente por JS -->
                                </tbody>
                                <tfoot id="filaTotales">
                                    <!-- Fila de totales generada reactivamente por JS -->
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Observaciones y Pautas Dietoterápicas -->
                <div class="card card-custom p-3">
                    <h6 class="fw-bold text-dark mb-2"><i class="fa-regular fa-comment-dots text-success me-1"></i> Observaciones y Pautas Dietoterápicas</h6>
                    <textarea id="txtObservacionesFormula" class="form-control" rows="2" placeholder="Notas clínicas, justificaciones fisiopatológicas, momentos de ingesta..."><?= htmlspecialchars($formulaActual['observaciones'] ?? '') ?></textarea>
                </div>

            </div>

            <!-- SECCIÓN DERECHA: 30% SIDEBAR CLÍNICO & ANALÍTICA VISUAL (CHART.JS) -->
            <div class="col-xl-4 col-lg-5">
                
                <!-- 1. Meta Calórica & Cumplimiento VCT -->
                <div class="sidebar-card-clinical">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="indicator-title"><i class="fa-solid fa-bullseye me-1 text-danger"></i> Meta Calórica vs Calculada</span>
                        <span class="badge bg-light text-dark border" id="badgePorcMeta">0%</span>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <div>
                            <span class="fs-4 fw-bold text-success" id="sidebarKcalCalculadas">0.0</span>
                            <span class="text-muted small"> / <span id="sidebarKcalObjetivo">2000</span> kcal</span>
                        </div>
                        <div class="small fw-semibold" id="sidebarDeltaKcal">
                            Δ 0 kcal
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 8px;">
                        <div id="progressBarKcal" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                    </div>
                </div>

                <!-- 2. Proteínas por kg de peso real -->
                <div class="sidebar-card-clinical">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="indicator-title"><i class="fa-solid fa-dumbbell me-1 text-primary"></i> Proteína por kg de Peso</span>
                        <span class="badge bg-secondary" id="badgeProtKg">Sin datos</span>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <span class="fs-4 fw-bold text-dark" id="sidebarProtKgVal">--</span>
                        <span class="text-muted small">g Prot / kg peso</span>
                    </div>
                </div>

                <!-- 3. Gráfico Doughnut: Distribución de Macronutrientes (Chart.js) -->
                <div class="sidebar-card-clinical">
                    <span class="indicator-title d-block mb-2"><i class="fa-solid fa-chart-pie me-1 text-success"></i> Distribución de Macronutrientes (% VCT)</span>
                    <div class="chart-container-box">
                        <canvas id="chartMacronutrientes"></canvas>
                    </div>
                    <div class="d-flex justify-content-around text-center mt-2 small border-top pt-2">
                        <div><span class="d-block text-primary fw-bold" id="lblPctHC">0%</span><span class="text-muted" style="font-size:0.7rem;">Carbohidratos</span></div>
                        <div><span class="d-block text-danger fw-bold" id="lblPctProt">0%</span><span class="text-muted" style="font-size:0.7rem;">Proteínas</span></div>
                        <div><span class="d-block text-warning fw-bold" id="lblPctGrasas">0%</span><span class="text-muted" style="font-size:0.7rem;">Grasas</span></div>
                    </div>
                </div>

                <!-- 4. Gráfico Radar: Micronutrientes vs. IDR (Chart.js) -->
                <div class="sidebar-card-clinical">
                    <span class="indicator-title d-block mb-2"><i class="fa-solid fa-shield-virus me-1 text-info"></i> Cobertura de Micronutrientes vs. IDR</span>
                    <div class="chart-container-box">
                        <canvas id="chartRadarMicros"></canvas>
                    </div>
                </div>

                <!-- 5. Semáforos de Alertas Patológicas -->
                <div class="sidebar-card-clinical">
                    <span class="indicator-title d-block mb-2"><i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> Semáforos de Alerta Patológica</span>
                    
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border" id="boxAlertaSodio">
                        <div>
                            <strong class="d-block small">Sodio Total (Hipertensión)</strong>
                            <small class="text-muted" id="txtAlertaSodio">0 mg (Límite 2000 mg)</small>
                        </div>
                        <span class="badge bg-success" id="badgeAlertaSodio">Óptimo</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2 rounded-2 mb-2 border" id="boxAlertaFibra">
                        <div>
                            <strong class="d-block small">Fibra Dietética (Salud Intestinal)</strong>
                            <small class="text-muted" id="txtAlertaFibra">0 g (Meta ≥ 25 g)</small>
                        </div>
                        <span class="badge bg-secondary" id="badgeAlertaFibra">Baja</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2 rounded-2 border" id="boxAlertaAzucar">
                        <div>
                            <strong class="d-block small">Azúcares Agregados (% Kcal)</strong>
                            <small class="text-muted" id="txtAlertaAzucar">0% (Límite OMS ≤ 10%)</small>
                        </div>
                        <span class="badge bg-success" id="badgeAlertaAzucar">Normal</span>
                    </div>
                </div>

                <!-- 6. Los 7 Indicadores Clínicos Diagnósticos SARA 2 -->
                <div class="sidebar-card-clinical">
                    <span class="indicator-title d-block mb-3"><i class="fa-solid fa-stethoscope me-1 text-success"></i> 7 Indicadores Diagnósticos SARA 2</span>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">1. Densidad Calórica</div>
                            <small class="text-muted">Total Kcal / Gramos</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-primary" id="ind_dens_cal">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_dens_cal">Sin datos</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">2. Cociente Gramo / Kcal</div>
                            <small class="text-muted">Total Gramos / Kcal</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-info" id="ind_cociente_gc">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_cociente_gc">g / kcal</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">3. % H.C. Complejos</div>
                            <small class="text-muted">V.N: ≥ 50% de HC</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-dark" id="ind_porc_hc_comp">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_hc_comp">Sin datos</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">4. Cociente Woodyatt</div>
                            <small class="text-muted">Ceto/Anticeto (0.25-0.35)</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-dark" id="ind_woodyatt">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_woodyatt">Sin datos</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">5. % Alim. Protectores</div>
                            <small class="text-muted">Kcal Prot / Kcal (>50%)</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-success" id="ind_porc_protectores">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_protectores">Sin datos</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">6. % Proteínas AVB</div>
                            <small class="text-muted">Prot AVB / Prot (>55%)</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-danger" id="ind_porc_avb">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_avb">Sin datos</span>
                        </div>
                    </div>

                    <div class="indicator-mini-card">
                        <div>
                            <div class="indicator-title">7. % Cobertura Láctea</div>
                            <small class="text-muted">Kcal Lácteos / Kcal (6-10%)</small>
                        </div>
                        <div class="text-end">
                            <div class="indicator-val text-primary" id="ind_porc_leche">--</div>
                            <span class="badge bg-secondary sara-badge" id="badge_leche">Sin datos</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Hidden Inputs con Datos del Paciente y Fórmula -->
    <input type="hidden" id="formulaIdActual" value="<?= !empty($formulaActual['id']) ? (int)$formulaActual['id'] : '' ?>">
    <input type="hidden" id="pacientePesoKg" value="<?= !empty($pacienteActual['Peso']) ? (float)$pacienteActual['Peso'] : '' ?>">

    <!-- Scripts y Datos Iniciales en JSON para JS -->
    <script>
        window.ALIMENTOS_BASE = <?= json_encode($alimentosBase, JSON_UNESCAPED_UNICODE) ?>;
        window.FORMULA_INICIAL = <?= !empty($formulaActual) ? json_encode($formulaActual, JSON_UNESCAPED_UNICODE) : 'null' ?>;
        window.PACIENTE_ACTUAL = <?= !empty($pacienteActual) ? json_encode($pacienteActual, JSON_UNESCAPED_UNICODE) : 'null' ?>;
    </script>

    <!-- Bootstrap 5 JS Bundle, SweetAlert2 & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Script de Fórmula Desarrollada SARA 2 & Chart.js Engine -->
    <script src="public/js/formula_desarrollada.js?v=<?= time() ?>"></script>

    <!-- Cargar historial del paciente si ya hay uno seleccionado -->
    <?php if (!empty($idPacienteSeleccionado)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectHist = document.getElementById('selectHistorial');
            if (selectHist) {
                fetch(`index.php?action=api_historial_formulas&id_paciente=<?= (int)$idPacienteSeleccionado ?>`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.data)) {
                            selectHist.innerHTML = '<option value="">-- Cargar fórmula previa --</option>';
                            data.data.forEach(f => {
                                const opt = document.createElement('option');
                                opt.value = f.id;
                                opt.textContent = `${f.nombre_formula} (${f.fecha_creacion.substring(0, 10)})`;
                                if (<?= (int)($formulaActual['id'] ?? 0) ?> === parseInt(f.id)) {
                                    opt.selected = true;
                                }
                                selectHist.appendChild(opt);
                            });
                        }
                    });
            }
        });
    </script>
    <?php endif; ?>

</body>
</html>
