<?php
require_once 'config/Conexion.php';

class Turno {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function agendar($fecha, $hora, $idPaciente, $idNutri) {
        $estado = 'Pendiente';
        $sql = "INSERT INTO Turno (Fecha, Hora, Estado_Turno, IdPaciente, IdNutri) 
                VALUES (:fecha, :hora, :estado, :id_paciente, :id_nutri)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarHoy($idNutri) {
        $sql = "SELECT t.*, p.Nombre, p.Apellido, p.Fecha_Nacimiento, p.Obra_Social,
                TIMESTAMPDIFF(YEAR, p.Fecha_Nacimiento, CURDATE()) AS Edad_Calculada
                FROM Turno t 
                JOIN Paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdNutri = :id_nutri AND t.Fecha = CURDATE() 
                ORDER BY t.Hora ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function leerPorNutricionista($idNutri) {
        $sql = "SELECT t.*, p.Nombre as PacienteNombre, p.Apellido as PacienteApellido 
                FROM Turno t 
                JOIN Paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdNutri = :id_nutri 
                ORDER BY t.Fecha DESC, t.Hora DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($idTurno, $idNutri) {
        $sql = "SELECT * FROM Turno WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function actualizar($idTurno, $fecha, $hora, $estado, $idNutri) {
        $sql = "UPDATE Turno 
                SET Fecha = :fecha, Hora = :hora, Estado_Turno = :estado 
                WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($idTurno, $idNutri) {
        $sql = "DELETE FROM Turno WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerProximoParaPaciente($idPaciente) {
        $sql = "SELECT * FROM Turno WHERE IdPaciente = :id_paciente AND Fecha >= CURDATE() ORDER BY Fecha ASC, Hora ASC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function leerPorPaciente($idPaciente) {
        $sql = "SELECT * FROM Turno WHERE IdPaciente = :id_paciente ORDER BY Fecha DESC, Hora DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPendientesHoy($idNutri) {
        $sql = "SELECT COUNT(*) as total FROM Turno WHERE IdNutri = :id_nutri AND Fecha = CURDATE() AND Estado_Turno = 'Pendiente'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'] ?? 0;
    }
}
?>
