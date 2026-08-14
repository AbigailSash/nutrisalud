<?php
$colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Lista de Pacientes</title>
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
            flex-wrap: wrap;
            gap: 15px;
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
        
        .table-custom tbody tr {
            transition: background-color 0.2s;
        }

        .table-custom tbody tr:hover {
            background-color: var(--light-green);
        }

        .user-avatar-small {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            margin-right: 15px;
            background: #fdf2e9;
            color: #e67e22;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            margin: 0 3px;
            text-decoration: none;
            border: none;
        }
        
        .btn-action.edit { color: #3498db; background-color: #ebf5fb; }
        .btn-action.edit:hover { background-color: #3498db; color: white; }
        
        .btn-action.delete { color: #e74c3c; background-color: #fdedec; }
        .btn-action.delete:hover { background-color: #e74c3c; color: white; }

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
                <h1 class="page-title">Directorio de Pacientes</h1>
                <p class="text-muted mb-0">Gestiona las historias clínicas e información personal de tus pacientes.</p>
            </div>
            <a href="index.php?action=crear_paciente" class="btn btn-gradient text-decoration-none">
                <i class="fa-solid fa-user-plus me-2"></i> Nuevo Paciente
            </a>
        </div>

        <div class="dashboard-card">
            <div class="card-header-custom">
                <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
                    <i class="fa-solid fa-magnifying-glass text-muted me-3"></i>
                    <input type="text" id="inputBuscarPaciente" class="form-control border-0 bg-transparent shadow-none" placeholder="Buscar paciente por nombre, apellido o DNI...">
                </div>
                <div class="text-muted small">
                    Total: <strong id="totalPacientesCount"><?= count($pacientes ?? []) ?></strong> pacientes
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="tablaPacientes">
                        <thead>
                            <tr>
                                <th class="ps-4">Paciente</th>
                                <th>DNI</th>
                                <th>Nacimiento</th>
                                <th>Obra Social</th>
                                <th>Contacto</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pacientes) && is_array($pacientes)): ?>
                                <?php foreach ($pacientes as $p): ?>
                                    <tr class="paciente-row" data-search="<?= strtolower(htmlspecialchars(($p['Apellido'] ?? '') . ' ' . ($p['Nombre'] ?? '') . ' ' . ($p['DNI'] ?? ''))) ?>">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar-small">
                                                    <?= strtoupper(substr($p['Nombre'] ?? 'X',0,1) . substr($p['Apellido'] ?? 'X',0,1)) ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        <?= htmlspecialchars(($p['Apellido'] ?? '') . ', ' . ($p['Nombre'] ?? '')) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold text-muted">
                                            <?= htmlspecialchars($p['DNI'] ?? 'S/N') ?>
                                        </td>
                                        <td><?= htmlspecialchars($p['Fecha_Nacimiento'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <?= htmlspecialchars($p['Obra_Social'] ?: 'Particular') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.9rem;">
                                                <div><i class="fa-solid fa-phone text-muted me-1"></i> <?= htmlspecialchars($p['Telefono'] ?? 'N/A') ?></div>
                                                <div><i class="fa-solid fa-envelope text-muted me-1"></i> <?= htmlspecialchars($p['Email'] ?? 'N/A') ?></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?action=ver_historia_clinica&id=<?= htmlspecialchars($p['IdPaciente'] ?? '') ?>" class="btn-action" title="Historia Clínica" style="background: rgba(13, 202, 240, 0.1); color: #0dcaf0;">
                                                <i class="fa-solid fa-notes-medical"></i>
                                            </a>
                                            <a href="index.php?action=imprimir_ficha_medica&id=<?= htmlspecialchars($p['IdPaciente'] ?? '') ?>" target="_blank" class="btn-action" title="Imprimir Ficha Médica" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <a href="index.php?action=editar_paciente&id=<?= htmlspecialchars($p['IdPaciente'] ?? '') ?>" class="btn-action edit" title="Editar">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="index.php?action=eliminar_paciente&id=<?= htmlspecialchars($p['IdPaciente'] ?? '') ?>" class="btn-action delete" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar permanentemente a este paciente?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="noPacientesRow">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3">
                                                <i class="fa-solid fa-users-slash" style="font-size: 3rem; color: #cbd5e1;"></i>
                                            </div>
                                            <h5 class="text-muted fw-bold">Sin Pacientes Registrados</h5>
                                            <p class="text-muted mb-0">Comienza agregando a tu primer paciente.</p>
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
    <script>
        // Filtro en vivo de pacientes
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById('inputBuscarPaciente');
            const rows = document.querySelectorAll('.paciente-row');
            const countDisplay = document.getElementById('totalPacientesCount');

            if (input && rows.length > 0) {
                input.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const searchText = row.getAttribute('data-search') || '';
                        if (searchText.includes(query)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (countDisplay) {
                        countDisplay.textContent = visibleCount;
                    }
                });
            }
        });
    </script>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
