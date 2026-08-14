<!-- views/planes/crear.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Nuevo Plan Alimentario</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">NutriSalud SaaS</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php?action=listar_pacientes">Mis Pacientes</a></li>
                    <li class="nav-item"><a class="nav-link active" href="index.php?action=crear_plan">Nuevo Plan Alimentario</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container text-center mt-5">
        <h2 class="text-success">Módulo de Dieta Atómico en Construcción</h2>
        <p class="text-muted mt-3">Esta sección se conectará a la tabla 'Detalle_Plan_Alimento' y permitirá cruzar 'Dia_Semana', 'Momento_Dia' y 'Alimento'.</p>
        <a href="index.php?action=listar_pacientes" class="btn btn-outline-success mt-4 rounded-pill px-4">Volver al Inicio</a>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
