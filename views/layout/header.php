<?php
$nombre = $_SESSION['NombreNutri'] ?? 'Usuario';
$apellido = $_SESSION['ApellidoNutri'] ?? '';
$especialidad = $_SESSION['EspecialidadNutri'] ?? 'Nutricionista';
$logoUrl = $_SESSION['LogoNutri'] ?? null;
$iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
?>

<style>
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
        gap: 15px;
    }
    .top-header .page-title {
        font-weight: 700;
        font-size: 1.85rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .top-header .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .top-header .user-avatar {
        width: 45px !important;
        height: 45px !important;
        min-width: 45px !important;
        min-height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 600;
        box-shadow: 0 4px 10px color-mix(in srgb, var(--primary-green) 30%, transparent);
        object-fit: cover !important;
        overflow: hidden !important;
    }
</style>

<div class="top-header">
    <div>
        <h1 class="page-title">¡Hola, <?= htmlspecialchars($nombre) ?>! 👋</h1>
        <p class="text-muted mb-0">Bienvenida/o a tu panel de gestión profesional.</p>
    </div>
    <div class="user-profile">
        <div class="text-end d-none d-md-block">
            <div class="fw-bold text-dark"><?= htmlspecialchars($nombre . ' ' . $apellido) ?></div>
            <small class="text-muted"><?= htmlspecialchars($especialidad) ?></small>
        </div>
        <?php if(!empty($logoUrl)): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>?v=<?= time() ?>" class="user-avatar" alt="Avatar">
        <?php else: ?>
            <div class="user-avatar"><?= $iniciales ?></div>
        <?php endif; ?>
    </div>
</div>
