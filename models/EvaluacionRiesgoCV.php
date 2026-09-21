<?php
// models/EvaluacionRiesgoCV.php
// Modelo para la persistencia y gestión de evaluaciones de Riesgo Cardiovascular a 10 Años (HEARTS / OMS).

require_once __DIR__ . '/../config/Conexion.php';

class EvaluacionRiesgoCV {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Guardar una nueva evaluación de riesgo cardiovascular asegurando aislamiento multi-tenant.
     *
     * @param array $datos
     * @return int|false ID del registro creado o false en caso de error.
     */
    public function guardar(array $datos) {
        try {
            $idPaciente = (int)($datos['id_paciente'] ?? 0);
            $idNutri = (int)($datos['id_nutri'] ?? 0);

            if ($idPaciente <= 0 || $idNutri <= 0) {
                return false;
            }

            // 1. Validar que el paciente pertenezca legítimamente al nutricionista
            $stmtPac = $this->pdo->prepare("SELECT IdPaciente FROM paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri LIMIT 1");
            $stmtPac->execute([
                ':id_paciente' => $idPaciente,
                ':id_nutri' => $idNutri
            ]);
            if (!$stmtPac->fetch()) {
                return false; // Violación de seguridad multi-tenant
            }

            // 2. Insertar registro
            $sql = "INSERT INTO evaluacion_riesgo_cv 
                    (id_paciente, id_nutri, fecha_evaluacion, antecedente_ecv, antecedente_erc, diabetes, tabaquismo, 
                     edad, sexo, presion_sistolica, con_colesterol, colesterol_total, peso, altura, imc, 
                     porcentaje_riesgo, categoria_riesgo, recomendacion_terapeutica)
                    VALUES 
                    (:id_paciente, :id_nutri, NOW(), :ecv, :erc, :diabetes, :tabaquismo,
                     :edad, :sexo, :pas, :con_col, :col, :peso, :altura, :imc,
                     :pct_riesgo, :cat_riesgo, :recom)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_paciente' => $idPaciente,
                ':id_nutri'    => $idNutri,
                ':ecv'         => !empty($datos['antecedente_ecv']) ? 1 : 0,
                ':erc'         => !empty($datos['antecedente_erc']) ? 1 : 0,
                ':diabetes'    => !empty($datos['diabetes']) ? 1 : 0,
                ':tabaquismo'  => !empty($datos['tabaquismo']) ? 1 : 0,
                ':edad'        => (int)($datos['edad'] ?? 0),
                ':sexo'        => strtoupper($datos['sexo'] ?? 'M') === 'F' ? 'F' : 'M',
                ':pas'         => (int)($datos['presion_sistolica'] ?? 120),
                ':con_col'     => !empty($datos['con_colesterol']) ? 1 : 0,
                ':col'         => (isset($datos['colesterol_total']) && $datos['colesterol_total'] !== '') ? (float)$datos['colesterol_total'] : null,
                ':peso'        => (isset($datos['peso']) && $datos['peso'] !== '') ? (float)$datos['peso'] : null,
                ':altura'      => (isset($datos['altura']) && $datos['altura'] !== '') ? (float)$datos['altura'] : null,
                ':imc'         => (isset($datos['imc']) && $datos['imc'] !== '') ? (float)$datos['imc'] : null,
                ':pct_riesgo'  => $datos['porcentaje_riesgo'] ?? '< 5%',
                ':cat_riesgo'  => $datos['categoria_riesgo'] ?? 'Bajo',
                ':recom'       => $datos['recomendacion_terapeutica'] ?? ''
            ]);

            return (int)$this->pdo->lastInsertId();
        } catch (Throwable $e) {
            error_log("Error en EvaluacionRiesgoCV::guardar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener la evaluación más reciente de un paciente
     */
    public function obtenerUltimaPorPaciente($idPaciente, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT * FROM evaluacion_riesgo_cv 
                                     WHERE id_paciente = :id_paciente AND id_nutri = :id_nutri 
                                     ORDER BY fecha_evaluacion DESC, id DESC LIMIT 1");
        $stmt->execute([
            ':id_paciente' => (int)$idPaciente,
            ':id_nutri' => (int)$idNutri
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Listar todo el historial de evaluaciones de un paciente
     */
    public function listarPorPaciente($idPaciente, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT * FROM evaluacion_riesgo_cv 
                                     WHERE id_paciente = :id_paciente AND id_nutri = :id_nutri 
                                     ORDER BY fecha_evaluacion DESC, id DESC");
        $stmt->execute([
            ':id_paciente' => (int)$idPaciente,
            ':id_nutri' => (int)$idNutri
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una evaluación específica por su ID
     */
    public function obtenerPorId($id, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT e.*, p.Nombre AS PacienteNombre, p.Apellido AS PacienteApellido, p.DNI AS PacienteDNI
                                     FROM evaluacion_riesgo_cv e
                                     JOIN paciente p ON e.id_paciente = p.IdPaciente
                                     WHERE e.id = :id AND e.id_nutri = :id_nutri 
                                     LIMIT 1");
        $stmt->execute([
            ':id' => (int)$id,
            ':id_nutri' => (int)$idNutri
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Eliminar una evaluación verificando pertenencia
     */
    public function eliminar($id, $idNutri) {
        $stmt = $this->pdo->prepare("DELETE FROM evaluacion_riesgo_cv WHERE id = :id AND id_nutri = :id_nutri");
        return $stmt->execute([
            ':id' => (int)$id,
            ':id_nutri' => (int)$idNutri
        ]);
    }
}
