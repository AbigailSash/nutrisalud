<?php
// index.php
// Front Controller Principal - Arquitectura MVC NutriSalud SaaS

// Cargar configuración de entorno e inicialización segura
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/preflight.php';

// 1. Cargar Controladores de la aplicación
require_once 'controllers/AuthController.php';
require_once 'controllers/PacienteController.php';
require_once 'controllers/PlanController.php';
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
        session_destroy();
        header("Location: index.php?action=landing");
        exit();
        break;

    // ==========================================
    // RUTAS PROTEGIDAS (Requieren Login)
    // ==========================================
    case 'dashboard':
        AuthController::verificarSesion();
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'dashboard_paciente':
        AuthController::verificarSesionPaciente();
        require_once 'controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->dashboard_paciente();
        break;

    case 'mi_plan':
        AuthController::verificarSesionPaciente();
        require_once 'controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->mi_plan();
        break;

    case 'mi_perfil_paciente':
        AuthController::verificarSesionPaciente();
        require_once 'controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->mi_perfil_paciente();
        break;

    case 'actualizar_mi_perfil_paciente':
        AuthController::verificarSesionPaciente();
        require_once 'controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->actualizar_mi_perfil_paciente();
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

    // -- ABM DE PACIENTES --
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
        require_once 'controllers/PacienteController.php';
        $controller = new PacienteController();
        $controller->imprimir_ficha_medica();
        break;

    // -- ABM DE TURNOS --
    case 'listar_turnos':
        AuthController::verificarSesion();
        $controller = new TurnoController();
        $controller->listar_turnos();
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
        // Puede ser visto por paciente o nutricionista
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
        
    case 'guardar_turno_paciente':
        AuthController::verificarSesionPaciente();
        require_once 'controllers/TurnoController.php';
        $controller = new TurnoController();
        $controller->guardar_turno_paciente();
        break;

    // -- MÓDULO DE PLANES ALIMENTARIOS --
    case 'listar_planes':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->listar_planes();
        break;

    case 'crear_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->crear_plan();
        break;
        
    case 'gestionar_detalles_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->gestionar_detalles_plan();
        break;

    case 'agregar_detalle_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->agregar_detalle_plan();
        break;

    case 'eliminar_detalle_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->eliminar_detalle_plan();
        break;

    case 'eliminar_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->eliminar_plan();
        break;

    case 'imprimir_plan':
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->imprimir_plan();
        break;

    case 'guardar_recomendaciones_plan':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->guardar_recomendaciones_plan();
        break;



    case 'api_buscar_alimento':
        AuthController::verificarSesion();
        require_once 'controllers/PlanAlimentarioController.php';
        $controller = new PlanAlimentarioController();
        $controller->api_buscar_alimento();
        break;

    // ==========================================
    // RUTAS ADMIN
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
        echo "<h1 style='text-align:center; margin-top: 50px; font-family: sans-serif; color: #2ecc71;'>404 - Acción no encontrada</h1>";
        break;
}
?>
