<?php
$currentAction = $_GET['action'] ?? 'dashboard';

// Helpers para determinar si una clase está activa
function is_active($actions, $currentAction) {
    if (is_array($actions)) {
        return in_array($currentAction, $actions) ? 'active' : '';
    }
    return $currentAction === $actions ? 'active' : '';
}

// Datos de sesión para la mini tarjeta
$nombre = $_SESSION['NombreNutri'] ?? 'Usuario';
$apellido = $_SESSION['ApellidoNutri'] ?? '';
$matricula = $_SESSION['MatriculaNutri'] ?? '';
$logoUrl = $_SESSION['LogoNutri'] ?? null;
$iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
?>

<style>
    /* Estilos del Sidebar */
    .sidebar {
        height: 100vh;
        width: 280px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #1a252f;
        padding-top: 1rem;
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease;
    }

    /* Modificador para estado colapsado (aplicado vía JS) */
    .main-content {
        transition: margin-left 0.3s ease, width 0.3s ease;
    }
    body.sidebar-collapsed .sidebar {
        width: 80px;
    }
    body.sidebar-collapsed .main-content {
        margin-left: 80px;
        width: calc(100% - 80px);
    }

    /* Logo y Toggle */
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        margin-bottom: 1.5rem;
    }
    
    .sidebar-brand {
        color: white;
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        transition: opacity 0.2s;
        white-space: nowrap;
        overflow: hidden;
    }
    .sidebar-brand i { color: #2ecc71; margin-right: 10px; font-size: 1.5rem; }

    .sidebar-toggle-btn {
        background: transparent;
        border: none;
        color: #b8c7ce;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        transition: color 0.2s;
    }
    .sidebar-toggle-btn:hover { color: white; background: rgba(255,255,255,0.1); }

    /* Ocultar textos si está colapsado */
    body.sidebar-collapsed .sidebar-brand span,
    body.sidebar-collapsed .menu-category,
    body.sidebar-collapsed .nav-link-text,
    body.sidebar-collapsed .badge-menu,
    body.sidebar-collapsed .sidebar-user-info {
        display: none !important;
    }
    
    body.sidebar-collapsed .sidebar-brand i { margin-right: 0; }
    body.sidebar-collapsed .sidebar-header { justify-content: center; }

    /* Menú navegable */
    .sidebar-menu-wrapper {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 20px;
    }
    .sidebar-menu-wrapper::-webkit-scrollbar { width: 4px; }
    .sidebar-menu-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

    .menu-category {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 1.5rem 0 0.5rem 25px;
        white-space: nowrap;
    }

    .nav-sidebar .nav-link {
        color: #b8c7ce;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.2s ease;
        margin: 2px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        white-space: nowrap;
    }

    body.sidebar-collapsed .nav-sidebar .nav-link {
        margin: 5px 10px;
        justify-content: center;
        padding: 12px 0;
    }

    .nav-sidebar .nav-link:hover {
        color: white;
        background-color: rgba(255,255,255,0.05);
    }

    .nav-sidebar .nav-link.active {
        color: #2ecc71;
        background-color: rgba(46, 204, 113, 0.1);
        font-weight: 600;
        border-left: 3px solid #2ecc71;
    }

    .nav-link-content {
        display: flex;
        align-items: center;
    }

    .nav-sidebar .nav-link i {
        margin-right: 12px;
        width: 22px;
        text-align: center;
        font-size: 1.1rem;
        transition: color 0.2s ease;
    }
    body.sidebar-collapsed .nav-sidebar .nav-link i { margin-right: 0; font-size: 1.3rem; }

    .nav-sidebar .nav-link:hover i { color: white; }
    .nav-sidebar .nav-link.active i { color: #2ecc71; }

    .badge-menu {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 700;
    }

    .nav-link-danger { color: #e74c3c !important; }
    .nav-link-danger:hover { background-color: rgba(231, 76, 60, 0.1) !important; color: #ff6b6b !important; }
    .nav-link-danger.active { border-left-color: #e74c3c !important; color: #e74c3c !important; }
    .nav-link-danger i { color: #e74c3c !important; }

    /* Tarjeta Usuario (Pie del Sidebar) */
    .sidebar-user-card {
        padding: 15px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        background: rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .sidebar-user-card:hover { background: rgba(0, 0, 0, 0.3); }
    body.sidebar-collapsed .sidebar-user-card { justify-content: center; padding: 15px 5px; }

    .sidebar-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2ecc71, #27ae60);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
        flex-shrink: 0;
    }
    .sidebar-user-info { overflow: hidden; }
    .sidebar-user-name { color: white; font-weight: 600; font-size: 0.85rem; margin: 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
    .sidebar-user-role { color: rgba(255, 255, 255, 0.5); font-size: 0.75rem; margin: 0; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }

    /* Tooltips nativos estilizados para modo colapsado */
    [data-tooltip] {
        position: relative;
    }
    body.sidebar-collapsed [data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #2c3e50;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        white-space: nowrap;
        margin-left: 10px;
        z-index: 1001;
        pointer-events: none;
    }
</style>

<div class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fa-solid fa-leaf"></i> <span>NutriSalud</span>
        </div>
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Colapsar Menú">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
    
    <div class="sidebar-menu-wrapper">
        <ul class="nav flex-column nav-sidebar">
            
            <div class="menu-category">Gestión Diaria</div>
            
            <li class="nav-item" data-tooltip="Dashboard">
                <a class="nav-link <?= is_active('dashboard', $currentAction) ?>" href="index.php?action=dashboard">
                    <div class="nav-link-content"><i class="fa-solid fa-border-all"></i> <span class="nav-link-text">Dashboard</span></div>
                </a>
            </li>
            
            <li class="nav-item" data-tooltip="Mis Pacientes">
                <a class="nav-link <?= is_active(['listar_pacientes', 'crear_paciente', 'editar_paciente'], $currentAction) ?>" href="index.php?action=listar_pacientes">
                    <div class="nav-link-content"><i class="fa-solid fa-users"></i> <span class="nav-link-text">Mis Pacientes</span></div>
                </a>
            </li>
            
            <?php
            $totalTurnosPendientes = 0;
            if (isset($_SESSION['IdNutri'])) {
                require_once 'models/Turno.php';
                $tModel = new Turno();
                $totalTurnosPendientes = $tModel->contarPendientesHoy($_SESSION['IdNutri']);
            }
            ?>
            <li class="nav-item" data-tooltip="Turnos">
                <a class="nav-link <?= is_active(['listar_turnos', 'crear_turno'], $currentAction) ?>" href="index.php?action=listar_turnos">
                    <div class="nav-link-content"><i class="fa-solid fa-calendar-check"></i> <span class="nav-link-text">Turnos</span></div>
                    <?php if ($totalTurnosPendientes > 0): ?>
                        <span class="badge bg-success badge-menu"><?= $totalTurnosPendientes ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <div class="menu-category">Herramientas Clínicas</div>
            
            <li class="nav-item" data-tooltip="Planes Alimentarios">
                <a class="nav-link <?= is_active(['listar_planes', 'crear_plan', 'gestionar_detalles_plan'], $currentAction) ?>" href="index.php?action=listar_planes">
                    <div class="nav-link-content"><i class="fa-solid fa-apple-whole"></i> <span class="nav-link-text">Planes Alimentarios</span></div>
                </a>
            </li>

            <div class="menu-category">Herramientas</div>
            
            <li class="nav-item" data-tooltip="Calculadora Rápida">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalCalculadoraRapida">
                    <div class="nav-link-content"><i class="fa-solid fa-calculator"></i> <span class="nav-link-text">Calculadora Rápida</span></div>
                </a>
            </li>

            <li class="nav-item" data-tooltip="Riesgo CV (OPS)">
                <a class="nav-link" href="https://www.paho.org/cardioapp/web/#/cvrisk" target="_blank">
                    <div class="nav-link-content"><i class="fa-solid fa-heart-pulse"></i> <span class="nav-link-text">Riesgo CV (OPS)</span></div>
                </a>
            </li>

            <li class="nav-item" data-tooltip="Patrones Crecimiento OMS">
                <a class="nav-link" href="https://www.who.int/tools/child-growth-standards/standards" target="_blank">
                    <div class="nav-link-content"><i class="fa-solid fa-child"></i> <span class="nav-link-text">Curvas OMS</span></div>
                </a>
            </li>

            <li class="nav-item" data-tooltip="PubMed">
                <a class="nav-link" href="https://pubmed.ncbi.nlm.nih.gov/" target="_blank">
                    <div class="nav-link-content"><i class="fa-solid fa-book-medical"></i> <span class="nav-link-text">PubMed (Papers)</span></div>
                </a>
            </li>

            <div class="menu-category">Mi Cuenta</div>

            <li class="nav-item" data-tooltip="Mi Perfil">
                <a class="nav-link <?= is_active(['mi_perfil', 'cambiar_password'], $currentAction) ?>" href="index.php?action=mi_perfil">
                    <div class="nav-link-content"><i class="fa-solid fa-gear"></i> <span class="nav-link-text">Mi Perfil / Ajustes</span></div>
                </a>
            </li>
            
            <li class="nav-item mt-3" data-tooltip="Cerrar Sesión">
                <a class="nav-link nav-link-danger" href="index.php?action=logout">
                    <div class="nav-link-content"><i class="fa-solid fa-right-from-bracket"></i> <span class="nav-link-text">Cerrar Sesión</span></div>
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Mini User Profile Card -->
    <a href="index.php?action=mi_perfil" class="sidebar-user-card" data-tooltip="Ir a mi perfil">
        <?php if(!empty($logoUrl)): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>?v=<?= time() ?>" class="sidebar-user-avatar" style="object-fit: cover;">
        <?php else: ?>
            <div class="sidebar-user-avatar"><?= $iniciales ?></div>
        <?php endif; ?>
        <div class="sidebar-user-info">
            <p class="sidebar-user-name"><?= htmlspecialchars($nombre . ' ' . $apellido) ?></p>
            <p class="sidebar-user-role">M.P. <?= htmlspecialchars($matricula ?: 'No asignada') ?></p>
        </div>
    </a>
</div>

<script>
    // JS Vanilla para toggle del sidebar
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const body = document.body;
        
        // Cargar preferencia desde localStorage
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            body.classList.add('sidebar-collapsed');
        }
        
        toggleBtn.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
            
            // Guardar preferencia
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    });
</script>

<?php include 'views/layout/modal_calculadora.php'; ?>
<script src="public/js/calculadora_rapida.js?v=<?= time() ?>"></script>
