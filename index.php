<?php
// index.php
// Front Controller Principal - Arquitectura MVC NutriSalud SaaS

// Cargar configuración de entorno e inicialización segura
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/preflight.php';

// 1. Cargar Controladores base de la aplicación
require_once 'controllers/AuthController.php';
require_once 'controllers/PacienteController.php';
require_once 'controllers/PlanAlimentarioController.php';
require_once 'controllers/TurnoController.php';
require_once 'controllers/DashboardController.php';

// 2. Capturar la acción solicitada por el usuario
$action = $_GET['action'] ?? 'landing';

// 3. Sistema de Enrutamiento (Router)
switch ($action) {
    // ==========================================
    // VISTAS PÚBLICAS Y AUTENTICACIÓN
    // ==========================================
    case 'landing':
        require_once 'views/landing.php';
        break;

    case 'login_paciente':
        $auth = new AuthController();
        $auth->mostrarLoginPaciente();
        break;

    case 'procesar_login_paciente':
        $auth = new AuthController();
        $auth->procesarLoginPaciente();
        break;

    case 'login_nutri':
        $auth = new AuthController();
        $auth->mostrarLoginNutri();
        break;
        
    case 'procesar_login_nutri':
        $auth = new AuthController();
        $auth->procesarLoginNutri();
        break;

    case 'logout':
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: index.php?action=landing");
        exit();
        break;

    // ==========================================
    // RUTAS DEL PORTAL NUTRICIONISTA (PROTEGIDAS)
    // ==========================================
    case 'dashboard':
        AuthController::verificarSesion();
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'mi_perfil':
    case 'cambiar_password':
        AuthController::verificarSesion();
        require_once 'controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->mi_perfil();
        break;
        
    case 'actualizar_perfil':
        AuthController::verificarSesion();
        require_once 'controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->actualizar_perfil();
        break;

    case 'actualizar_password':
    case 'procesar_cambiar_password':
        AuthController::verificarSesion();
        require_once 'controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->actualizar_password();
        break;

    case 'actualizar_color_tema':
        AuthController::verificarSesion();
        require_once 'controllers/PerfilController.php';
        $controller = new PerfilController();
        $controller->actualizar_color_tema();
        break;

    // -- GESTIÓN DE PACIENTES --
    case 'listar_pacientes':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->listar_pacientes();
        break;
        
    case 'crear_paciente':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->crear_paciente();
        break;
        
    case 'guardar_paciente':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->guardar_paciente();
        break;
        
    case 'editar_paciente':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->editar_paciente();
        break;

    case 'actualizar_paciente':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->actualizar_paciente();
        break;

    case 'eliminar_paciente':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->eliminar_paciente();
        break;

    case 'ver_historia_clinica':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->ver_historia_clinica();
        break;

    case 'guardar_historia_clinica':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->guardar_historia_clinica();
        break;

    case 'imprimir_ficha_medica':
        AuthController::verificarSesion();
        $controller = new PacienteController();
        $controller->imprimir_ficha_medica();
        break;

    // -- GESTIÓN DE TURNOS (PROFESIONAL) --
    case 'listar_turnos':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->listar_turnos();
        break;

    case 'agendar_turno':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->agendar_turno();
        break;
        
    case 'guardar_turno':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->guardar_turno();
        break;
        
    case 'editar_turno':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->editar_turno();
        break;

    case 'actualizar_turno':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->actualizar_turno();
        break;

    case 'eliminar_turno':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->eliminar_turno();
        break;

    // -- INFORMES EDUCATIVOS --
    case 'listar_informes':
        AuthController::verificarSesion();
        require_once 'controllers/InformeController.php';
        $controller = new InformeController();
        $controller->listar_informes();
        break;
        
    case 'crear_informe':
        AuthController::verificarSesion();
        require_once 'controllers/InformeController.php';
        $controller = new InformeController();
        $controller->crear_informe();
        break;
        
    case 'guardar_informe':
        AuthController::verificarSesion();
        require_once 'controllers/InformeController.php';
        $controller = new InformeController();
        $controller->guardar_informe();
        break;
        
    case 'ver_informe':
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['IdNutri']) && !isset($_SESSION['IdPaciente'])) {
            header("Location: index.php?action=login_nutri");
            exit();
        }
        require_once 'controllers/InformeController.php';
        $controller = new InformeController();
        $controller->ver_informe();
        break;
        
    case 'eliminar_informe':
        AuthController::verificarSesion();
        require_once 'controllers/InformeController.php';
        $controller = new InformeController();
        $controller->eliminar_informe();
        break;

    // -- PLANES ALIMENTARIOS --
    case 'listar_planes':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->listar_planes();
        break;

    case 'crear_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->crear_plan();
        break;
        
    case 'gestionar_detalles_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->gestionar_detalles_plan();
        break;

    case 'agregar_detalle_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->agregar_detalle_plan();
        break;

    case 'eliminar_detalle_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->eliminar_detalle_plan();
        break;

    case 'eliminar_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->eliminar_plan();
        break;

    case 'imprimir_plan':
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['IdNutri']) && !isset($_SESSION['IdPaciente'])) {
            header("Location: index.php?action=login_nutri");
            exit();
        }
        $controller = new PlanAlimentarioController();
        $controller->imprimir_plan();
        break;

    case 'guardar_recomendaciones_plan':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->guardar_recomendaciones_plan();
        break;

    case 'api_buscar_alimento':
        AuthController::verificarSesion();
        $controller = new PlanAlimentarioController();
        $controller->api_buscar_alimento();
        break;

    // ==========================================
    // RUTAS DEL PORTAL PACIENTE (PROTEGIDAS)
    // ==========================================
    case 'dashboard_paciente':
        AuthController::verificarSesionPaciente();
        $controller = new PacienteController();
        $controller->dashboard_paciente();
        break;

    case 'mi_plan':
        AuthController::verificarSesionPaciente();
        $controller = new PacienteController();
        $controller->mi_plan();
        break;

    case 'mi_perfil_paciente':
        AuthController::verificarSesionPaciente();
        $controller = new PacienteController();
        $controller->mi_perfil_paciente();
        break;

    case 'actualizar_mi_perfil_paciente':
        AuthController::verificarSesionPaciente();
        $controller = new PacienteController();
        $controller->actualizar_mi_perfil_paciente();
        break;

    case 'mis_turnos':
        AuthController::verificarSesionPaciente();
        $controller = new TurnoController();
        $controller->mis_turnos();
        break;
        
    case 'solicitar_turno':
        AuthController::verificarSesionPaciente();
        $controller = new TurnoController();
        $controller->solicitar_turno();
        break;

    case 'guardar_turno_paciente':
        AuthController::verificarSesionPaciente();
        $controller = new TurnoController();
        $controller->guardar_turno_paciente();
        break;

    case 'cancelar_turno_paciente':
        AuthController::verificarSesionPaciente();
        $controller = new TurnoController();
        $controller->cancelar_turno_paciente();
        break;

    // ==========================================
    // RUTAS PANEL ADMINISTRADOR MASTER
    // ==========================================
    case 'admin_dashboard':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin_nutricionistas':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->listar_nutricionistas();
        break;

    case 'api_nutricionistas':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->obtener_nutricionistas();
        break;

    case 'admin_guardar_nutricionista':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->guardar_nutricionista();
        break;

    case 'admin_estado_nutricionista':
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->cambiar_estado_nutricionista();
        break;

    // ==========================================
    // RUTEO POR DEFECTO (404)
    // ==========================================
    default:
        http_response_code(404);
        echo "<div style='font-family: system-ui, sans-serif; text-align:center; padding: 60px 20px;'>
                <h1 style='color: #2ecc71; font-size: 3rem; margin-bottom: 10px;'>404</h1>
                <h2 style='color: #1e293b; margin-bottom: 20px;'>Página o Acción no encontrada</h2>
                <p style='color: #64748b; margin-bottom: 30px;'>La ruta solicitada no existe en el sistema NutriSalud.</p>
                <a href='index.php?action=landing' style='display:inline-block; background:#2ecc71; color:white; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:600;'>Volver al Inicio</a>
              </div>";
        break;
}
?>
