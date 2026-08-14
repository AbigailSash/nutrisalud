<?php
// controllers/PlanAlimentarioController.php
require_once 'models/PlanAlimentario.php';
require_once 'models/Paciente.php';
require_once 'config/Conexion.php';

class PlanAlimentarioController {
    private $model;
    private $pacienteModel;
    private $informeModel;
    private $pdo;

    public function __construct() {
        $this->model = new PlanAlimentario();
        $this->pacienteModel = new Paciente();
        require_once 'models/InformeEducativo.php';
        $this->informeModel = new InformeEducativo();
        $this->pdo = Conexion::conectar();
    }

    public function listar_planes() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        
        $planes = $this->model->listarPorNutricionista($idNutri);
        require_once 'views/planes/index.php';
    }

    public function crear_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPaciente = $_POST['id_paciente'] ?? 0;
            $nombre = trim($_POST['nombre_plan'] ?? '');
            $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
            $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            $objetivo = trim($_POST['objetivo'] ?? '');
            
            $idPlan = $this->model->crearEncabezado($nombre, $fecha_inicio, $fecha_fin, $objetivo, $idPaciente);
            if ($idPlan) {
                $_SESSION['mensaje'] = "Plan creado exitosamente. Diseña los momentos del menú.";
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al crear el plan.";
                $_SESSION['tipo_mensaje'] = "danger";
                header("Location: index.php?action=listar_planes");
                exit();
            }
        }
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
        require_once 'views/planes/form.php';
    }

    public function gestionar_detalles_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPlan = (int)($_GET['id'] ?? 0);
        
        $plan = $this->model->obtenerPorId($idPlan);
        if (!$plan) {
            $_SESSION['mensaje'] = "El plan solicitado no existe o no tiene permiso para verlo.";
            $_SESSION['tipo_mensaje'] = "danger";
            header("Location: index.php?action=listar_planes");
            exit();
        }

        // Catálogos para los selects
        $dias = $this->pdo->query("SELECT * FROM dia_semana ORDER BY IdDia ASC")->fetchAll(PDO::FETCH_ASSOC);
        $momentos = $this->pdo->query("SELECT * FROM momento_dia ORDER BY IdMomento ASC")->fetchAll(PDO::FETCH_ASSOC);
        $alimentos = $this->pdo->query("SELECT * FROM alimento ORDER BY Nombre_Alimento ASC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        
        // Detalles actuales del plan
        $detalles = $this->model->obtenerDetallesPlan($idPlan);
        
        // Agrupar detalles por Día y Momento para la vista
        $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $planAgrupado = [];
        foreach($detalles as $d) {
            $nombreDia = $diasNombres[$d['IdDia']] ?? 'Día ' . $d['IdDia'];
            $planAgrupado[$nombreDia][$d['Momento_Comida']][] = $d;
        }

        require_once 'views/planes/detalles.php';
    }

    public function agregar_detalle_plan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idPlan = (int)($_POST['id_plan'] ?? 0);
            $idDia = (int)($_POST['id_dia'] ?? 1);
            $idMomento = (int)($_POST['id_momento'] ?? 1);
            $alimentoLibre = trim($_POST['alimento'] ?? '');
            $cantidad = trim($_POST['cantidad'] ?? '');
            $indicaciones = trim($_POST['indicaciones'] ?? '');
            
            if ($idPlan > 0 && !empty($alimentoLibre)) {
                $this->model->agregarAlimentoAlDetalle($idPlan, $idDia, $idMomento, $alimentoLibre, $cantidad, $indicaciones);
                $_SESSION['mensaje'] = "Alimento agregado al menú.";
                $_SESSION['tipo_mensaje'] = "success";
            }
            header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
            exit();
        }
    }

    public function eliminar_detalle_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idDetalle = (int)($_GET['id_detalle'] ?? 0);
        $idPlan = (int)($_GET['id_plan'] ?? 0);
        if ($idDetalle > 0) {
            $this->model->eliminarDetalle($idDetalle);
            $_SESSION['mensaje'] = "Alimento eliminado del menú.";
            $_SESSION['tipo_mensaje'] = "info";
        }
        header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
        exit();
    }

    public function eliminar_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPlan = (int)($_GET['id'] ?? 0);
        if ($idPlan > 0) {
            $this->model->eliminarPlan($idPlan);
            $_SESSION['mensaje'] = "Plan alimentario eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
        }
        header("Location: index.php?action=listar_planes");
        exit();
    }

    public function imprimir_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPlan = (int)($_GET['id_plan'] ?? 0);
        $planActivo = $this->model->obtenerPorId($idPlan);
        
        if (!$planActivo) {
            echo "Plan no encontrado.";
            return;
        }

        $detalles = $this->model->obtenerDetallesPlan($idPlan);
        $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $planAgrupado = [];
        foreach($detalles as $d) {
            $nombreDia = $diasNombres[$d['IdDia']] ?? 'Día ' . $d['IdDia'];
            $planAgrupado[$nombreDia][$d['Momento_Comida']][] = $d;
        }
        
        require_once 'views/planes/imprimir.php';
    }

    public function guardar_recomendaciones_plan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idPlan = (int)($_POST['id_plan'] ?? 0);
            $recomendaciones = trim($_POST['recomendaciones'] ?? '');
            $this->model->guardarRecomendaciones($idPlan, $recomendaciones);
            
            $_SESSION['mensaje'] = "Recomendaciones guardadas exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
            
            header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
            exit();
        }
    }

    public function api_buscar_alimento() {
        $term = trim($_GET['term'] ?? '');
        if (strlen($term) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit();
        }

        try {
            $sql = "SELECT IdAlimento, Nombre_Alimento AS Nombre, Calorias_100g AS Calorias 
                    FROM alimento 
                    WHERE Nombre_Alimento LIKE :term 
                    ORDER BY Nombre_Alimento ASC 
                    LIMIT 10";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':term' => '%' . $term . '%']);
            $alimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode($alimentos);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([]);
        }
    }
}
?>
