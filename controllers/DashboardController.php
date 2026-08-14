<?php
// controllers/DashboardController.php
require_once 'models/DashboardModel.php';
require_once 'models/Turno.php';

class DashboardController {
    private $model;
    private $turnoModel;

    public function __construct() {
        $this->model = new DashboardModel();
        $this->turnoModel = new Turno();
    }

    public function index() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        
        $turnosHoy = $this->model->contarTurnosHoy($idNutri);
        $pacientesActivos = $this->model->contarPacientesActivos($idNutri);
        $planesDisenados = $this->model->contarPlanesDisenados($idNutri);
        $turnos = $this->turnoModel->listarHoy($idNutri);

        require_once 'views/dashboard.php';
    }
}
?>
