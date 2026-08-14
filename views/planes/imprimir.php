<?php
if (!isset($nutri) && !empty($planActivo['IdNutri'])) {
    require_once 'models/Nutricionista.php';
    $nutriModel = new Nutricionista();
    $nutri = $nutriModel->obtenerPorId($planActivo['IdNutri']);
}
$colorTema = $nutri['Color_Tema'] ?? $_SESSION['ColorTema'] ?? '#2ecc71';
$nombreNutri = ($nutri['Nombre'] ?? $_SESSION['NombreNutri'] ?? 'Lic. en Nutrición') . ' ' . ($nutri['Apellido'] ?? $_SESSION['ApellidoNutri'] ?? '');
$especialidadNutri = $nutri['Especialidad'] ?? $_SESSION['EspecialidadNutri'] ?? 'Nutricionista Clínico';
$matriculaNutri = $nutri['Matricula'] ?? $_SESSION['MatriculaNutri'] ?? 'Nacional';
$logoNutri = $nutri['Logo_URL'] ?? $_SESSION['LogoNutri'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía Nutricional y Plan Alimentario - <?= htmlspecialchars($planActivo['Nombre'] . ' ' . $planActivo['Apellido']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 12%, white);
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: white; 
            color: #1e293b; 
            font-size: 13px;
        }

        .print-header { 
            border-bottom: 3px solid var(--primary-green); 
            margin-bottom: 1.5rem; 
            padding-bottom: 1.2rem; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .clinic-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .clinic-logo {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: bold;
            overflow: hidden;
        }
        .clinic-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .patient-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid var(--primary-green);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 1.5rem;
        }

        .card-menu-day { 
            border: 1px solid #cbd5e1; 
            border-radius: 10px;
            margin-bottom: 1.2rem; 
            page-break-inside: avoid; 
            overflow: hidden;
        }
        .card-header-day { 
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
            font-weight: 700; 
            padding: 9px 14px; 
            font-size: 0.95rem;
            color: white;
        }
        .momento-title { 
            font-weight: 700; 
            color: var(--dark-green); 
            margin-top: 8px; 
            text-transform: uppercase; 
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--light-green);
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        .food-item { 
            margin-bottom: 5px; 
            padding: 6px 10px; 
            border-left: 3px solid var(--primary-green); 
            background-color: #fafafa;
            border-radius: 4px;
        }

        .education-box {
            background-color: var(--light-green);
            border: 1px solid color-mix(in srgb, var(--primary-green) 30%, transparent);
            border-left: 5px solid var(--primary-green);
            border-radius: 10px;
            padding: 18px;
            margin-top: 2rem;
            page-break-inside: avoid;
        }

        .footer-sign {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            page-break-inside: avoid;
        }

        .btn-theme-action {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .card-menu-day { border-color: #94a3b8 !important; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Barra de herramientas (No imprimible) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print bg-light p-3 rounded-3 border">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-pdf text-danger me-2"></i> Vista de Impresión / Guardado PDF</h5>
                <small class="text-muted">El documento refleja el diseño y color institucional de tu consultorio.</small>
            </div>
            <div>
                <button onclick="window.close()" class="btn btn-outline-secondary me-2"><i class="fa-solid fa-times me-1"></i> Cerrar</button>
                <button onclick="window.print()" class="btn btn-theme-action rounded-pill px-4 shadow-sm"><i class="fa-solid fa-print me-2"></i> Imprimir / Guardar en PDF</button>
            </div>
        </div>

        <!-- Encabezado Membretado Institucional -->
        <div class="print-header">
            <div class="clinic-brand">
                <div class="clinic-logo">
                    <?php if(!empty($logoNutri)): ?>
                        <img src="<?= htmlspecialchars($logoNutri) ?>" alt="Logo">
                    <?php else: ?>
                        <i class="fa-solid fa-leaf"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="fw-bold mb-0" style="color: var(--dark-green);">NutriSalud</h4>
                    <small class="text-muted fw-semibold">Consultorio Nutricional & Salud Integral</small>
                </div>
            </div>
            <div class="text-end">
                <h6 class="fw-bold mb-0"><?= htmlspecialchars($nombreNutri) ?></h6>
                <small class="text-muted d-block"><?= htmlspecialchars($especialidadNutri) ?> • M.P. <?= htmlspecialchars($matriculaNutri) ?></small>
                <small class="text-muted">Fecha de emisión: <?= date('d/m/Y') ?></small>
            </div>
        </div>

        <!-- Caja de Datos del Paciente -->
        <div class="patient-box">
            <div class="row">
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold d-block">Paciente</span>
                    <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($planActivo['Nombre'] . ' ' . $planActivo['Apellido']) ?></h5>
                </div>
                <div class="col-3">
                    <span class="text-muted small text-uppercase fw-bold d-block">Plan Prescrito</span>
                    <span class="fw-bold" style="color: var(--dark-green);"><?= htmlspecialchars($planActivo['Nombre_Plan']) ?></span>
                </div>
                <div class="col-3 text-end">
                    <span class="text-muted small text-uppercase fw-bold d-block">Inicio</span>
                    <span class="fw-semibold text-dark"><?= date('d/m/Y', strtotime($planActivo['Fecha_Inicio'])) ?></span>
                </div>
            </div>
            <?php if(!empty($planActivo['Objetivo'])): ?>
                <div class="mt-2 pt-2 border-top">
                    <small class="text-muted fw-bold text-uppercase">Objetivo Terapéutico:</small>
                    <span class="ms-1 fw-medium"><?= htmlspecialchars($planActivo['Objetivo']) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pauta Dietética / Menú Semanal -->
        <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
            <i class="fa-solid fa-utensils me-2" style="color: var(--primary-green);"></i> Distribución de Comidas y Menú Semanal
        </h5>

        <?php if(empty($planAgrupado)): ?>
            <div class="text-center py-4 border rounded">
                <p class="text-muted mb-0">Sin comidas específicas cargadas.</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                    <div class="col-6 mb-3">
                        <div class="card-menu-day">
                            <div class="card-header-day">
                                <?= htmlspecialchars($nombreDia) ?>
                            </div>
                            <div class="p-3 bg-white">
                                <?php foreach($momentosAgrupados as $momento => $items): ?>
                                    <div class="momento-title">
                                        <?= htmlspecialchars($momento) ?>
                                    </div>
                                    <?php foreach($items as $item): ?>
                                        <div class="food-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= htmlspecialchars($item['Alimento']) ?></strong>
                                                <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                                    <br><small class="text-muted fst-italic"><?= htmlspecialchars($item['Indicaciones_Especiales']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <?php if(!empty($item['Cantidad'])): ?>
                                                <span class="badge bg-light text-dark border ms-2"><?= htmlspecialchars($item['Cantidad']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Educación Nutricional & Pautas de Hábitos (Informe Integrado) -->
        <?php if(!empty($planActivo['Recomendaciones'])): ?>
            <div class="education-box">
                <h5 class="fw-bold mb-2" style="color: var(--dark-green);">
                    <i class="fa-solid fa-graduation-cap me-2"></i> Pautas de Educación Nutricional y Hábitos
                </h5>
                <p class="mb-0 text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?= htmlspecialchars($planActivo['Recomendaciones']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Pie y Firma Profesional -->
        <div class="footer-sign">
            <div class="text-muted small" style="max-width: 60%;">
                <p class="mb-0"><em>* Este plan alimentario y sus pautas de educación nutricional fueron elaborados exclusivamente para las necesidades fisiológicas de este paciente.</em></p>
            </div>
            <div class="text-center" style="min-width: 200px;">
                <div style="border-bottom: 1px solid #000; width: 180px; margin: 0 auto 5px auto;"></div>
                <small class="fw-bold d-block"><?= htmlspecialchars($nombreNutri) ?></small>
                <small class="text-muted">Firma y Sello Profesional</small>
            </div>
        </div>

    </div>
</body>
</html>
