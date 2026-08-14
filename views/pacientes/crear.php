<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Registrar Paciente</title>
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

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.04);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .form-title {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 30px;
            text-align: center;
        }

        .form-title i {
            color: var(--primary-green);
        }

        /* Form Inputs Modernization */
        .form-label {
            font-weight: 500;
            color: var(--text-gray);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s;
            font-size: 0.95rem;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1);
            background-color: white;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
            width: 100%;
            font-size: 1.1rem;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.4);
            color: white;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-gray);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--primary-green);
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
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?action=listar_pacientes" class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-3">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="form-card">
            <h2 class="form-title"><i class="fa-solid fa-user-plus"></i> Registrar Nuevo Paciente</h2>
            
            <form action="index.php?action=guardar_paciente" method="POST">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">DNI / Documento de Identidad</label>
                        <input type="text" name="dni" class="form-control" placeholder="Ej. 12345678" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Juan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" placeholder="Pérez" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Obra Social</label>
                        <input type="text" name="obra_social" class="form-control" placeholder="Ej. OSDE, Swiss Medical, Particular">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Teléfono de Contacto</label>
                        <input type="text" name="telefono" class="form-control" placeholder="+54 9 11 1234-5678">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="juan.perez@email.com">
                    </div>
                </div>
                
                <div class="mt-2">
                    <button type="submit" class="btn btn-gradient">Guardar Paciente <i class="fa-solid fa-check ms-2"></i></button>
                    <a href="index.php?action=listar_pacientes" class="back-link">Cancelar registro</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
