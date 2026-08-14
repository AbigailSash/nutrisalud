<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Solicitar Turno</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .form-card { background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; max-width: 600px; margin: 0 auto; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 15px; border: 1px solid #e0e0e0; }
        .form-control:focus, .form-select:focus { border-color: #3498db; box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25); }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole text-info"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos" class="active"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?action=mis_turnos" class="btn btn-light rounded-circle shadow-sm me-3"><i class="fa-solid fa-arrow-left"></i></a>
            <h2 class="fw-bold mb-0">Solicitar Nuevo Turno 📝</h2>
        </div>

        <div class="form-card">
            <p class="text-muted mb-4">Selecciona la fecha y hora de tu preferencia. Tu solicitud será enviada al nutricionista para su aprobación.</p>
            
            <form action="index.php?action=guardar_turno_paciente" method="POST">
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Fecha Deseada</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-regular fa-calendar text-primary"></i></span>
                        <input type="date" name="fecha" class="form-control" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Horario Preferido</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-regular fa-clock text-primary"></i></span>
                        <select name="hora" class="form-select" required>
                            <option value="">Selecciona un horario...</option>
                            <!-- Rangos generados (ejemplo de agenda típica) -->
                            <?php for($h=8; $h<=19; $h++): ?>
                                <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00 hs</option>
                                <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:30"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:30 hs</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Motivo de la consulta <span class="text-muted fw-normal">(Opcional)</span></label>
                    <textarea class="form-control" name="motivo" rows="3" placeholder="Ej: Control mensual, dudas sobre el plan, actualización de medidas..."></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                        <i class="fa-solid fa-paper-plane me-2"></i> Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
