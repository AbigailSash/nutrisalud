<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSalud - Cambiar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Cambiar Contraseña</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($mensaje)): ?>
                            <div class="alert alert-success text-center"><?= htmlspecialchars($mensaje) ?></div>
                        <?php endif; ?>

                        <form action="index.php?action=procesar_cambiar_password" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" name="nueva_password" class="form-control" required minlength="6" placeholder="Mínimo 6 caracteres" style="border-right: none;">
                                    <button type="button" class="btn btn-toggle-password input-group-text border" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" name="confirmar_password" class="form-control" required minlength="6" placeholder="Repite la nueva contraseña" style="border-right: none;">
                                    <button type="button" class="btn btn-toggle-password input-group-text border" aria-label="Mostrar contraseña" title="Mostrar u ocultar contraseña">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-gradient w-100 rounded-pill py-2 fw-bold">Guardar Contraseña</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="index.php?action=dashboard" class="text-decoration-none text-secondary">Volver al Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
