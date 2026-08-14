<?php
$currentAction = 'admin_dashboard';
$nombre = $_SESSION['NombreNutri'] ?? 'Admin';
$apellido = $_SESSION['ApellidoNutri'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #2ecc71;
            --dark-green: #27ae60;
            --text-dark: #2c3e50;
        }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .sidebar {
            height: 100vh; width: 280px; position: fixed; top: 0; left: 0;
            background-color: #1a252f; padding-top: 1rem; color: white;
        }
        .main-content { margin-left: 280px; padding: 20px; }
        .nav-link { color: #ecf0f1; padding: 12px 20px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: #2ecc71; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center fw-bold mb-4 text-success"><i class="fa-solid fa-leaf"></i> NutriSalud</h4>
        <ul class="nav flex-column px-3">
            <li class="nav-item"><a class="nav-link active" href="index.php?action=admin_dashboard"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?action=admin_nutricionistas"><i class="fa-solid fa-users-medical me-2"></i> Nutricionistas</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger" href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a></li>
        </ul>
    </div>
    <div class="main-content">
        <h2 class="fw-bold mb-4">Bienvenido, <?= htmlspecialchars($nombre) ?> (SuperAdmin)</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-muted">Gestión de Plataforma</h5>
                        <p class="card-text">Puedes gestionar las cuentas de profesionales desde aquí.</p>
                        <a href="index.php?action=admin_nutricionistas" class="btn btn-success">Ver Nutricionistas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
