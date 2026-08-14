<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: linear-gradient(90deg, var(--primary-green), var(--dark-green)); color: white; border-left: 4px solid var(--light-green); }
        .main-content { margin-left: 250px; padding: 2rem; }
        .card-kpi { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 20px; height: 100%; }
        .meal-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 15px; border-left: 4px solid var(--primary-green); box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .prof-card { background: linear-gradient(135deg, #ffffff, var(--light-green)); border-radius: 20px; border: 1px solid color-mix(in srgb, var(--primary-green) 30%, transparent); box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .btn-wa { background-color: #25D366; color: white; border-radius: 50px; font-weight: 600; }
        .btn-wa:hover { background-color: #128C7E; color: white; }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole" style="color: var(--primary-green);"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente" class="active"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Hola, <?= htmlspecialchars($_SESSION['NombrePaciente'] ?? 'Paciente') ?> 👋</h2>
            <p class="text-muted mb-0"><i class="fa-regular fa-calendar me-1"></i> <?= date('d M, Y') ?></p>
        </div>

        <!-- KPIs Superiores -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="card card-kpi bg-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 text-white me-3" style="background: linear-gradient(135deg, var(--primary-green), var(--dark-green));">
                            <i class="fa-solid fa-apple-whole fa-2x"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Plan Alimentario Actual</small>
                            <h4 class="fw-bold mb-0 text-dark"><?= $planActivo ? htmlspecialchars($planActivo['Nombre_Plan']) : 'Sin plan activo' ?></h4>
                            <?php if($planActivo): ?>
                                <small class="text-muted">Objetivo: <?= htmlspecialchars($planActivo['Objetivo']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-kpi bg-white">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 text-white me-3" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                            <i class="fa-solid fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Próxima Consulta</small>
                            <?php if($proximoTurno): ?>
                                <h4 class="fw-bold mb-0 text-dark"><?= date('d/m/Y', strtotime($proximoTurno['Fecha'])) ?> - <?= substr($proximoTurno['Hora'], 0, 5) ?> hs</h4>
                                <span class="badge bg-success bg-opacity-75"><?= htmlspecialchars($proximoTurno['Estado_Turno']) ?></span>
                            <?php else: ?>
                                <h4 class="fw-bold mb-0 text-dark">No tienes turnos</h4>
                                <a href="index.php?action=mis_turnos" class="small text-decoration-none fw-bold" style="color: var(--dark-green);">Solicitar un turno &rarr;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Menú de Hoy -->
            <div class="col-lg-8 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-utensils me-2" style="color: var(--primary-green);"></i> Lo que te toca comer hoy (<?= $nombreDiaHoy ?>)</h4>
                    <a href="index.php?action=mi_plan" class="btn btn-sm btn-outline-secondary rounded-pill">Ver Semana Completa</a>
                </div>

                <?php if(empty($comidasHoy)): ?>
                    <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                        <i class="fa-solid fa-bowl-food text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5 class="text-muted fw-bold">No hay comidas programadas para hoy</h5>
                        <p class="text-muted mb-0">Revisa tu plan completo o consúltale a tu nutricionista.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($comidasHoy as $momento => $items): ?>
                        <div class="meal-card">
                            <h5 class="fw-bold mb-3 text-uppercase fs-6" style="color: var(--dark-green); letter-spacing: 0.5px;">
                                <i class="fa-regular fa-clock me-1"></i> <?= htmlspecialchars($momento) ?>
                            </h5>
                            <?php foreach($items as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                                    <div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($item['Alimento']) ?></span>
                                        <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                            <br><small class="text-muted fst-italic"><i class="fa-solid fa-info-circle me-1 text-primary"></i> <?= htmlspecialchars($item['Indicaciones_Especiales']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($item['Cantidad'])): ?>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['Cantidad']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Ficha del Profesional Asignado -->
            <div class="col-lg-4">
                <div class="card prof-card p-4">
                    <h5 class="fw-bold text-dark mb-4 text-center">Tu Profesional a Cargo</h5>
                    <?php if($nutricionista): ?>
                        <div class="text-center mb-3">
                            <?php if(!empty($nutricionista['Logo_URL'])): ?>
                                <img src="<?= htmlspecialchars($nutricionista['Logo_URL']) ?>" class="rounded-circle shadow-sm mx-auto mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle shadow-sm mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px; background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); font-size: 2rem; font-weight: bold;">
                                    <?= strtoupper(substr($nutricionista['Nombre'], 0, 1) . substr($nutricionista['Apellido'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($nutricionista['Nombre'] . ' ' . $nutricionista['Apellido']) ?></h5>
                            <small class="text-muted d-block"><?= htmlspecialchars($nutricionista['Especialidad'] ?? 'Nutricionista Clínico') ?></small>
                            <small class="text-muted d-block fw-semibold">M.P. <?= htmlspecialchars($nutricionista['Matricula'] ?? 'Nacional') ?></small>
                        </div>

                        <?php if(!empty($nutricionista['Biografia'])): ?>
                            <p class="small text-muted text-center mb-4 fst-italic">"<?= htmlspecialchars($nutricionista['Biografia']) ?>"</p>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?php if(!empty($nutricionista['Whatsapp'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $nutricionista['Whatsapp']) ?>" target="_blank" class="btn btn-wa py-2">
                                    <i class="fa-brands fa-whatsapp me-2 fs-5 align-middle"></i> Contactar por WhatsApp
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($nutricionista['Instagram'])): ?>
                                <a href="https://instagram.com/<?= str_replace('@', '', $nutricionista['Instagram']) ?>" target="_blank" class="btn btn-outline-danger rounded-pill py-2">
                                    <i class="fa-brands fa-instagram me-2"></i> Seguir en Instagram
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">No hay datos del profesional.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
