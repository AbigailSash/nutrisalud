<?php
require_once 'models/PlanAlimentario.php';

class PlanController {
    private $model;

    public function __construct() {
        $this->model = new PlanAlimentario();
    }

    public function crear_plan() {
        // Despacha a la vista requerida: views/planes/disenar.php
        require_once 'views/planes/disenar.php';
    }

    public function guardar_plan() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Recibir los valores desde el formulario disenar.php
            $idPlan = $_POST['id_plan'] ?? 1; // Asumiendo un plan temporal o enviado por hidden
            $idDia = $_POST['id_dia'] ?? 1;
            $idMomento = $_POST['id_momento'] ?? 1;
            $alimentoLibre = $_POST['alimento'] ?? ''; // Texto libre desde el Datalist/Input
            $cantidad = $_POST['cantidad'] ?? 0;
            
            // Lógica para guardar un alimento atómico en el grid
            if (!empty($alimentoLibre) && $cantidad > 0) {
                $this->model->agregarAlimentoAlDetalle($idPlan, $idDia, $idMomento, $alimentoLibre, $cantidad);
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_planes");
                exit();
            } else {
                echo "Error: Debes especificar un alimento y una cantidad.";
            }
        }
    }
}
?>
