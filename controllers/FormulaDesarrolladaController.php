<?php
// controllers/FormulaDesarrolladaController.php
// Controlador para el Módulo Clínico de Fórmula Desarrollada, Balance Nutricional SARA 2 y Analítica Visual.

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../models/Sara2Alimento.php';
require_once __DIR__ . '/../models/FormulaDesarrollada.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Nutricionista.php';

class FormulaDesarrolladaController {
    private $alimentoModel;
    private $formulaModel;
    private $pacienteModel;
    private $nutriModel;

    public function __construct() {
        $this->alimentoModel = new Sara2Alimento();
        $this->formulaModel = new FormulaDesarrollada();
        $this->pacienteModel = new Paciente();
        $this->nutriModel = new Nutricionista();
    }

    /**
     * Vista principal interactiva de la planilla de Fórmula Desarrollada SARA 2.
     */
    public function index() {
        AuthController::verificarSesion();
        $idNutri = (int)($_SESSION['IdNutri'] ?? 0);

        // Listar todos los pacientes del profesional
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);

        // Paciente y fórmula preseleccionados (si vienen por GET)
        $idPacienteSeleccionado = (int)($_GET['id_paciente'] ?? 0);
        $idFormulaSeleccionada = (int)($_GET['id_formula'] ?? 0);

        $formulaActual = null;
        if ($idFormulaSeleccionada > 0) {
            $formulaActual = $this->formulaModel->obtenerPorId($idFormulaSeleccionada, $idNutri);
            if ($formulaActual) {
                $idPacienteSeleccionado = (int)$formulaActual['id_paciente'];
            }
        } elseif ($idPacienteSeleccionado > 0) {
            $formulaActual = $this->formulaModel->obtenerUltimaPorPaciente($idPacienteSeleccionado, $idNutri);
        }

        $pacienteActual = null;
        if ($idPacienteSeleccionado > 0) {
            $pacienteActual = $this->pacienteModel->leerUno($idPacienteSeleccionado);
        }

        // Catálogo de grupos oficiales SARA 2 y alimentos iniciales
        $grupos = $this->alimentoModel->listarGrupos();
        $alimentosBase = $this->alimentoModel->listarTodos();

        require_once __DIR__ . '/../views/formula/index.php';
    }

    /**
     * Vista imprimible / PDF en formato institucional A4 membretado.
     */
    public function imprimir() {
        AuthController::verificarSesion();
        $idNutri = (int)($_SESSION['IdNutri'] ?? 0);
        $idFormula = (int)($_GET['id'] ?? 0);

        if ($idFormula <= 0) {
            die("Fórmula no especificada.");
        }

        $formula = $this->formulaModel->obtenerPorId($idFormula, $idNutri);
        if (!$formula) {
            die("No se encontró la fórmula o no tienes permisos para visualizarla.");
        }

        $nutri = $this->nutriModel->obtenerPorId($idNutri);
        require_once __DIR__ . '/../views/formula/imprimir.php';
    }

    /**
     * API JSON: Búsqueda reactiva de alimentos SARA 2 con filtro de grupo y término de búsqueda
     */
    public function api_buscar_sara2() {
        AuthController::verificarSesion();
        header('Content-Type: application/json; charset=utf-8');

        $query = trim($_GET['q'] ?? '');
        $grupoId = !empty($_GET['grupo_id']) ? (int)$_GET['grupo_id'] : null;
        $alimentos = $this->alimentoModel->buscar($query, $grupoId, 50);

        echo json_encode(['success' => true, 'data' => $alimentos], JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Alias para compatibilidad de rutas
     */
    public function api_buscar_alimentos() {
        $this->api_buscar_sara2();
    }

    /**
     * API JSON: Guardar fórmula desarrollada (cabecera + detalles) con momentos del día y kcal objetivo
     */
    public function api_guardar() {
        AuthController::verificarSesion();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
            exit();
        }

        $idNutri = (int)($_SESSION['IdNutri'] ?? 0);
        $rawInput = file_get_contents('php://input');
        $payload = json_decode($rawInput, true);

        if (!$payload) {
            $payload = $_POST;
        }

        $idPaciente = (int)($payload['id_paciente'] ?? 0);
        $nombreFormula = trim($payload['nombre_formula'] ?? ($payload['nombre_plan'] ?? 'Plan Nutricional SARA 2'));
        $kcalObjetivo = !empty($payload['kcal_objetivo']) ? (float)$payload['kcal_objetivo'] : null;
        $observaciones = trim($payload['observaciones'] ?? '');
        $detalles = $payload['detalles'] ?? [];
        $idFormulaExistente = !empty($payload['id_formula']) ? (int)$payload['id_formula'] : null;

        if ($idPaciente <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Debes seleccionar un paciente válido.']);
            exit();
        }

        if (empty($detalles) || !is_array($detalles)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'La planilla debe contener al menos un alimento con cantidad en gramos mayor a 0.']);
            exit();
        }

        // Sanitizar detalles
        $detallesSanitizados = [];
        $momentosValidos = ['Desayuno', 'Media Mañana', 'Almuerzo', 'Merienda', 'Cena', 'Colación'];

        foreach ($detalles as $det) {
            $idAlimento = (int)($det['id_alimento'] ?? 0);
            $gramos = (float)($det['gramos'] ?? 0);
            $momento = trim($det['momento_dia'] ?? 'Almuerzo');
            if (!in_array($momento, $momentosValidos)) {
                $momento = 'Almuerzo';
            }

            if ($idAlimento > 0 && $gramos > 0) {
                $detallesSanitizados[] = [
                    'id_alimento' => $idAlimento,
                    'momento_dia' => $momento,
                    'gramos' => $gramos
                ];
            }
        }

        if (empty($detallesSanitizados)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No hay alimentos válidos con peso mayor a 0 gramos.']);
            exit();
        }

        $idGuardado = $this->formulaModel->guardar(
            $idPaciente,
            $idNutri,
            $nombreFormula,
            $observaciones,
            $detallesSanitizados,
            $idFormulaExistente,
            $kcalObjetivo
        );

        if ($idGuardado) {
            echo json_encode([
                'success' => true,
                'id_formula' => $idGuardado,
                'message' => 'Fórmula Desarrollada SARA 2 guardada exitosamente.'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al guardar la fórmula en la base de datos o fallo de permisos.'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * API JSON: Obtener datos completos de una fórmula por ID o por Paciente
     */
    public function api_obtener_formula() {
        AuthController::verificarSesion();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = (int)($_SESSION['IdNutri'] ?? 0);
        $idFormula = (int)($_GET['id_formula'] ?? 0);
        $idPaciente = (int)($_GET['id_paciente'] ?? 0);

        $formula = null;
        if ($idFormula > 0) {
            $formula = $this->formulaModel->obtenerPorId($idFormula, $idNutri);
        } elseif ($idPaciente > 0) {
            $formula = $this->formulaModel->obtenerUltimaPorPaciente($idPaciente, $idNutri);
        }

        if ($formula) {
            echo json_encode(['success' => true, 'data' => $formula], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró ninguna fórmula registrada.'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * API JSON: Listar historial de fórmulas de un paciente
     */
    public function api_listar_historial() {
        AuthController::verificarSesion();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = (int)($_SESSION['IdNutri'] ?? 0);
        $idPaciente = (int)($_GET['id_paciente'] ?? 0);

        if ($idPaciente <= 0) {
            echo json_encode(['success' => false, 'error' => 'Paciente no especificado.'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $historial = $this->formulaModel->listarPorPaciente($idPaciente, $idNutri);
        echo json_encode(['success' => true, 'data' => $historial], JSON_UNESCAPED_UNICODE);
        exit();
    }
}
