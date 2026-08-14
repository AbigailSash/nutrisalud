<?php
$currentAction = 'admin_nutricionistas';
$nombre = $_SESSION['NombreNutri'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Admin Nutricionistas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .sidebar {
            height: 100vh; width: 280px; position: fixed; top: 0; left: 0;
            background-color: #1a252f; padding-top: 1rem; color: white;
        }
        .main-content { margin-left: 280px; padding: 20px; }
        .nav-link { color: #ecf0f1; padding: 12px 20px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: #2ecc71; border-radius: 8px; }
        .table-custom { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="text-center fw-bold mb-4 text-success"><i class="fa-solid fa-leaf"></i> NutriSalud</h4>
        <ul class="nav flex-column px-3">
            <li class="nav-item"><a class="nav-link" href="index.php?action=admin_dashboard"><i class="fa-solid fa-chart-pie me-2"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link active" href="index.php?action=admin_nutricionistas"><i class="fa-solid fa-users-medical me-2"></i> Nutricionistas</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger" href="index.php?action=logout"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0"><i class="fa-solid fa-user-doctor text-success me-2"></i> Gestión de Nutricionistas</h2>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNutricionista" onclick="limpiarFormulario()">
                <i class="fa-solid fa-plus me-2"></i> Nuevo Nutricionista
            </button>
        </div>

        <div class="table-responsive table-custom p-3">
            <table class="table table-hover align-middle" id="tablaNutricionistas">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Profesional</th>
                        <th>Email</th>
                        <th>Pacientes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Llenado vía JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Formulario -->
    <div class="modal fade" id="modalNutricionista" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Nuevo Nutricionista</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNutricionista">
                        <input type="hidden" id="nutri_id" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">DNI</label>
                                <input type="text" class="form-control" name="dni" id="nutri_dni" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Matrícula</label>
                                <input type="text" class="form-control" name="matricula" id="nutri_matricula" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="nutri_nombre" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" name="apellido" id="nutri_apellido" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="nutri_email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" id="nutri_password" placeholder="(Opcional en edición)">
                                <small class="text-muted">Dejar vacío para no cambiar</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select class="form-select" name="estado" id="nutri_estado">
                                    <option value="A">Activo</option>
                                    <option value="I">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="guardarNutricionista()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/admin_nutricionistas.js"></script>
</body>
</html>
