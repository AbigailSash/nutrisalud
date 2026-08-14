<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .card-kpi { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 20px; height: 100%; }
        .meal-card { background: white; border-radius: 12px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #2ecc71; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .prof-card { background: linear-gradient(135deg, #ffffff, #f1f9f6); border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(46, 204, 113, 0.1); }
        .btn-wa { background-color: #25D366; color: white; border-radius: 50px; font-weight: 600; }
        .btn-wa:hover { background-color: #128C7E; color: white; }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole text-info"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente" class="active"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Hola, <?= htmlspecialchars($_SESSION['NombrePaciente'] ?? 'Paciente') ?> 👋</h2>
            <p class="text-muted mb-0"><?= date('d M, Y') ?></p>
        </div>

        <!-- KPIs Superiores -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card-kpi bg-white">
                    <h6 class="text-muted fw-bold text-uppercase mb-2"><i class="fa-solid fa-bullseye text-primary me-2"></i> Mi Objetivo Actual</h6>
                    <?php if($planActivo): ?>
                        <h4 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($planActivo['Objetivo'] ?: 'Mantener estilo de vida saludable') ?></h4>
                        <small class="text-success">Plan: <?= htmlspecialchars($planActivo['Nombre_Plan']) ?></small>
                    <?php else: ?>
                        <h4 class="text-muted fst-italic">No definido</h4>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-kpi bg-white">
                    <h6 class="text-muted fw-bold text-uppercase mb-2"><i class="fa-solid fa-calendar-check text-warning me-2"></i> Próximo Turno</h6>
                    <?php if($proximoTurno): ?>
                        <h4 class="fw-bold mb-0"><?= date('d/m/Y', strtotime($proximoTurno['Fecha'])) ?> - <?= date('H:i', strtotime($proximoTurno['Hora'])) ?></h4>
                        <span class="badge bg-<?= $proximoTurno['Estado_Turno'] == 'Confirmado' ? 'success' : 'warning text-dark' ?> rounded-pill mt-1"><?= $proximoTurno['Estado_Turno'] ?></span>
                    <?php else: ?>
                        <h5 class="text-muted fst-italic mb-2">No tienes turnos programados</h5>
                        <a href="index.php?action=solicitar_turno" class="btn btn-sm btn-outline-primary rounded-pill">Solicitar Turno</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna Izquierda: Plan del Día -->
            <div class="col-md-8">
                <h4 class="fw-bold mb-3">🍽️ Menú de Hoy</h4>
                
                <?php if(!$planActivo): ?>
                    <div class="alert alert-light text-center py-5 border rounded-4">
                        <i class="fa-solid fa-utensils text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5>No tienes un plan asignado actualmente.</h5>
                        <p class="text-muted">Contacta a tu profesional si crees que se trata de un error.</p>
                    </div>
                <?php elseif(empty($detallesHoy)): ?>
                    <div class="alert alert-info text-center py-4 rounded-4">
                        <i class="fa-solid fa-mug-hot mb-2" style="font-size: 2rem;"></i>
                        <h5>Hoy es un día libre o sin indicaciones estrictas.</h5>
                        <p class="mb-0">Disfruta con moderación siguiendo las pautas generales de tu nutricionista.</p>
                    </div>
                <?php else: ?>
                    
                    <?php 
                    // Agrupar por momento del día
                    $comidas = [];
                    foreach($detallesHoy as $d) {
                        $comidas[$d['Momento_Comida']][] = $d;
                    }
                    ?>

                    <?php foreach($comidas as $momento => $items): ?>
                        <div class="meal-card">
                            <h5 class="fw-bold text-success mb-3"><i class="fa-regular fa-clock me-1"></i> <?= htmlspecialchars($momento) ?></h5>
                            <ul class="list-unstyled mb-0 ms-3">
                                <?php foreach($items as $item): ?>
                                    <li class="mb-2">
                                        <i class="fa-solid fa-caret-right text-success me-2"></i>
                                        <strong><?= htmlspecialchars($item['Alimento']) ?></strong> 
                                        <span class="text-muted">(<?= $item['Cantidad'] ?>g)</span>
                                        <?php if(!empty($item['Indicaciones_Especiales'])): ?>
                                            <br><small class="text-muted ms-4 fst-italic">"<?= htmlspecialchars($item['Indicaciones_Especiales']) ?>"</small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                    
                <?php endif; ?>
            </div>

            <!-- Columna Derecha: Tarjeta Mi Profesional -->
            <div class="col-md-4">
                <h4 class="fw-bold mb-3">👨‍⚕️ Mi Profesional</h4>
                <div class="card prof-card p-4 text-center">
                    <?php if(!empty($miProfesional['Logo_URL'])): ?>
                        <img src="<?= htmlspecialchars($miProfesional['Logo_URL']) ?>" class="rounded-circle shadow-sm mx-auto mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle shadow-sm mx-auto mb-3 d-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px; background: #2ecc71; font-size: 2rem; font-weight: bold;">
                            <?= strtoupper(substr($miProfesional['Nombre'] ?? 'D', 0, 1) . substr($miProfesional['Apellido'] ?? '', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars(($miProfesional['Nombre'] ?? 'Doc') . ' ' . ($miProfesional['Apellido'] ?? '')) ?></h5>
                    <p class="text-success fw-medium small mb-2"><?= htmlspecialchars($miProfesional['Especialidad'] ?? 'Nutricionista') ?></p>
                    
                    <?php if(!empty($miProfesional['Biografia'])): ?>
                        <p class="text-muted small fst-italic mb-4">"<?= htmlspecialchars($miProfesional['Biografia']) ?>"</p>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <?php if(!empty($miProfesional['Whatsapp'])): ?>
                            <a href="<?= $waLink ?>" target="_blank" class="btn btn-wa"><i class="fa-brands fa-whatsapp me-2"></i> Enviar Mensaje</a>
                        <?php endif; ?>
                        <a href="index.php?action=solicitar_turno" class="btn btn-outline-primary rounded-pill"><i class="fa-regular fa-calendar-plus me-2"></i> Solicitar Turno</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
