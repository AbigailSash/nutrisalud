<?php
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
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idPaciente = $_POST['id_paciente'];
            $nombre = $_POST['nombre_plan'];
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
            $objetivo = $_POST['objetivo'];
            
            $idPlan = $this->model->crearEncabezado($nombre, $fecha_inicio, $fecha_fin, $objetivo, $idPaciente);
            // Redirigir a los detalles para diseñar el menú
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Plan creado. Ahora puedes diseñar el menú.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
            exit();
        }
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
        require_once 'views/planes/form.php';
    }

    public function gestionar_detalles_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPlan = $_GET['id'] ?? 0;
        
        $plan = $this->model->obtenerPorId($idPlan);
        if (!$plan) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "El plan solicitado no existe o no tiene permiso para verlo.";
            $_SESSION['tipo_mensaje'] = "error";
            header("Location: index.php?action=listar_planes");
            exit();
        }

        // Catálogos para los selects
        $dias = $this->pdo->query("SELECT * FROM Dia_Semana")->fetchAll();
        $momentos = $this->pdo->query("SELECT * FROM Momento_Dia")->fetchAll();
        $alimentos = $this->pdo->query("SELECT * FROM Alimento")->fetchAll();
        
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
            $idPlan = $_POST['id_plan'];
            $idDia = $_POST['id_dia'];
            $idMomento = $_POST['id_momento'];
            $alimentoLibre = $_POST['alimento']; // Texto libre
            $cantidad = $_POST['cantidad']; // Puede ser texto como "2 unidades"
            $indicaciones = $_POST['indicaciones'] ?? '';
            
            $this->model->agregarAlimentoAlDetalle($idPlan, $idDia, $idMomento, $alimentoLibre, $cantidad, $indicaciones);
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=gestionar_detalles_plan");
            exit();
        }
    }

    public function eliminar_detalle_plan() {
        $idDetalle = $_GET['id_detalle'];
        $idPlan = $_GET['id_plan'];
        $this->model->eliminarDetalle($idDetalle);
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=gestionar_detalles_plan");
        exit();
    }

    public function eliminar_plan() {
        $idPlan = $_GET['id'];
        $this->model->eliminarPlan($idPlan);
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_planes");
        exit();
    }

    public function imprimir_plan() {
        $idPlan = $_GET['id_plan'] ?? 0;
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
            $idPlan = $_POST['id_plan'];
            $recomendaciones = $_POST['recomendaciones'] ?? '';
            $this->model->guardarRecomendaciones($idPlan, $recomendaciones);
            
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Recomendaciones/Informe guardado exitosamente.";
            $_SESSION['tipo_mensaje'] = "success";
            
            header("Location: index.php?action=gestionar_detalles_plan&id=" . $idPlan);
            exit();
        }
    }

    public function api_buscar_alimento() {
        $term = $_GET['term'] ?? '';
        if (strlen($term) < 2) {
            echo json_encode([]);
            exit();
        }

        try {
            $sql = "SELECT IdAlimento, Nombre_Alimento AS Nombre, Calorias_100g AS Calorias FROM Alimento WHERE Nombre_Alimento LIKE :term LIMIT 10";
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
