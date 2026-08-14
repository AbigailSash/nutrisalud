<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mis Turnos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .turno-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; padding: 20px; transition: transform 0.2s; border-left: 5px solid transparent; }
        .turno-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        .turno-card.pendiente { border-left-color: #f39c12; }
        .turno-card.confirmado { border-left-color: #2ecc71; }
        .turno-card.cancelado { border-left-color: #e74c3c; opacity: 0.7; }
        
        .badge-turno { font-size: 0.85rem; padding: 6px 12px; border-radius: 20px; font-weight: 600; }
        .badge-pendiente { background-color: #fcf3cf; color: #d68910; }
        .badge-confirmado { background-color: #eaeded; color: #27ae60; }
        .badge-cancelado { background-color: #fadbd8; color: #c0392b; }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole text-info"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos" class="active"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Mis Turnos 📅</h2>
            <a href="index.php?action=solicitar_turno" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4">
                <i class="fa-solid fa-plus me-2"></i> Solicitar Nuevo Turno
            </a>
        </div>

        <?php if(isset($_GET['success']) && $_GET['success'] == 'turno_solicitado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i> Tu solicitud de turno ha sido enviada. El profesional te confirmará a la brevedad.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['success']) && $_GET['success'] == 'cancelado'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i> El turno fue cancelado exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Próximos Turnos (Pendientes o Confirmados >= hoy) -->
            <div class="col-md-6">
                <h4 class="text-muted fw-bold mb-4 border-bottom pb-2">Próximos Turnos</h4>
                <?php 
                $proximosEncontrados = false;
                foreach($turnos as $t): 
                    $fechaTurno = strtotime($t['Fecha']);
                    $hoy = strtotime(date('Y-m-d'));
                    
                    if ($fechaTurno >= $hoy && $t['Estado_Turno'] != 'Cancelado'):
                        $proximosEncontrados = true;
                        $claseCard = strtolower($t['Estado_Turno']);
                ?>
                <div class="card turno-card bg-white <?= $claseCard ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h4 class="fw-bold text-dark mb-1"><i class="fa-regular fa-calendar text-primary me-2"></i> <?= date('d/m/Y', strtotime($t['Fecha'])) ?></h4>
                            <p class="text-muted mb-0 ms-4"><i class="fa-regular fa-clock me-1"></i> <?= date('H:i', strtotime($t['Hora'])) ?> hs</p>
                        </div>
                        <span class="badge-turno badge-<?= $claseCard ?>"><?= $t['Estado_Turno'] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <a href="index.php?action=cancelar_turno_paciente&id=<?= $t['IdTurno'] ?>&f=<?= $t['Fecha'] ?>&h=<?= $t['Hora'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-confirm="true" data-mensaje="¿Estás seguro de que deseas cancelar este turno?">
                            <i class="fa-solid fa-ban me-1"></i> Cancelar Cita
                        </a>
                    </div>
                </div>
                <?php endif; endforeach; ?>
                
                <?php if(!$proximosEncontrados): ?>
                    <div class="text-center text-muted p-4 bg-white rounded-3 shadow-sm border">
                        <i class="fa-regular fa-calendar-xmark fs-2 mb-2"></i>
                        <p class="mb-0">No tienes turnos próximos asignados.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Historial (Pasados o Cancelados) -->
            <div class="col-md-6">
                <h4 class="text-muted fw-bold mb-4 border-bottom pb-2">Historial</h4>
                <?php 
                $historialEncontrado = false;
                foreach($turnos as $t): 
                    $fechaTurno = strtotime($t['Fecha']);
                    $hoy = strtotime(date('Y-m-d'));
                    
                    if ($fechaTurno < $hoy || $t['Estado_Turno'] == 'Cancelado'):
                        $historialEncontrado = true;
                        $claseCard = strtolower($t['Estado_Turno']);
                ?>
                <div class="card turno-card bg-light <?= $claseCard ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold text-secondary mb-1"><i class="fa-solid fa-clock-rotate-left me-2"></i> <?= date('d/m/Y', strtotime($t['Fecha'])) ?> - <?= date('H:i', strtotime($t['Hora'])) ?> hs</h6>
                        </div>
                        <span class="badge-turno badge-<?= $claseCard ?>"><?= $t['Estado_Turno'] ?></span>
                    </div>
                </div>
                <?php endif; endforeach; ?>
                
                <?php if(!$historialEncontrado): ?>
                    <p class="text-muted text-center mt-4">Aún no hay historial de turnos.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
