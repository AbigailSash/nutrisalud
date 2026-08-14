<?php
// views/layout/alertas.php
if (isset($_SESSION['mensaje'])): 
    $tipo = $_SESSION['tipo_mensaje'] ?? 'success';
    $icono = 'fa-circle-check';
    if ($tipo === 'danger') $icono = 'fa-circle-xmark';
    else if ($tipo === 'warning') $icono = 'fa-triangle-exclamation';
    else if ($tipo === 'info') $icono = 'fa-circle-info';
?>
    <div class="alert alert-<?= htmlspecialchars($tipo) ?> alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px; margin-bottom: 20px; border-left: 5px solid; border-left-color: var(--<?= $tipo === 'success' ? 'primary-green' : ($tipo === 'danger' ? 'danger' : 'warning') ?>);">
        <i class="fa-solid <?= $icono ?> me-2"></i>
        <strong><?= htmlspecialchars($_SESSION['mensaje']) ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php 
    // Limpiar variables de sesión
    unset($_SESSION['mensaje']); 
    unset($_SESSION['tipo_mensaje']); 
endif; 
?>
