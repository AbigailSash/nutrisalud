<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Dashboard Profesional</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #2ecc71;
            --dark-green: #27ae60;
            --light-green: #eafaf1;
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

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.3);
        }

        /* Cards and Elements */
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
        
        .dashboard-card:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
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
            padding: 20px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f2f6;
            color: var(--text-dark);
            font-weight: 500;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 20px;
        }
        
        .stat-icon.green { background-color: var(--light-green); color: var(--primary-green); }
        .stat-icon.blue { background-color: #e3f2fd; color: #2196f3; }
        .stat-icon.orange { background-color: #fff3e0; color: #ff9800; }
        
        .stat-details h3 { font-size: 1.8rem; font-weight: 700; margin: 0; }
        .stat-details p { color: var(--text-gray); margin: 0; font-size: 0.95rem; }

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
        <?php include 'views/layout/header.php'; ?>

        <!-- Quick Tools -->
        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-gradient shadow" data-bs-toggle="modal" data-bs-target="#modalCalculadoraRapida">
                <i class="fa-solid fa-calculator me-2"></i> Calculadora Rápida
            </button>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-user-clock"></i></div>
                    <div class="stat-details">
                        <h3><?= $turnosHoy ?? 0 ?></h3>
                        <p>Turnos Hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-details">
                        <h3><?= $pacientesActivos ?? 0 ?></h3>
                        <p>Pacientes Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fa-solid fa-file-invoice"></i></div>
                    <div class="stat-details">
                        <h3><?= $planesDisenados ?? 0 ?></h3>
                        <p>Planes Diseñados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agenda Section -->
        <div class="dashboard-card mt-2">
            <div class="card-header-custom">
                <h4 class="mb-0 fw-bold"><i class="fa-solid fa-calendar-day me-2 text-primary-custom" style="color:var(--primary-green)"></i> Agenda del Día</h4>
                <a href="index.php?action=agendar_turno" class="btn btn-gradient text-decoration-none"><i class="fa-solid fa-plus me-2"></i> Nuevo Turno</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom table-hover">
                        <thead>
                            <tr>
                                <th class="ps-4">Hora</th>
                                <th>Paciente</th>
                                <th>Edad</th>
                                <th>Obra Social</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            require_once 'models/Turno.php';
                            $turnoModel = new Turno();
                            $turnos = [];
                            try {
                                $turnos = $turnoModel->listarHoy($_SESSION['IdNutri'] ?? 1);
                            } catch (Exception $e) { }
                            ?>

                            <?php if (!empty($turnos)): ?>
                                <?php foreach($turnos as $t): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-muted"><?= htmlspecialchars($t['Hora']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem; margin-right: 10px; background: #e3f2fd; color: #2196f3; box-shadow: none;">
                                                <?= strtoupper(substr($t['Nombre'],0,1) . substr($t['Apellido'],0,1)) ?>
                                            </div>
                                            <?= htmlspecialchars($t['Nombre'] . ' ' . $t['Apellido']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($t['Edad_Calculada']) ?> años</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <?= htmlspecialchars($t['Obra_Social'] ?: 'Particular') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fw-semibold">
                                            <?= htmlspecialchars($t['Estado_Turno']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3">
                                                <i class="fa-solid fa-mug-hot" style="font-size: 3rem; color: #ecf0f1;"></i>
                                            </div>
                                            <h5 class="text-muted fw-bold">Día Libre</h5>
                                            <p class="text-muted mb-0">No tienes turnos programados para hoy.</p>
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
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
