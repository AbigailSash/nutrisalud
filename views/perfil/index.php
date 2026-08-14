<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-green: #2ecc71; --dark-green: #27ae60; --sidebar-bg: #1a252f; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .sidebar { height: 100vh; width: 280px; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); padding-top: 2rem; z-index: 1000; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 2.5rem; }
        .sidebar-brand i { color: var(--primary-green); margin-right: 10px; }
        .nav-sidebar .nav-link { color: #b8c7ce; padding: 12px 25px; font-weight: 500; }
        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,0.05); border-left: 4px solid var(--primary-green); }
        .nav-sidebar .nav-link i { margin-right: 12px; width: 20px; text-align: center; }
        .main-content { margin-left: 280px; padding: 2rem 3rem; }
        .dashboard-card { background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 2rem; }
        .btn-gradient { background: linear-gradient(135deg, var(--primary-green), var(--dark-green)); color: white; border: none; font-weight: 600; border-radius: 50px; }
        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #ccc; margin-bottom: 1rem; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include 'views/layout/sidebar.php'; ?>

    <div class="main-content">
        <h1 class="fw-bold mb-4">Ajustes de Perfil</h1>
        
        <?php include 'views/layout/alertas.php'; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger rounded-pill fw-bold"><i class="fa-solid fa-circle-xmark me-2"></i> Error al guardar los cambios. Revisa los datos.</div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <h4 class="fw-bold text-success mb-4"><i class="fa-solid fa-address-card me-2"></i> Datos Profesionales</h4>
                    <form action="index.php?action=actualizar_perfil" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4 align-items-center">
                            <div class="col-auto">
                                <?php if(!empty($perfil['Logo_URL'])): ?>
                                    <img src="<?= htmlspecialchars($perfil['Logo_URL']) ?>" class="avatar-preview">
                                <?php else: ?>
                                    <div class="avatar-preview"><i class="fa-solid fa-camera"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="col">
                                <label class="form-label fw-bold">Foto de Perfil / Logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG. Se utilizará en el encabezado de los informes.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($perfil['Nombre'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($perfil['Apellido'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Título / Especialidad</label>
                                <input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($perfil['Especialidad'] ?? '') ?>" placeholder="Ej: Lic. en Nutrición Clínica">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Matrícula (M.P.)</label>
                                <input type="text" name="matricula" class="form-control" value="<?= htmlspecialchars($perfil['Matricula'] ?? '') ?>" placeholder="Ej: M.P. 12345">
                            </div>
                        </div>

                        <h5 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-address-book text-muted me-2"></i> Datos de Contacto (Para Informes)</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-instagram text-danger"></i> Instagram</label>
                                <input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($perfil['Instagram'] ?? '') ?>" placeholder="@tu.usuario">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="fa-brands fa-whatsapp text-success"></i> WhatsApp / Teléfono</label>
                                <input type="text" name="whatsapp" class="form-control" value="<?= htmlspecialchars($perfil['Whatsapp'] ?? '') ?>" placeholder="+54 9 11...">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-location-dot text-primary"></i> Dirección / Localidad</label>
                            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($perfil['Direccion'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-book-open text-info"></i> Biografía / Presentación</label>
                            <textarea name="biografia" class="form-control" rows="3" placeholder="Ej: Especialista en nutrición deportiva..."><?= htmlspecialchars($perfil['Biografia'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient w-100 py-2">Guardar Datos de Perfil</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card border-top border-4 border-warning">
                    <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-lock text-warning me-2"></i> Seguridad de Cuenta</h5>
                    <form action="index.php?action=actualizar_password" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Contraseña Actual</label>
                            <input type="password" name="password_actual" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Nueva Contraseña</label>
                            <input type="password" name="nueva_password" class="form-control" placeholder="••••••••" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small">Confirmar Nueva Contraseña</label>
                            <input type="password" name="confirmar_password" class="form-control" placeholder="••••••••" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill">Actualizar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include 'views/layout/global_scripts.php'; ?>
</body>
</html>
