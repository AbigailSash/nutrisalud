<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Crear Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-green: <?= htmlspecialchars($colorTema) ?>;
            --dark-green: color-mix(in srgb, var(--primary-green) 75%, black);
            --light-green: color-mix(in srgb, var(--primary-green) 15%, white);
        }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .form-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 3rem; }
        .btn-gradient { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white; border: none; font-weight: 600; border-radius: 50px; }
        .btn-gradient:hover { color: white; opacity: 0.95; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold">Crear Nuevo Plan Alimentario</h2>
            <a href="index.php?action=listar_planes" class="btn btn-outline-secondary rounded-pill">Volver</a>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?action=crear_plan" method="POST" class="form-card">
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Paciente</label>
                    <select name="id_paciente" class="form-select" required>
                        <option value="">Seleccione el paciente...</option>
                        <?php foreach($pacientes as $p): ?>
                            <option value="<?= $p['IdPaciente'] ?>"><?= htmlspecialchars($p['Nombre'].' '.$p['Apellido']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nombre del Plan</label>
                    <input type="text" name="nombre_plan" class="form-control" placeholder="Ej: Plan Descenso Fase 1" required>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Fecha de Fin (Opcional)</label>
                    <input type="date" name="fecha_fin" class="form-control">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Objetivo Principal</label>
                <input type="text" name="objetivo" class="form-control" placeholder="Ej: Déficit calórico de 300 kcal">
            </div>

            <button type="submit" class="btn btn-gradient w-100 py-3">Crear Plan y Avanzar al Diseño de Menú <i class="fa-solid fa-arrow-right ms-2"></i></button>
        </form>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
