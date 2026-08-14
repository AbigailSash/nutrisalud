<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Planes Alimentarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #2ecc71; --dark-green: #27ae60; --light-green: #eafaf1; --sidebar-bg: #1a252f; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .sidebar { height: 100vh; width: 280px; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); padding-top: 2rem; z-index: 1000; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 2.5rem; }
        .sidebar-brand i { color: var(--primary-green); margin-right: 10px; }
        .nav-sidebar .nav-link { color: #b8c7ce; padding: 12px 25px; font-weight: 500; }
        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,0.05); border-left: 4px solid var(--primary-green); }
        .nav-sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; }
        .main-content { margin-left: 280px; padding: 2rem 3rem; }
        .dashboard-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .btn-gradient { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white; border: none; font-weight: 600; padding: 10px 25px; border-radius: 50px; }
    </style>
</head>
<body>
    <?php include 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold m-0">Planes Alimentarios</h1>
            <a href="index.php?action=crear_plan" class="btn btn-gradient"><i class="fa-solid fa-plus me-2"></i> Crear Nuevo Plan</a>
        </div>
        
        <?php include 'views/layout/alertas.php'; ?>

        <div class="dashboard-card p-0">
            <table class="table table-hover mb-0">
                <thead style="background-color: var(--light-green);">
                    <tr>
                        <th class="p-4 border-0">Paciente</th>
                        <th class="p-4 border-0">Nombre del Plan</th>
                        <th class="p-4 border-0">Fecha de Inicio</th>
                        <th class="p-4 border-0 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($planes)): ?>
                        <?php foreach($planes as $p): ?>
                        <tr>
                            <td class="p-4 align-middle fw-bold"><?= htmlspecialchars($p['Nombre'] . ' ' . $p['Apellido']) ?></td>
                            <td class="p-4 align-middle text-muted"><?= htmlspecialchars($p['Nombre_Plan']) ?></td>
                            <td class="p-4 align-middle text-muted"><?= date('d/m/Y', strtotime($p['Fecha_Inicio'])) ?></td>
                            <td class="p-4 align-middle text-end">
                                <a href="index.php?action=gestionar_detalles_plan&id=<?= $p['IdPlan'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"><i class="fa-solid fa-pencil me-1"></i> Diseñar (ABM)</a>
                                <a href="index.php?action=imprimir_plan&id_plan=<?= $p['IdPlan'] ?>" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold"><i class="fa-solid fa-print me-1"></i> Imprimir / PDF</a>
                                <a href="index.php?action=eliminar_plan&id=<?= $p['IdPlan'] ?>" data-confirm="true" data-mensaje="¿Eliminar plan por completo?" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center p-5 text-muted">Aún no hay planes creados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
