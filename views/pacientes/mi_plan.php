<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Plan Alimentario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; padding: 2rem; }
        
        .plan-header { background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; padding: 2rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-menu { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem; overflow: hidden; }
        .card-header-day { background-color: #1a252f; color: white; border-radius: 15px 15px 0 0 !important; font-weight: 600; padding: 15px 20px; }
        .momento-title { color: #27ae60; font-weight: 700; border-bottom: 2px solid #eafaf1; padding-bottom: 0.5rem; margin-top: 1rem; margin-bottom: 1rem; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;}
        .food-item { background: #fdfdfd; border-left: 4px solid #2ecc71; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        
        @media print {
            .sidebar-paciente, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; }
            .card-menu { box-shadow: none !important; border: 1px solid #ddd !important; }
            .plan-header { color: black !important; background: transparent !important; border-bottom: 2px solid #2ecc71; box-shadow: none !important; }
            .plan-header * { color: black !important; }
        }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole text-info"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan" class="active"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <?php if($planActivo): ?>
            <div class="plan-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1"><i class="fa-solid fa-book-open me-2"></i> <?= htmlspecialchars($planActivo['Nombre_Plan']) ?></h2>
                    <p class="mb-0 text-white-50 fs-5"><i class="fa-solid fa-bullseye me-2"></i> Objetivo: <?= htmlspecialchars($planActivo['Objetivo']) ?></p>
                </div>
                <div class="text-end bg-white bg-opacity-25 p-3 rounded-4">
                    <small class="d-block text-uppercase fw-bold text-white-50">Fecha de Inicio</small>
                    <span class="fs-4 fw-bold"><?= date('d M, Y', strtotime($planActivo['Fecha_Inicio'])) ?></span>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4 no-print">
                <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 py-2 fw-bold shadow-sm"><i class="fa-solid fa-print me-2"></i> Imprimir o Guardar PDF</button>
            </div>

            <?php if(empty($planAgrupado)): ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-clipboard-list text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i>
                    <h4 class="text-muted">Tu profesional aún no ha diseñado las comidas de este plan.</h4>
                    <p class="text-muted">Pronto tendrás tu menú disponible aquí.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($planAgrupado as $nombreDia => $momentosAgrupados): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card card-menu h-100">
                                <div class="card-header card-header-day">
                                    <h5 class="mb-0"><i class="fa-regular fa-calendar-check me-2 text-success"></i> <?= htmlspecialchars($nombreDia) ?></h5>
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
                                                            <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> <?= htmlspecialchars($item['Indicaciones_Especiales']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="badge bg-light text-dark border px-3 py-2 ms-3" style="font-size: 0.9rem;">
                                                        <?= htmlspecialchars($item['Cantidad']) ?>
                                                    </span>
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

            <!-- Recomendaciones del Plan -->
            <?php if(!empty($planActivo['Recomendaciones'])): ?>
                <div class="mt-5 mb-4">
                    <h3 class="fw-bold mb-4 border-bottom pb-2"><i class="fa-solid fa-file-medical text-primary me-2"></i> Recomendaciones / Educación</h3>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 bg-white">
                            <p class="text-muted m-0" style="white-space: pre-wrap; font-size: 1.05rem;"><?= htmlspecialchars($planActivo['Recomendaciones']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5 mt-5">
                <i class="fa-solid fa-folder-open text-muted mb-4" style="font-size: 5rem; opacity: 0.3;"></i>
                <h3 class="fw-bold text-dark mb-2">No tienes un Plan Alimentario Activo</h3>
                <p class="text-muted fs-5">Tu profesional de nutrición debe asignarte un plan para que puedas verlo aquí.</p>
                <a href="index.php?action=dashboard_paciente" class="btn btn-primary rounded-pill px-4 py-2 mt-3 fw-bold">
                    <i class="fa-solid fa-arrow-left me-2"></i> Volver a Mi Día
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
