<?php
// models/InformeEducativo.php
require_once 'config/Conexion.php';

class InformeEducativo {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function crear($idPaciente, $idNutri, $fecha, $contenidoJson) {
        $sql = "INSERT INTO informe_educativo (IdPaciente, IdNutri, Fecha, Contenido_JSON) 
                VALUES (:id_paciente, :id_nutri, :fecha, :contenido)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':contenido', $contenidoJson, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function leerPorNutricionista($idNutri) {
        $sql = "SELECT i.*, p.Nombre, p.Apellido 
                FROM informe_educativo i 
                JOIN paciente p ON i.IdPaciente = p.IdPaciente 
                WHERE i.IdNutri = :id_nutri 
                ORDER BY i.Fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function leerPorPaciente($idPaciente) {
        $sql = "SELECT * FROM informe_educativo WHERE IdPaciente = :id_paciente ORDER BY Fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idInforme) {
        $sql = "SELECT i.*, p.Nombre, p.Apellido 
                FROM informe_educativo i 
                JOIN paciente p ON i.IdPaciente = p.IdPaciente
                WHERE i.IdInforme = :id_informe";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_informe', $idInforme, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminar($idInforme, $idNutri) {
        $sql = "DELETE FROM informe_educativo WHERE IdInforme = :id_informe AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_informe', $idInforme, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
