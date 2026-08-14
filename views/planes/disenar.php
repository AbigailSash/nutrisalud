<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Planificador Atómico</title>
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

        /* Form Inputs Modernization */
        .form-label {
            font-weight: 500;
            color: var(--text-gray);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-select, .form-control {
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
            background-color: white;
        }
        
        /* User Avatar */
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

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-leaf"></i> NutriSalud
        </div>
        <ul class="nav flex-column nav-sidebar">
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=dashboard">
                    <i class="fa-solid fa-border-all"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?action=listar_pacientes">
                    <i class="fa-solid fa-users"></i> Mis Pacientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-calendar-check"></i> Turnos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#">
                    <i class="fa-solid fa-apple-whole"></i> Planes Alimentarios
                </a>
            </li>
            <li class="nav-item mt-5">
                <a class="nav-link text-danger" href="index.php?action=logout">
                    <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title"><i class="fa-solid fa-utensils text-success me-2" style="color:var(--primary-green) !important;"></i> Planificador 3FN</h1>
                <p class="text-muted">Asigna alimentos en la matriz del plan dietético.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="index.php?action=listar_pacientes" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left"></i> Volver a Pacientes
                </a>
                <div class="user-profile ms-3">
                    <div class="user-avatar">DN</div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card mb-4">
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-plus-circle me-2" style="color:var(--primary-green)"></i> Agregar Alimento</h5>
            </div>
            <div class="card-body p-4">
                <form action="index.php?action=guardar_plan" method="POST" class="row g-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Día de la semana</label>
                        <select class="form-select" name="id_dia">
                            <option value="1">Lunes</option>
                            <option value="2">Martes</option>
                            <option value="3">Miércoles</option>
                            <option value="4">Jueves</option>
                            <option value="5">Viernes</option>
                            <option value="6">Sábado</option>
                            <option value="7">Domingo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Momento del Día</label>
                        <select class="form-select" name="id_momento">
                            <option value="1">Desayuno</option>
                            <option value="2">Colación Mañana</option>
                            <option value="3">Almuerzo</option>
                            <option value="4">Merienda</option>
                            <option value="5">Cena</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="alimento" class="form-label">Alimento</label>
                        <input type="text" 
                               id="alimento" 
                               name="alimento" 
                               class="form-control" 
                               placeholder="Ej: Tostadas integrales con palta" 
                               required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Cantidad</label>
                        <div class="input-group">
                            <input type="number" name="cantidad" class="form-control" placeholder="Ej: 1" style="border-radius: 10px 0 0 10px;">
                            <button class="btn btn-gradient m-0 rounded-start-0" type="submit" style="padding: 12px 15px; border-radius: 0 10px 10px 0; width: auto;"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 d-flex align-items-center" style="background-color: #e3f2fd; color: #0c5460;">
            <i class="fa-solid fa-circle-info fs-3 me-3" style="color: #2196f3;"></i> 
            <div>
                <strong class="d-block mb-1">Información sobre la Arquitectura:</strong> 
                La visualización cruzada de la dieta consumirá dinámicamente la <code>Vista_Menu_Paciente</code> desde MySQL una vez insertados los datos en las tablas relacionales correspondientes.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
