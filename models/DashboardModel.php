<?php
require_once 'config/Conexion.php';

class DashboardModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    public function contarTurnosHoy($idNutri) {
        $sql = "SELECT COUNT(*) AS total FROM turno 
                WHERE DATE(Fecha) = CURDATE() AND IdNutri = :idNutri AND Estado_Turno != 'Cancelado'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idNutri' => $idNutri]);
        return $stmt->fetchColumn();
    }

    public function contarPacientesActivos($idNutri) {
        $sql = "SELECT COUNT(*) AS total FROM paciente WHERE IdNutri = :idNutri";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idNutri' => $idNutri]);
        return $stmt->fetchColumn();
    }

    public function contarPlanesDisenados($idNutri) {
        $sql = "SELECT COUNT(*) AS total 
                FROM plan_alimentario pa 
                INNER JOIN paciente p ON pa.IdPaciente = p.IdPaciente 
                WHERE p.IdNutri = :idNutri";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['idNutri' => $idNutri]);
        return $stmt->fetchColumn();
    }
}
?>
