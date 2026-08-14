<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Plan Alimentario y Educación Nutricional</title>
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
            --bg-light: #f8f9fa;
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--bg-light); 
            color: var(--text-dark);
        }

        .sidebar-paciente { 
            height: 100vh; 
            width: 250px; 
            position: fixed; 
            background: #2c3e50; 
            padding-top: 2rem; 
            color: white; 
            z-index: 1000;
        }
        .sidebar-paciente a { 
            color: #b8c7ce; 
            padding: 15px 25px; 
            display: block; 
            text-decoration: none; 
            font-weight: 500; 
            transition: all 0.2s;
        }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { 
            background: linear-gradient(90deg, var(--primary-green), var(--dark-green)); 
            color: white; 
            border-left: 4px solid var(--light-green); 
        }
        .main-content { 
            margin-left: 250px; 
            padding: 2rem 3rem; 
            min-height: 100vh;
        }
        
        .plan-header { 
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); 
            color: white; 
            padding: 2rem 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px color-mix(in srgb, var(--primary-green) 30%, transparent); 
            margin-bottom: 2rem; 
        }

        .card-menu { 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            margin-bottom: 1.5rem; 
            overflow: hidden; 
            background: white;
        }
        .card-header-day { 
            background: linear-gradient(90deg, #1a252f, #2c3e50); 
            color: white; 
            font-weight: 600; 
            padding: 14px 20px; 
        }
        .momento-title { 
            color: var(--dark-green); 
            font-weight: 700; 
            border-bottom: 2px solid var(--light-green); 
            padding-bottom: 0.4rem; 
            margin-top: 1rem; 
            margin-bottom: 0.8rem; 
            text-transform: uppercase; 
            font-size: 0.85rem; 
            letter-spacing: 0.5px;
        }
        .food-item { 
            background: #fdfdfd; 
            border-left: 4px solid var(--primary-green); 
            padding: 12px 16px; 
            margin-bottom: 8px; 
            border-radius: 8px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.02); 
        }

        .card-education {
            background: linear-gradient(135deg, #ffffff, var(--light-green));
            border: 1px solid color-mix(in srgb, var(--primary-green) 35%, transparent);
            border-left: 6px solid var(--primary-green);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        
        @media print {
            .sidebar-paciente, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; }
            .card-menu { box-shadow: none !important; border: 1px solid #ddd !important; }
            .plan-header { color: black !important; background: transparent !important; border-bottom: 2px solid var(--primary-green); box-shadow: none !important; }
            .plan-header * { color: black !important; }
        }

        @media (max-width: 991px) {
            .sidebar-paciente { display: none; }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole" style="color: var(--primary-green);"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan" class="active"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <?php if($planActivo): ?>
            <!-- Hero Card del Plan -->
            <div class="plan-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-white px-3 py-1 rounded-pill fw-bold mb-2" style="color: var(--dark-green);">
                        <i class="fa-solid fa-clipboard-check me-1"></i> Plan Alimentario Activo
                    </span>
                    <h2 class="fw-bold mb-1"><i class="fa-solid fa-book-open me-2"></i> <?= htmlspecialchars($planActivo['Nombre_Plan']) ?></h2>
                    <p class="mb-0 text-white-50 fs-5"><i class="fa-solid fa-bullseye me-2 text-warning"></i> Objetivo: <strong class="text-white"><?= htmlspecialchars($planActivo['Objetivo'] ?: 'Bienestar general y nutrición saludable') ?></strong></p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end bg-white bg-opacity-25 p-3 rounded-4">
                        <small class="d-block text-uppercase fw-bold text-white-50">Inicio</small>
                        <span class="fs-5 fw-bold"><?= date('d M, Y', strtotime($planActivo['Fecha_Inicio'])) ?></span>
                    </div>
                    <a href="index.php?action=imprimir_plan&id_plan=<?= $planActivo['IdPlan'] ?>" target="_blank" class="btn btn-dark rounded-pill px-4 py-3 fw-bold shadow no-print">
                        <i class="fa-solid fa-print me-2"></i> Imprimir o Guardar PDF
                    </a>
                </div>
            </div>

            <!-- Menú de la Semana -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-days me-2" style="color: var(--primary-green);"></i> Distribución de tus Comidas</h4>
            </div>

            <?php if(empty($planAgrupado)): ?>
                <div class="card card-menu p-5 text-center">
                    <i class="fa-solid fa-clipboard-list text-muted mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                    <h5 class="text-muted fw-bold">Tu profesional aún está confeccionando los momentos de este plan.</h5>
                    <p class="text-muted mb-0">Pronto verás el menú semanal completo aquí.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card card-menu h-100">
                                <div class="card-header card-header-day">
                                    <h5 class="mb-0"><i class="fa-regular fa-calendar-check me-2" style="color: var(--primary-green);"></i> <?= htmlspecialchars($nombreDia) ?></h5>
                                </div>
                                <div class="card-body bg-white p-4">
                                    <?php foreach($momentosAgrupados as $momento => $items): ?>
                                        <div class="momento-title">
                                            <i class="fa-regular fa-clock me-2"></i> <?= htmlspecialchars($momento) ?>
                                        </div>
                                        <?php foreach($items as $item): ?>
                                            <div class="food-item">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div>
                                                        <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($item['Alimento']) ?></h6>
                                                        <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                                            <small class="text-muted"><i class="fa-solid fa-circle-info me-1 text-primary"></i> <?= htmlspecialchars($item['Indicaciones_Especiales']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if(!empty($item['Cantidad'])): ?>
                                                        <span class="badge bg-light text-dark border px-3 py-2 ms-3" style="font-size: 0.85rem;">
                                                            <?= htmlspecialchars($item['Cantidad']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
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
                <div class="card card-education mt-3 mb-5 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-size: 1.2rem; background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-dark">Tu Guía de Educación Nutricional y Pautas</h4>
                            <small class="text-muted">Recomendaciones personalizadas preparadas por tu nutricionista</small>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-4 border">
                        <p class="text-dark m-0" style="white-space: pre-wrap; font-size: 1rem; line-height: 1.7;"><?= htmlspecialchars($planActivo['Recomendaciones']) ?></p>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 mt-5">
                <i class="fa-solid fa-folder-open text-muted mb-4" style="font-size: 5rem; opacity: 0.3;"></i>
                <h3 class="fw-bold text-dark mb-2">Aún no tienes un Plan Alimentario asignado</h3>
                <p class="text-muted fs-5">Tu profesional de nutrición te diseñará un plan personalizado en tu próxima consulta.</p>
                <a href="index.php?action=dashboard_paciente" class="btn rounded-pill px-4 py-2 mt-3 fw-bold text-white" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver a Mi Día
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
