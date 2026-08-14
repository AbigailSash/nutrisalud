<?php
// models/PlanAlimentario.php
require_once 'config/Conexion.php';

class PlanAlimentario {
    private $conexion;

    public function __construct() {
        // Solicitamos la conexión segura al instanciar el modelo
        $this->conexion = Conexion::conectar();
    }

    /**
     * 1. Crea el registro maestro del Plan Alimentario.
     * Retorna el ID generado para poder enlazar los detalles.
     */
    public function crearEncabezado($nombrePlan, $fechaInicio, $fechaFin, $objetivo, $idPaciente) {
        // 1. Pasar todos los planes anteriores a histórico
        $sqlHist = "UPDATE Plan_Alimentario SET Estado_Plan = 'Historico' WHERE IdPaciente = :id_paciente";
        $stmtHist = $this->conexion->prepare($sqlHist);
        $stmtHist->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmtHist->execute();

        // 2. Insertar el nuevo plan como activo
        $sql = "INSERT INTO Plan_Alimentario (Nombre_Plan, Fecha_Inicio, Fecha_Fin, Objetivo, IdPaciente, Estado_Plan) 
                VALUES (:nombre_plan, :fecha_inicio, :fecha_fin, :objetivo, :id_paciente, 'Activo')";
        
        $stmt = $this->conexion->prepare($sql);
        
        // Bind de parámetros estricto
        $stmt->bindParam(':nombre_plan', $nombrePlan, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_inicio', $fechaInicio, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_fin', $fechaFin, PDO::PARAM_STR);
        $stmt->bindParam(':objetivo', $objetivo, PDO::PARAM_STR);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Retorna la Llave Primaria (IdPlan) recién insertada
            return $this->conexion->lastInsertId();
        }
        return false;
    }

    /**
     * 2. Inserta en la tabla intermedia (Intersección Atómica).
     * Cruza el Plan con el Día, el Momento y el Alimento específico.
     */
    public function agregarAlimentoAlDetalle($idPlan, $idDia, $idMomento, $alimentoLibre, $cantidadGramos, $indicaciones = "") {
        $sql = "INSERT INTO Detalle_Plan_Alimento 
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
     * Asume el uso de la vista 'Vista_Menu_Paciente' sugerida en la base de datos.
     */
    public function obtenerPlanCompleto($idPaciente) {
        // Hacemos INNER JOIN con Plan_Alimentario para asegurar que el plan pertenece a este paciente
        $sql = "SELECT v.* FROM Vista_Menu_Paciente v
                INNER JOIN Plan_Alimentario p ON v.IdPlan = p.IdPlan
                WHERE p.IdPaciente = :id_paciente
                ORDER BY v.IdDia, v.IdMomento";
                
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function listarPorNutricionista($idNutri) {
        $sql = "SELECT p.*, pac.Nombre, pac.Apellido 
                FROM Plan_Alimentario p 
                JOIN Paciente pac ON p.IdPaciente = pac.IdPaciente 
                WHERE pac.IdNutri = :id_nutri
                ORDER BY p.Fecha_Inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($idPlan) {
        $sql = "SELECT p.*, pac.Nombre, pac.Apellido 
                FROM Plan_Alimentario p 
                JOIN Paciente pac ON p.IdPaciente = pac.IdPaciente 
                WHERE p.IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerDetallesPlan($idPlan) {
        $sql = "SELECT v.* 
                FROM Vista_Menu_Paciente v
                WHERE v.IdPlan = :id_plan
                ORDER BY v.IdDia, v.IdMomento";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function eliminarDetalle($idDetalle) {
        $sql = "DELETE FROM Detalle_Plan_Alimento WHERE IdDetalle = :id_detalle";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_detalle', $idDetalle, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarPlan($idPlan) {
        $sql = "DELETE FROM Plan_Alimentario WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPlanActivo($idPaciente) {
        $sql = "SELECT p.* FROM Plan_Alimentario p 
                WHERE p.IdPaciente = :id_paciente AND p.Estado_Plan = 'Activo' 
                ORDER BY p.IdPlan DESC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerDetallesPlanHoy($idPlan, $idDiaSemana) {
        $sql = "SELECT v.* 
                FROM Vista_Menu_Paciente v
                WHERE v.IdPlan = :id_plan AND v.IdDia = :id_dia
                ORDER BY v.IdMomento";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->bindParam(':id_dia', $idDiaSemana, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function vincularInformes($idPlan, $informes_ids) {
        // Primero limpiar anteriores (si es edición, aunque aquí solo creamos)
        $sql = "DELETE FROM Plan_Informe_Educativo WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id_plan' => $idPlan]);

        if (!empty($informes_ids)) {
            $sqlInsert = "INSERT INTO Plan_Informe_Educativo (IdPlan, IdInforme) VALUES (:id_plan, :id_informe)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            foreach ($informes_ids as $idInforme) {
                $stmtInsert->execute([':id_plan' => $idPlan, ':id_informe' => $idInforme]);
            }
        }
    }

    public function obtenerInformesVinculados($idPlan) {
        $sql = "SELECT i.* FROM Informe_Educativo i
                JOIN Plan_Informe_Educativo pi ON i.IdInforme = pi.IdInforme
                WHERE pi.IdPlan = :id_plan
                ORDER BY i.Fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function guardarRecomendaciones($idPlan, $recomendaciones) {
        $sql = "UPDATE Plan_Alimentario SET Recomendaciones = :recom WHERE IdPlan = :id_plan";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':recom', $recomendaciones, PDO::PARAM_STR);
        $stmt->bindParam(':id_plan', $idPlan, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
