<?php
// models/Turno.php
// Modelo de gestión de turnos y agenda médica con aislamiento Multi-Tenant estricto.

require_once 'config/Conexion.php';

class Turno {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    /**
     * Agenda un turno básico (retrocompatibilidad).
     */
    public function agendar($fecha, $hora, $idPaciente, $idNutri) {
        return $this->agendarConDetalles($fecha, $hora, $idPaciente, $idNutri, 'Presencial', 'Consulta Nutricional');
    }

    /**
     * Agenda un turno con todos los detalles de modalidad, link, dirección y notas.
     */
    public function agendarConDetalles($fecha, $hora, $idPaciente, $idNutri, $modalidad = 'Presencial', $motivo = 'Consulta Nutricional', $link = null, $direccion = null, $notas = null, $estado = 'Pendiente') {
        $sql = "INSERT INTO turno (Fecha, Hora, Estado_Turno, Modalidad, Motivo_Consulta, Link_Reunion, Direccion, Notas, IdPaciente, IdNutri) 
                VALUES (:fecha, :hora, :estado, :modalidad, :motivo, :link, :direccion, :notas, :id_paciente, :id_nutri)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':modalidad', $modalidad, PDO::PARAM_STR);
        $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
        $stmt->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':notas', $notas, PDO::PARAM_STR);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return (int)$this->conexion->lastInsertId();
        }
        return false;
    }

    /**
     * Obtiene los eventos en formato optimizado para FullCalendar v6.
     */
    public function obtenerParaCalendario($idNutri, $inicio = null, $fin = null, $filtroEstado = null) {
        $sql = "SELECT t.*, 
                       p.Nombre AS PacienteNombre, 
                       p.Apellido AS PacienteApellido, 
                       p.Telefono AS PacienteTelefono, 
                       p.Email AS PacienteEmail, 
                       p.DNI AS PacienteDNI,
                       p.Obra_Social AS PacienteObraSocial
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdNutri = :id_nutri";

        $params = [':id_nutri' => $idNutri];

        if (!empty($inicio)) {
            $sql .= " AND t.Fecha >= :inicio";
            $params[':inicio'] = date('Y-m-d', strtotime($inicio));
        }
        if (!empty($fin)) {
            $sql .= " AND t.Fecha <= :fin";
            $params[':fin'] = date('Y-m-d', strtotime($fin));
        }
        if (!empty($filtroEstado) && $filtroEstado !== 'Todos') {
            $sql .= " AND t.Estado_Turno = :estado";
            $params[':estado'] = $filtroEstado;
        }

        $sql .= " ORDER BY t.Fecha ASC, t.Hora ASC";

        $stmt = $this->conexion->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle completo de un turno con datos del paciente y profesional.
     */
    public function obtenerDetalleCompleto($idTurno, $idNutri) {
        $sql = "SELECT t.*, 
                       p.Nombre AS PacienteNombre, 
                       p.Apellido AS PacienteApellido, 
                       p.Telefono AS PacienteTelefono, 
                       p.Email AS PacienteEmail, 
                       p.DNI AS PacienteDNI,
                       p.Obra_Social AS PacienteObraSocial,
                       p.Fecha_Nacimiento AS PacienteFechaNac,
                       TIMESTAMPDIFF(YEAR, p.Fecha_Nacimiento, CURDATE()) AS PacienteEdad
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE t.IdTurno = :id_turno AND t.IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reprograma la fecha y hora de un turno (usado por Drag & Drop y edición rápida).
     */
    public function reprogramar($idTurno, $fecha, $hora, $idNutri) {
        $sql = "UPDATE turno 
                SET Fecha = :fecha, Hora = :hora 
                WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Actualiza el estado de un turno (Confirmado, Pendiente, Atendido, Cancelado).
     */
    public function actualizarEstado($idTurno, $estado, $idNutri) {
        $sql = "UPDATE turno 
                SET Estado_Turno = :estado 
                WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Actualización completa de todos los campos del turno.
     */
    public function actualizarCompleto($idTurno, $fecha, $hora, $estado, $modalidad, $motivo, $link, $direccion, $notas, $idNutri) {
        $sql = "UPDATE turno 
                SET Fecha = :fecha, Hora = :hora, Estado_Turno = :estado, Modalidad = :modalidad, 
                    Motivo_Consulta = :motivo, Link_Reunion = :link, Direccion = :direccion, Notas = :notas 
                WHERE IdTurno = :id_turno AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':hora', $hora, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindParam(':modalidad', $modalidad, PDO::PARAM_STR);
        $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
        $stmt->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':notas', $notas, PDO::PARAM_STR);
        $stmt->bindParam(':id_turno', $idTurno, PDO::PARAM_INT);
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
        $sql = "SELECT t.* 
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE (t.IdPaciente = :id_paciente OR p.DNI = (SELECT p2.DNI FROM paciente p2 WHERE p2.IdPaciente = :id_paciente2 LIMIT 1)) 
                  AND t.Fecha >= CURDATE() AND t.Estado_Turno != 'Cancelado' 
                ORDER BY t.Fecha ASC, t.Hora ASC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_paciente2', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function leerPorPaciente($idPaciente) {
        $sql = "SELECT t.* 
                FROM turno t 
                JOIN paciente p ON t.IdPaciente = p.IdPaciente 
                WHERE (t.IdPaciente = :id_paciente OR p.DNI = (SELECT p2.DNI FROM paciente p2 WHERE p2.IdPaciente = :id_paciente2 LIMIT 1)) 
                ORDER BY t.Fecha DESC, t.Hora DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_paciente2', $idPaciente, PDO::PARAM_INT);
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
