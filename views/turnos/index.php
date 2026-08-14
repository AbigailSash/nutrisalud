<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Gestión de Turnos</title>
    <!-- Google Fonts: Poppins -->
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
            overflow-x: hidden;
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
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
            transition: all 0.3s;
        }

        .card-header-custom {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4);
            color: white;
        }

        /* Modern Table */
        .table-custom {
            margin-bottom: 0;
        }
        .table-custom thead th {
            background-color: var(--light-green);
            color: var(--dark-green);
            font-weight: 600;
            border-bottom: none;
            padding: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-custom tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f2f6;
            color: var(--text-dark);
            font-weight: 500;
        }

        .user-avatar-small {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            margin-right: 12px;
            background: #e3f2fd;
            color: #1976d2;
            font-weight: 600;
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
        <!-- Top Header Modular -->
        <?php include 'views/layout/header.php'; ?>

        <!-- Alertas Flash -->
        <?php include 'views/layout/alertas.php'; ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="page-title">Gestión de Turnos</h1>
                <p class="text-muted mb-0">Administra tu agenda de consultas y citas con pacientes.</p>
            </div>
            <a href="index.php?action=agendar_turno" class="btn btn-gradient text-decoration-none">
                <i class="fa-solid fa-plus me-2"></i> Nuevo Turno
            </a>
        </div>

        <!-- Agenda Section -->
        <div class="dashboard-card">
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-calendar-days me-2" style="color:var(--primary-green)"></i> Agenda General de Citas</h5>
                <span class="text-muted small">Total: <strong><?= count($turnos ?? []) ?></strong> turnos</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Fecha y Hora</th>
                                <th>Paciente</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($turnos)): ?>
                                <?php foreach($turnos as $t): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= date('d/m/Y', strtotime($t['Fecha'])) ?></div>
                                        <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= htmlspecialchars($t['Hora']) ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar-small">
                                                <?= strtoupper(substr($t['PacienteNombre'] ?? 'X',0,1) . substr($t['PacienteApellido'] ?? 'X',0,1)) ?>
                                            </div>
                                            <span class="fw-semibold"><?= htmlspecialchars(($t['PacienteNombre'] ?? '') . ' ' . ($t['PacienteApellido'] ?? '')) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-warning text-dark';
                                        if ($t['Estado_Turno'] === 'Confirmado') $badgeClass = 'bg-success text-white';
                                        elseif ($t['Estado_Turno'] === 'Cancelado') $badgeClass = 'bg-danger text-white';
                                        elseif ($t['Estado_Turno'] === 'Atendido') $badgeClass = 'bg-info text-dark';
                                        ?>
                                        <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 fw-semibold">
                                            <?= htmlspecialchars($t['Estado_Turno']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?action=editar_turno&id=<?= $t['IdTurno'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold me-1" title="Editar Turno">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button onclick="confirmarEliminacion(<?= $t['IdTurno'] ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" title="Eliminar Turno">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3">
                                                <i class="fa-solid fa-calendar-xmark" style="font-size: 3rem; color: #cbd5e1;"></i>
                                            </div>
                                            <h5 class="text-muted fw-bold">No hay turnos registrados</h5>
                                            <p class="text-muted mb-0">Comienza agendando un nuevo turno para tus pacientes.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmarEliminacion(idTurno) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "El turno será eliminado permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#27ae60',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#ffffff',
                customClass: {
                    title: 'fw-bold text-dark',
                    content: 'text-muted'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?action=eliminar_turno&id=' + idTurno;
                }
            });
        }
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
