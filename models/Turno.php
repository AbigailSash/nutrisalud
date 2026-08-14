<?php
// models/Turno.php
require_once 'config/Conexion.php';

class Turno {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function agendar($fecha, $hora, $idPaciente, $idNutri) {
        $estado = 'Pendiente';
        $sql = "INSERT INTO turno (Fecha, Hora, Estado_Turno, IdPaciente, IdNutri) 
                VALUES (:fecha, :hora, :estado, :id_paciente, :id_nutri)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarHoy($idNutri) {
        $sql = "SELECT t.*, p.Nombre, p.Apellido, p.Fecha_Nacimiento, p.Obra_Social,
                TIMESTAMPDIFF(YEAR, p.Fecha_Nacimiento, CURDATE()) AS Edad_Calculada
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdNutri = :id_nutri AND t.Fecha = CURDATE() 
                ORDER BY t.Hora ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function leerPorNutricionista($idNutri) {
        $sql = "SELECT t.*, p.Nombre as PacienteNombre, p.Apellido as PacienteApellido 
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdNutri = :id_nutri 
                ORDER BY t.Fecha DESC, t.Hora DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idTurno, $idNutri) {
        $sql = "SELECT * FROM turno WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($idTurno, $fecha, $hora, $estado, $idNutri) {
        $sql = "UPDATE turno 
                SET Fecha = :fecha, Hora = :hora, Estado_Turno = :estado 
                WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelarPorPaciente($idTurno, $idPaciente) {
        $sql = "UPDATE turno 
                SET Estado_Turno = 'Cancelado' 
                WHERE IdTurno = :id_turno AND IdPaciente = :id_paciente";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($idTurno, $idNutri) {
        $sql = "DELETE FROM turno WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerProximoParaPaciente($idPaciente) {
        $sql = "SELECT * FROM turno 
                WHERE IdPaciente = :id_paciente AND Fecha >= CURDATE() AND Estado_Turno != 'Cancelado' 
                ORDER BY Fecha ASC, Hora ASC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function leerPorPaciente($idPaciente) {
        $sql = "SELECT * FROM turno WHERE IdPaciente = :id_paciente ORDER BY Fecha DESC, Hora DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPendientesHoy($idNutri) {
        $sql = "SELECT COUNT(*) as total FROM turno WHERE IdNutri = :id_nutri AND Fecha = CURDATE() AND Estado_Turno = 'Pendiente'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
