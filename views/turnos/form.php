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
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
            --text-dark: #2c3e50;
            --bg-light: #f4f7f6;
            --sidebar-bg: #1a252f;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light); 
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--sidebar-bg);
            padding-top: 2rem;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-brand i { color: var(--primary-green); margin-right: 10px; }

        .nav-sidebar .nav-link {
            color: #b8c7ce;
            padding: 12px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-bottom: 5px;
            border-left: 4px solid transparent;
        }

        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.05);
            border-left-color: var(--primary-green);
        }

        .nav-sidebar .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem 3rem;
            min-height: 100vh;
        }

        .page-title {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .form-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            max-width: 600px;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
            color: white;
        }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title"><?= $turno ? 'Editar Turno' : 'Agendar Nuevo Turno' ?></h1>

        <div class="form-card">
            <form action="index.php?action=<?= $turno ? 'actualizar_turno' : 'guardar_turno' ?>" method="POST">
                
                <?php if ($turno): ?>
                    <input type="hidden" name="id_turno" value="<?= $turno['IdTurno'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label fw-bold">Paciente</label>
                    <select name="id_paciente" class="form-select" required <?= $turno ? 'disabled' : '' ?>>
                        <option value="">Selecciona un paciente...</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['IdPaciente'] ?>" <?= ($turno && $turno['IdPaciente'] == $p['IdPaciente']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['Nombre'] . ' ' . $p['Apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if($turno): ?>
                        <input type="hidden" name="id_paciente" value="<?= $turno['IdPaciente'] ?>">
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= $turno ? $turno['Fecha'] : '' ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Hora</label>
                        <input type="time" name="hora" class="form-control" value="<?= $turno ? $turno['Hora'] : '' ?>" required>
                    </div>
                </div>

                <?php if ($turno): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="Pendiente" <?= $turno['Estado_Turno'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Confirmado" <?= $turno['Estado_Turno'] == 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="Cancelado" <?= $turno['Estado_Turno'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <a href="index.php?action=listar_turnos" class="btn btn-light w-50 py-2 fw-bold text-muted mt-3">Cancelar</a>
                    <button type="submit" class="btn-gradient w-50"><?= $turno ? 'Guardar Cambios' : 'Agendar Turno' ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
