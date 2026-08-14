<?php
$nombre = $_SESSION['NombreNutri'] ?? 'Usuario';
$apellido = $_SESSION['ApellidoNutri'] ?? '';
$especialidad = $_SESSION['EspecialidadNutri'] ?? 'Nutricionista';
$logoUrl = $_SESSION['LogoNutri'] ?? null;
$iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
?>
<div class="top-header">
    <div>
        <h1 class="page-title">¡Hola, <?= htmlspecialchars($nombre) ?>! 👋</h1>
        <p class="text-muted">Bienvenido a tu panel de control.</p>
    </div>
    <div class="user-profile">
        <div class="text-end d-none d-md-block">
            <div class="fw-bold text-dark"><?= htmlspecialchars($nombre . ' ' . $apellido) ?></div>
            <small class="text-muted"><?= htmlspecialchars($especialidad) ?></small>
        </div>
        <?php if(!empty($logoUrl)): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>?v=<?= time() ?>" class="user-avatar" style="object-fit: cover;" alt="Avatar">
        <?php else: ?>
            <div class="user-avatar"><?= $iniciales ?></div>
        <?php endif; ?>
    </div>
</div>
