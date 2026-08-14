<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Gestor de Plan y Educación Nutricional</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
            --text-dark: #2c3e50;
            --text-gray: #7f8c8d;
            --bg-light: #f4f7f6;
            --sidebar-bg: #1a252f;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-light); 
            color: var(--text-dark);
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        .top-bar-clean {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 15px;
        }

        .user-avatar-mini {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            min-height: 38px !important;
            max-width: 38px !important;
            max-height: 38px !important;
            border-radius: 50% !important;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
            object-fit: cover !important;
            overflow: hidden !important;
        }

        .plan-header-card { 
            background: linear-gradient(135deg, #1a252f, #2c3e50); 
            color: white; 
            padding: 1.8rem 2.2rem; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .plan-header-card::after {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: color-mix(in srgb, var(--primary-green) 20%, transparent);
            border-radius: 50%;
        }

        .nav-tabs-clinical {
            border-bottom: 2px solid #e2e8f0;
            gap: 10px;
        }
        .nav-tabs-clinical .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px 12px 0 0;
            transition: all 0.2s;
            background: transparent;
        }
        .nav-tabs-clinical .nav-link:hover {
            color: var(--dark-green);
            background-color: #f1f5f9;
        }
        .nav-tabs-clinical .nav-link.active {
            color: var(--dark-green) !important;
            background-color: white !important;
            border-bottom: 3px solid var(--primary-green) !important;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.02);
        }

        .card-clinical {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
        }

        .card-header-day { 
            background-color: #f8fafc; 
            color: #1e293b; 
            border-radius: 16px 16px 0 0 !important; 
            font-weight: 700; 
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 20px;
        }

        .momento-title { 
            color: var(--dark-green); 
            font-weight: 700; 
            border-bottom: 2px solid var(--light-green); 
            padding-bottom: 0.4rem; 
            margin-top: 1rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .food-item { 
            background: #fdfdfd; 
            border-left: 3px solid var(--primary-green); 
            padding: 10px 14px; 
            margin-bottom: 8px; 
            border-radius: 6px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            transition: background 0.2s;
        }
        .food-item:hover {
            background: #f8fafc;
        }

        /* Píldoras de plantillas clínicas */
        .template-pill {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .template-pill:hover {
            border-color: var(--primary-green);
            background: var(--light-green);
            color: var(--dark-green);
            transform: translateY(-1px);
        }

        @media (max-width: 991px) {
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Barra Superior de Navegación -->
        <div class="top-bar-clean">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="index.php?action=listar_planes" class="text-decoration-none fw-semibold" style="color: var(--dark-green);"><i class="fa-solid fa-apple-whole me-1" style="color: var(--primary-green);"></i> Planes Alimentarios</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Diseñar Menú & Educación</li>
                    </ol>
                </nav>
                <h3 class="fw-bold mb-0 text-dark">Diseñador Integral del Plan</h3>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold text-dark small"><?= htmlspecialchars($_SESSION['NombreNutri'] ?? 'Usuario') ?> <?= htmlspecialchars($_SESSION['ApellidoNutri'] ?? '') ?></div>
                    <small class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($_SESSION['EspecialidadNutri'] ?? 'Nutricionista') ?></small>
                </div>
                <?php if(!empty($_SESSION['LogoNutri'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['LogoNutri']) ?>?v=<?= time() ?>" class="user-avatar-mini" alt="Avatar">
                <?php else: ?>
                    <div class="user-avatar-mini"><?= strtoupper(substr($_SESSION['NombreNutri'] ?? 'U', 0, 1) . substr($_SESSION['ApellidoNutri'] ?? '', 0, 1)) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alertas Flash -->
        <?php include 'views/layout/alertas.php'; ?>

        <!-- Encabezado del Plan y Paciente -->
        <div class="plan-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge text-white px-3 py-1 rounded-pill mb-2" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <i class="fa-solid fa-file-waveform me-1"></i> Plan Alimentario & Educación Clínica
                </span>
                <h2 class="fw-bold mb-1 text-white"><?= htmlspecialchars($plan['Nombre_Plan']) ?></h2>
                <p class="mb-0 text-white-50 fs-6">
                    <i class="fa-solid fa-user me-2" style="color: var(--primary-green);"></i> Paciente: <strong class="text-white"><?= htmlspecialchars($plan['Nombre'] . ' ' . $plan['Apellido']) ?></strong>
                    <span class="mx-2">•</span>
                    <i class="fa-solid fa-bullseye me-1 text-warning"></i> Objetivo: <?= htmlspecialchars($plan['Objetivo'] ?: 'No especificado') ?>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="index.php?action=imprimir_plan&id_plan=<?= $plan['IdPlan'] ?>" target="_blank" class="btn rounded-pill px-4 fw-semibold shadow-sm text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <i class="fa-solid fa-print me-2"></i> Ver / Imprimir Guía (PDF)
                </a>
                <a href="index.php?action=listar_planes" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver a Planes
                </a>
            </div>
        </div>

        <!-- Pestañas Clínicas Unificadas -->
        <ul class="nav nav-tabs nav-tabs-clinical mb-4" id="planTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-menu-btn" data-bs-toggle="tab" data-bs-target="#tab-menu" type="button" role="tab">
                    <i class="fa-solid fa-utensils me-2"></i> 1. Menú y Distribución Semanal
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-educacion-btn" data-bs-toggle="tab" data-bs-target="#tab-educacion" type="button" role="tab">
                    <i class="fa-solid fa-book-open-reader me-2"></i> 2. Educación Nutricional & Pautas de Hábitos
                </button>
            </li>
        </ul>

        <div class="tab-content" id="planTabsContent">
            <!-- ========================================================= -->
            <!-- PESTAÑA 1: DISEÑO DEL MENÚ Y COMIDAS                      -->
            <!-- ========================================================= -->
            <div class="tab-pane fade show active" id="tab-menu" role="tabpanel">
                
                <!-- Formulario Agregar Alimento / Comida -->
                <div class="card card-clinical border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="color: var(--dark-green);">
                            <i class="fa-solid fa-circle-plus me-2" style="color: var(--primary-green);"></i> Prescribir Alimento o Preparación
                        </h5>
                        <form action="index.php?action=agregar_detalle_plan" method="POST">
                            <input type="hidden" name="id_plan" value="<?= $plan['IdPlan'] ?>">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-muted">Día de la semana</label>
                                    <select name="id_dia" class="form-select" required>
                                        <?php foreach($dias as $d): ?>
                                            <option value="<?= $d['IdDia'] ?>"><?= htmlspecialchars($d['Nombre_Dia']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-muted">Momento del día</label>
                                    <select name="id_momento" class="form-select" required>
                                        <?php foreach($momentos as $m): ?>
                                            <option value="<?= $m['IdMomento'] ?>"><?= htmlspecialchars($m['Nombre_Momento']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 position-relative">
                                    <label class="form-label fw-semibold small text-muted">Alimento / Preparación</label>
                                    <input type="text" name="alimento" id="search-alimento" class="form-control" placeholder="Ej: Tostadas con huevo y palta" required autocomplete="off">
                                    <div id="sugerencias-alimentos" class="list-group position-absolute w-100 d-none shadow mt-1" style="z-index: 1050; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small text-muted">Porción / Cantidad</label>
                                    <input type="text" name="cantidad" class="form-control" placeholder="Ej: 2 unid. o 150g" required>
                                </div>
                            </div>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-10">
                                    <label class="form-label fw-semibold small text-muted">Indicaciones culinarias / Reemplazos (Opcional)</label>
                                    <input type="text" name="indicaciones" class="form-control" placeholder="Ej: Condimentar con orégano y oliva en crudo. Se puede reemplazar por 1 fruta de estación.">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn w-100 fw-semibold text-white shadow-sm" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                                        <i class="fa-solid fa-plus me-1"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Grilla de la Semana Diseñada -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-week me-2" style="color: var(--primary-green);"></i> Distribución del Menú Semanal</h5>
                    <span class="text-muted small">Total: <strong><?= count($detalles) ?></strong> comidas programadas</span>
                </div>

                <?php if(empty($planAgrupado)): ?>
                    <div class="card card-clinical p-5 text-center">
                        <i class="fa-solid fa-plate-wheat text-muted mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                        <h5 class="text-muted fw-bold">El menú aún está vacío</h5>
                        <p class="text-muted mb-0">Usa el formulario superior para añadir las comidas de cada momento del día.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card card-clinical h-100">
                                    <div class="card-header-day d-flex justify-content-between align-items-center">
                                        <span><i class="fa-regular fa-calendar-check me-2" style="color: var(--primary-green);"></i> <?= htmlspecialchars($nombreDia) ?></span>
                                    </div>
                                    <div class="card-body p-3">
                                        <?php foreach($momentosAgrupados as $nombreMomento => $listaAlimentos): ?>
                                            <div class="momento-title">
                                                <i class="fa-regular fa-clock me-2" style="color: var(--primary-green);"></i> <?= htmlspecialchars($nombreMomento) ?>
                                            </div>
                                            <?php foreach($listaAlimentos as $item): ?>
                                                <div class="food-item">
                                                    <div>
                                                        <strong><?= htmlspecialchars($item['Alimento']) ?></strong>
                                                        <?php if(!empty($item['Cantidad'])): ?>
                                                             <span class="badge bg-light text-dark border ms-2"><?= htmlspecialchars($item['Cantidad']) ?></span>
                                                        <?php endif; ?>
                                                        <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                                            <br><small class="text-muted fst-italic">"<?= htmlspecialchars($item['Indicaciones_Especiales']) ?>"</small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <a href="index.php?action=eliminar_detalle_plan&id_detalle=<?= $item['IdDetalle'] ?>&id_plan=<?= $plan['IdPlan'] ?>" class="btn btn-sm btn-outline-danger ms-2 rounded-circle" style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;" data-confirm="true" data-mensaje="¿Eliminar esta indicación?">
                                                        <i class="fa-solid fa-times"></i>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- ========================================================= -->
            <!-- PESTAÑA 2: EDUCACIÓN NUTRICIONAL & PAUTAS DE HÁBITOS      -->
            <!-- ========================================================= -->
            <div class="tab-pane fade" id="tab-educacion" role="tabpanel">
                <div class="card card-clinical border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="fa-solid fa-graduation-cap me-2" style="color: var(--primary-green);"></i> Pautas de Educación Nutricional e Informe Clínico
                                </h5>
                                <p class="text-muted small mb-0">Estas recomendaciones acompañarán al menú en el portal del paciente y en el PDF descargable.</p>
                            </div>
                        </div>

                        <!-- Plantillas rápidas de redacción clínica -->
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold small text-muted mb-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-warning me-1"></i> Pautas Clínicas Rápidas (Haz clic para insertar en el informe):
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="template-pill" onclick="insertarPauta('hidratacion')">
                                    <i class="fa-solid fa-droplet text-info"></i> Hidratación Óptima
                                </span>
                                <span class="template-pill" onclick="insertarPauta('coccion')">
                                    <i class="fa-solid fa-fire-burner text-danger"></i> Métodos de Cocción
                                </span>
                                <span class="template-pill" onclick="insertarPauta('saciedad')">
                                    <i class="fa-solid fa-apple-whole" style="color: var(--primary-green);"></i> Saciedad y Masticación
                                </span>
                                <span class="template-pill" onclick="insertarPauta('etiquetas')">
                                    <i class="fa-solid fa-tag text-primary"></i> Lectura de Etiquetas
                                </span>
                                <span class="template-pill" onclick="insertarPauta('plato')">
                                    <i class="fa-solid fa-circle-notch text-warning"></i> Método del Plato
                                </span>
                                <span class="template-pill" onclick="insertarPauta('sueno')">
                                    <i class="fa-solid fa-moon text-secondary"></i> Descanso y Crononutrición
                                </span>
                            </div>
                        </div>

                        <form action="index.php?action=guardar_recomendaciones_plan" method="POST">
                            <input type="hidden" name="id_plan" value="<?= $plan['IdPlan'] ?>">
                            <div class="mb-4">
                                <textarea id="textareaRecomendaciones" class="form-control" name="recomendaciones" rows="12" placeholder="Redacta o inserta aquí las pautas de educación nutricional para tu paciente..." style="border-radius: 12px; font-size: 0.95rem; line-height: 1.6;"><?= htmlspecialchars($plan['Recomendaciones'] ?? '') ?></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="text-muted small"><i class="fa-solid fa-circle-check me-1" style="color: var(--primary-green);"></i> El paciente podrá acceder a estas pautas desde su portal o descargarlas en PDF.</span>
                                <button type="submit" class="btn rounded-pill px-4 py-2 fw-bold text-white shadow-sm" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                                    <i class="fa-solid fa-save me-2"></i> Guardar Educación Nutricional
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Plantillas clínicas para inserción rápida
        const templates = {
            hidratacion: "\n💧 PAUTA DE HIDRATACIÓN:\n- Consumir entre 2 y 2.5 litros de agua potable al día.\n- Evitar jugos comerciales, gaseosas comunes y bebidas alcohólicas.\n- Puedes sumar infusiones sin azúcar agregada (té verde, mate, café filtrado).\n",
            coccion: "\n🍳 TÉCNICAS CULINARIAS SALUDABLES:\n- Priorizar métodos de cocción al vapor, hervido, plancha, horno o parrilla.\n- Evitar frituras y salteados con exceso de grasas trans.\n- Utilizar aceite de oliva o de girasol alto oleico preferentemente en crudo (al final de la preparación).\n- Condimentar con hierbas naturales (orégano, cúrcuma, provenzal, limón) para reducir el agregado de sal.\n",
            saciedad: "\n🍽️ MANEJO DE SACIEDAD Y CONDUCTA:\n- Comer despacio, masticando bien cada bocado (mínimo 20 minutos por comida principal).\n- Identificar hambre real vs. hambre emocional. Si aparece ansiedad entre horas, beber un vaso de agua o elegir bastones de zanahoria, pepino o una infusión tibia.\n- No saltear comidas para evitar llegar con descontrol a la siguiente ingesta.\n",
            etiquetas: "\n🏷️ LECTURA DE ETIQUETAS Y COMPRAS INTELIGENTES:\n- Revisar la lista de ingredientes: que los primeros sean alimentos reales y no azúcares ocultos (jarabes, maltodextrina).\n- Optar por productos con menos de 5 ingredientes y reducidos en sellos negros de advertencia nutricional.\n",
            plato: "\n🥗 DISTRIBUCIÓN DEL MÉTODO DEL PLATO:\n- 50% del plato: Vegetales variados crudos o cocidos (fibra, vitaminas y minerales).\n- 25% del plato: Proteínas de alto valor biológico (huevos, pollo, carnes magras, legumbres, tofu).\n- 25% del plato: Carbohidratos complejos o cereales integrales (arroz integral, papa con cáscara, quinoa, fideos integrales).\n",
            sueno: "\n🌙 CRONONUTRICIÓN Y DESCANSO:\n- Cenar liviano al menos 2 horas antes de dormir para mejorar la calidad del sueño.\n- Mantener horarios regulares de descanso (7 a 8 horas diarias) para optimizar las hormonas del apetito (leptina y grelina).\n"
        };

        function insertarPauta(tipo) {
            const textarea = document.getElementById('textareaRecomendaciones');
            if (textarea && templates[tipo]) {
                const textoActual = textarea.value.trim();
                textarea.value = textoActual ? (textoActual + "\n" + templates[tipo]) : templates[tipo].trim();
                textarea.focus();
                textarea.scrollTop = textarea.scrollHeight;
            }
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
    <script src="public/js/plan_alimentario.js"></script>
</body>
</html>
