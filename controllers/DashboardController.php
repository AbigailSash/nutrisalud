<?php
require_once 'models/DashboardModel.php';

class DashboardController {
    private $model;

    public function __construct() {
        $this->model = new DashboardModel();
    }

    public function index() {
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        
        $turnosHoy = $this->model->contarTurnosHoy($idNutri);
        $pacientesActivos = $this->model->contarPacientesActivos($idNutri);
        $planesDisenados = $this->model->contarPlanesDisenados($idNutri);

        require_once 'views/dashboard.php';
    }
}
?>
