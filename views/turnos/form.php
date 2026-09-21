<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - <?= $turno ? 'Editar Turno' : 'Nuevo Turno' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 12%, white);
            --text-dark: #1e293b;
            --bg-light: #f8fafc;
            --sidebar-bg: #1a252f;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light); 
            color: var(--text-dark);
            overflow-x: hidden;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.85rem;
            margin-bottom: 1.5rem;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            max-width: 650px;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 4px 14px rgba(46, 204, 113, 0.25);
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
            color: white;
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
        <!-- Header -->
        <?php include 'views/layout/header.php'; ?>

        <div class="d-flex align-items-center mb-4">
            <a href="index.php?action=listar_turnos" class="btn btn-light rounded-circle me-3 border" style="width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="page-title mb-0"><?= $turno ? 'Editar Turno' : 'Agendar Nuevo Turno' ?></h1>
                <p class="text-muted small mb-0">Configuración completa de la consulta médica.</p>
            </div>
        </div>

        <div class="form-card">
            <form action="index.php?action=<?= $turno ? 'actualizar_turno' : 'guardar_turno' ?>" method="POST">
                
                <?php if ($turno): ?>
                    <input type="hidden" name="id_turno" value="<?= $turno['IdTurno'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Paciente *</label>
                    <select name="id_paciente" class="form-select rounded-3 py-2" required <?= $turno ? 'disabled' : '' ?>>
                        <option value="">Selecciona un paciente...</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['IdPaciente'] ?>" <?= ($turno && $turno['IdPaciente'] == $p['IdPaciente']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['Apellido'] . ', ' . $p['Nombre']) ?> (DNI: <?= htmlspecialchars($p['DNI']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if($turno): ?>
                        <input type="hidden" name="id_paciente" value="<?= $turno['IdPaciente'] ?>">
                    <?php endif; ?>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold small text-muted text-uppercase">Fecha *</label>
                        <input type="date" name="fecha" class="form-control rounded-3 py-2" value="<?= $turno ? $turno['Fecha'] : date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-muted text-uppercase">Hora *</label>
                        <input type="time" name="hora" class="form-control rounded-3 py-2" value="<?= $turno ? substr($turno['Hora'], 0, 5) : '09:00' ?>" required>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Modalidad *</label>
                        <select name="modalidad" id="form_modalidad" class="form-select rounded-3 py-2" onchange="toggleFormModalidad(this.value)">
                            <option value="Presencial" <?= (!$turno || ($turno['Modalidad'] ?? '') === 'Presencial') ? 'selected' : '' ?>>🏢 Presencial</option>
                            <option value="Online" <?= ($turno && ($turno['Modalidad'] ?? '') === 'Online') ? 'selected' : '' ?>>💻 Online (Videollamada)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Estado *</label>
                        <select name="estado" class="form-select rounded-3 py-2" required>
                            <option value="Confirmado" <?= ($turno && $turno['Estado_Turno'] === 'Confirmado') ? 'selected' : '' ?>>✅ Confirmado</option>
                            <option value="Pendiente" <?= (!$turno || $turno['Estado_Turno'] === 'Pendiente') ? 'selected' : '' ?>>⏳ Pendiente</option>
                            <option value="Atendido" <?= ($turno && $turno['Estado_Turno'] === 'Atendido') ? 'selected' : '' ?>>📋 Atendido</option>
                            <option value="Cancelado" <?= ($turno && $turno['Estado_Turno'] === 'Cancelado') ? 'selected' : '' ?>>❌ Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Motivo de Consulta</label>
                    <input type="text" name="motivo_consulta" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($turno['Motivo_Consulta'] ?? 'Consulta Nutricional') ?>">
                </div>

                <div class="mb-3 <?= ($turno && ($turno['Modalidad'] ?? '') === 'Online') ? '' : 'd-none' ?>" id="form_div_link">
                    <label class="form-label fw-bold small text-muted text-uppercase"><i class="fa-solid fa-video me-1 text-primary"></i> Enlace de Videollamada (Meet / Zoom)</label>
                    <input type="url" name="link_reunion" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($turno['Link_Reunion'] ?? '') ?>" placeholder="https://meet.google.com/...">
                </div>

                <div class="mb-3 <?= ($turno && ($turno['Modalidad'] ?? '') === 'Online') ? 'd-none' : '' ?>" id="form_div_direccion">
                    <label class="form-label fw-bold small text-muted text-uppercase"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Consultorio / Dirección</label>
                    <input type="text" name="direccion" class="form-control rounded-3 py-2" value="<?= htmlspecialchars($turno['Direccion'] ?? 'Consultorio') ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted text-uppercase">Notas / Indicaciones</label>
                    <textarea name="notas" class="form-control rounded-3" rows="2"><?= htmlspecialchars($turno['Notas'] ?? '') ?></textarea>
                </div>

                <div class="d-flex gap-3">
                    <a href="index.php?action=listar_turnos" class="btn btn-light w-50 py-2 fw-semibold text-muted rounded-pill">Cancelar</a>
                    <button type="submit" class="btn btn-gradient w-50"><?= $turno ? 'Guardar Cambios' : 'Agendar Turno' ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFormModalidad(val) {
            const divLink = document.getElementById('form_div_link');
            const divDir = document.getElementById('form_div_direccion');
            if (val === 'Online') {
                divLink.classList.remove('d-none');
                divDir.classList.add('d-none');
            } else {
                divLink.classList.add('d-none');
                divDir.classList.remove('d-none');
            }
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
