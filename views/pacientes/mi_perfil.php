<?php
$fotoUrl = !empty($paciente['FotoPerfil']) ? $paciente['FotoPerfil'] : 'public/assets/img/default-avatar.png';
// Fix absolute paths for displaying (if they contain public/)
if (strpos($fotoUrl, 'public/') === false && $fotoUrl !== 'public/assets/img/default-avatar.png') {
    $fotoUrl = 'public/uploads/pacientes/' . basename($fotoUrl);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>NutriSalud - Mi Perfil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .sidebar-paciente { height: 100vh; width: 250px; position: fixed; background: #2c3e50; padding-top: 2rem; color: white; }
        .sidebar-paciente a { color: #b8c7ce; padding: 15px 25px; display: block; text-decoration: none; font-weight: 500; }
        .sidebar-paciente a:hover, .sidebar-paciente a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .profile-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; }
        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-custom { background-color: #3498db; color: white; font-weight: 600; border-radius: 50px; padding: 10px 25px; }
        .btn-custom:hover { background-color: #2980b9; color: white; }
    </style>
</head>
<body>
    <div class="sidebar-paciente">
        <h4 class="text-center mb-4"><i class="fa-solid fa-apple-whole text-info"></i> Mi Portal</h4>
        <a href="index.php?action=dashboard_paciente"><i class="fa-solid fa-house me-2"></i> Mi Día</a>
        <a href="index.php?action=mi_plan"><i class="fa-solid fa-utensils me-2"></i> Mi Plan Completo</a>
        <a href="index.php?action=mis_turnos"><i class="fa-solid fa-calendar me-2"></i> Mis Turnos</a>
        <a href="index.php?action=mi_perfil_paciente" class="active"><i class="fa-solid fa-user-gear me-2"></i> Mi Perfil</a>
        <a href="index.php?action=logout" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Salir</a>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Configuración de Perfil</h2>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-<?= $_SESSION['tipo_mensaje'] ?> alert-dismissible fade show">
                <?= $_SESSION['mensaje'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['mensaje']); unset($_SESSION['tipo_mensaje']); endif; ?>

        <div class="row">
            <div class="col-md-8 col-lg-6">
                <div class="profile-card">
                    <form action="index.php?action=actualizar_mi_perfil_paciente" method="POST" enctype="multipart/form-data">
                        
                        <div class="text-center mb-4">
                            <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto Perfil" class="avatar-preview mb-3" id="imgPreview">
                            <div>
                                <label for="fotoInput" class="btn btn-sm btn-outline-primary rounded-pill">Cambiar Foto</label>
                                <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/*" onchange="previewImage(this)">
                                <input type="hidden" name="foto_actual" value="<?= htmlspecialchars($paciente['FotoPerfil'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Nombre y Apellido</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($paciente['Nombre'] . ' ' . $paciente['Apellido']) ?>" disabled>
                            <div class="form-text">Tus datos personales solo pueden ser modificados por tu Nutricionista.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">DNI</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($paciente['DNI']) ?>" disabled>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3 fw-bold">Seguridad de la cuenta</h5>

                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual">
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-custom"><i class="fa-solid fa-save me-2"></i> Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imgPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
