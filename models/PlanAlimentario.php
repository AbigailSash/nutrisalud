<?php
// models/PlanAlimentario.php
require_once 'config/Conexion.php';

class PlanAlimentario {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    /**
     * 1. Crea el registro maestro del Plan Alimentario.
     * Retorna el ID generado para poder enlazar los detalles.
     */
    public function crearEncabezado($nombrePlan, $fechaInicio, $fechaFin, $objetivo, $idPaciente) {
        // 1. Pasar todos los planes anteriores a histórico
        $sqlHist = "UPDATE plan_alimentario SET Estado_Plan = 'Historico' WHERE IdPaciente = :id_paciente";
        $stmtHist = $this->conexion->prepare($sqlHist);
        $stmtHist->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmtHist->execute();

        // 2. Insertar el nuevo plan como activo
        $sql = "INSERT INTO plan_alimentario (Nombre_Plan, Fecha_Inicio, Fecha_Fin, Objetivo, IdPaciente, Estado_Plan) 
                VALUES (:nombre_plan, :fecha_inicio, :fecha_fin, :objetivo, :id_paciente, 'Activo')";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_plan', $nombrePlan, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fechaFin, PDO::PARAM_STR);
        $stmt->bindParam(':objetivo', $objetivo, PDO::PARAM_STR);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conexion->lastInsertId();
        }
        return false;
    }

    /**
     * 2. Inserta en la tabla intermedia (Intersección Atómica).
     * Cruza el Plan con el Día, el Momento y el Alimento específico.
     */
    public function agregarAlimentoAlDetalle($idPlan, $idDia, $idMomento, $alimentoLibre, $cantidadGramos, $indicaciones = "") {
        $sql = "INSERT INTO detalle_plan_alimento 
                (IdPlan, IdDia, IdMomento, IdAlimento, Alimento_Personalizado, Cantidad_Gramos, Indicaciones_Especiales) 
                VALUES (:id_plan, :id_dia, :id_momento, NULL, :alimento_libre, :cantidad, :indicaciones)";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->bindParam(':id_dia', $idDia, PDO::PARAM_INT);
        $stmt->bindParam(':id_momento', $idMomento, PDO::PARAM_INT);
        $stmt->bindParam(':alimento_libre', $alimentoLibre, PDO::PARAM_STR);
        $stmt->bindParam(':cantidad', $cantidadGramos, PDO::PARAM_STR);
        $stmt->bindParam(':indicaciones', $indicaciones, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * 3. Obtiene el plan completo resuelto para el Nutricionista/Paciente.
     */
    public function obtenerPlanCompleto($idPaciente) {
        $sql = "SELECT v.* FROM vista_menu_paciente v
                INNER JOIN plan_alimentario p ON v.IdPlan = p.IdPlan
                WHERE p.IdPaciente = :id_paciente
                ORDER BY v.IdDia ASC, v.IdMomento ASC";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorNutricionista($idNutri) {
        $sql = "SELECT p.*, pac.Nombre, pac.Apellido 
                FROM plan_alimentario p 
                JOIN paciente pac ON p.IdPaciente = pac.IdPaciente 
                WHERE pac.IdNutri = :id_nutri
                ORDER BY p.Fecha_Inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idPlan) {
        $sql = "SELECT p.*, pac.Nombre, pac.Apellido, pac.IdNutri 
                FROM plan_alimentario p 
                JOIN paciente pac ON p.IdPaciente = pac.IdPaciente 
                WHERE p.IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesPlan($idPlan) {
        $sql = "SELECT v.* 
                FROM vista_menu_paciente v
                WHERE v.IdPlan = :id_plan
                ORDER BY v.IdDia ASC, v.IdMomento ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarDetalle($idDetalle) {
        $sql = "DELETE FROM detalle_plan_alimento WHERE IdDetalle = :id_detalle";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_detalle', $idDetalle, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarPlan($idPlan) {
        $sql = "DELETE FROM plan_alimentario WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPlanActivo($idPaciente) {
        $sql = "SELECT p.* FROM plan_alimentario p 
                WHERE p.IdPaciente = :id_paciente AND p.Estado_Plan = 'Activo' 
                ORDER BY p.IdPlan DESC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesPlanHoy($idPlan, $idDiaSemana) {
        $sql = "SELECT v.* 
                FROM vista_menu_paciente v
                WHERE v.IdPlan = :id_plan AND v.IdDia = :id_dia
                ORDER BY v.IdMomento ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->bindParam(':id_dia', $idDiaSemana, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function vincularInformes($idPlan, $informes_ids) {
        $sql = "DELETE FROM plan_informe_educativo WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();

        if (!empty($informes_ids) && is_array($informes_ids)) {
            $sqlInsert = "INSERT INTO plan_informe_educativo (IdPlan, IdInforme) VALUES (:id_plan, :id_informe)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            foreach ($informes_ids as $idInforme) {
                $stmtInsert->execute([':id_plan' => $idPlan, ':id_informe' => $idInforme]);
            }
        }
    }

    public function obtenerInformesVinculados($idPlan) {
        $sql = "SELECT i.* FROM informe_educativo i
                JOIN plan_informe_educativo pi ON i.IdInforme = pi.IdInforme
                WHERE pi.IdPlan = :id_plan
                ORDER BY i.Fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarRecomendaciones($idPlan, $recomendaciones) {
        $sql = "UPDATE plan_alimentario SET Recomendaciones = :recom WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':recom', $recomendaciones, PDO::PARAM_STR);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
