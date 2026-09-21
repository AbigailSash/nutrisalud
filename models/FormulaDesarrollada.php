<?php
// models/FormulaDesarrollada.php
// Modelo para la gestión y persistencia de planillas de Fórmula Desarrollada, Balance Nutricional SARA 2 y Momentos del Día.

require_once __DIR__ . '/../config/Conexion.php';

class FormulaDesarrollada {
    private $pdo;

    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    /**
     * Guardar o actualizar una fórmula desarrollada completa en una transacción atómica.
     * Garantiza aislamiento multi-tenant verificando $idNutri y pertenencia del paciente.
     *
     * @param int $idPaciente
     * @param int $idNutri
     * @param string $nombreFormula
     * @param string $observaciones
     * @param array $detalles Array de items: [['id_alimento' => 1, 'gramos' => 150, 'momento_dia' => 'Almuerzo'], ...]
     * @param int|null $idFormulaExistente
     * @param float|null $kcalObjetivo
     * @return int|false ID de la fórmula guardada o false en caso de fallo.
     */
    public function guardar($idPaciente, $idNutri, $nombreFormula, $observaciones, array $detalles, $idFormulaExistente = null, $kcalObjetivo = null) {
        try {
            $this->pdo->beginTransaction();

            // 1. Validar que el paciente pertenezca al nutricionista
            $stmtPac = $this->pdo->prepare("SELECT IdPaciente FROM paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri LIMIT 1");
            $stmtPac->execute([
                ':id_paciente' => (int)$idPaciente,
                ':id_nutri' => (int)$idNutri
            ]);
            if (!$stmtPac->fetch()) {
                $this->pdo->rollBack();
                return false; // Violación de aislamiento multi-tenant
            }

            $idFormula = null;
            $kcalObjVal = (!empty($kcalObjetivo) && (float)$kcalObjetivo > 0) ? (float)$kcalObjetivo : null;

            if (!empty($idFormulaExistente) && (int)$idFormulaExistente > 0) {
                // Actualizar cabecera existente
                $stmtUpd = $this->pdo->prepare("UPDATE formula_desarrollada_cabecera 
                                                SET nombre_formula = :nombre, kcal_objetivo = :kcal_obj, observaciones = :obs, updated_at = NOW() 
                                                WHERE id = :id AND id_nutri = :id_nutri AND id_paciente = :id_paciente");
                $stmtUpd->execute([
                    ':nombre' => $nombreFormula,
                    ':kcal_obj' => $kcalObjVal,
                    ':obs' => $observaciones,
                    ':id' => (int)$idFormulaExistente,
                    ':id_nutri' => (int)$idNutri,
                    ':id_paciente' => (int)$idPaciente
                ]);
                $idFormula = (int)$idFormulaExistente;

                // Eliminar detalles previos para re-insertar
                $stmtDel = $this->pdo->prepare("DELETE FROM formula_desarrollada_detalle WHERE id_formula = :id_formula");
                $stmtDel->execute([':id_formula' => $idFormula]);
            } else {
                // Crear nueva cabecera
                $stmtIns = $this->pdo->prepare("INSERT INTO formula_desarrollada_cabecera 
                                                (id_paciente, id_nutri, nombre_formula, kcal_objetivo, observaciones, fecha_creacion) 
                                                VALUES (:id_paciente, :id_nutri, :nombre, :kcal_obj, :obs, NOW())");
                $stmtIns->execute([
                    ':id_paciente' => (int)$idPaciente,
                    ':id_nutri' => (int)$idNutri,
                    ':nombre' => $nombreFormula,
                    ':kcal_obj' => $kcalObjVal,
                    ':obs' => $observaciones
                ]);
                $idFormula = (int)$this->pdo->lastInsertId();
            }

            // 2. Insertar detalles
            if (!empty($detalles) && $idFormula > 0) {
                $stmtDet = $this->pdo->prepare("INSERT INTO formula_desarrollada_detalle 
                                                (id_formula, id_alimento, momento_dia, gramos) 
                                                VALUES (:id_formula, :id_alimento, :momento_dia, :gramos)");
                $momentosValidos = ['Desayuno', 'Media Mañana', 'Almuerzo', 'Merienda', 'Cena', 'Colación'];

                foreach ($detalles as $det) {
                    $idAlimento = (int)($det['id_alimento'] ?? 0);
                    $gramos = (float)($det['gramos'] ?? 0);
                    $momento = $det['momento_dia'] ?? 'Almuerzo';
                    if (!in_array($momento, $momentosValidos)) {
                        $momento = 'Almuerzo';
                    }

                    if ($idAlimento > 0 && $gramos > 0) {
                        $stmtDet->execute([
                            ':id_formula' => $idFormula,
                            ':id_alimento' => $idAlimento,
                            ':momento_dia' => $momento,
                            ':gramos' => $gramos
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return $idFormula;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error en FormulaDesarrollada::guardar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener una fórmula completa por su ID con todos sus renglones, momentos del día y datos químicos SARA 2.
     */
    public function obtenerPorId($idFormula, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT f.*, p.Nombre AS PacienteNombre, p.Apellido AS PacienteApellido, p.DNI AS PacienteDNI,
                                            p.Peso AS PacientePeso, p.Estatura AS PacienteEstatura, p.Sexo AS PacienteSexo, p.Fecha_Nacimiento,
                                            p.Obra_Social, p.Telefono, p.Email
                                     FROM formula_desarrollada_cabecera f
                                     JOIN paciente p ON f.id_paciente = p.IdPaciente
                                     WHERE f.id = :id AND f.id_nutri = :id_nutri
                                     LIMIT 1");
        $stmt->execute([
            ':id' => (int)$idFormula,
            ':id_nutri' => (int)$idNutri
        ]);
        $formula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$formula) {
            return null;
        }

        // Obtener detalles con datos químicos del alimento SARA 2 y momento del día
        $stmtDet = $this->pdo->prepare("SELECT d.id AS detalle_id, d.momento_dia, d.gramos, a.* 
                                        FROM formula_desarrollada_detalle d
                                        JOIN sara2_alimentos a ON d.id_alimento = a.id
                                        WHERE d.id_formula = :id_formula
                                        ORDER BY FIELD(d.momento_dia, 'Desayuno', 'Media Mañana', 'Almuerzo', 'Merienda', 'Cena', 'Colación'), d.id ASC");
        $stmtDet->execute([':id_formula' => (int)$idFormula]);
        $formula['detalles'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        return $formula;
    }

    /**
     * Obtener la última fórmula guardada de un paciente.
     */
    public function obtenerUltimaPorPaciente($idPaciente, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT id FROM formula_desarrollada_cabecera 
                                     WHERE id_paciente = :id_paciente AND id_nutri = :id_nutri 
                                     ORDER BY id DESC LIMIT 1");
        $stmt->execute([
            ':id_paciente' => (int)$idPaciente,
            ':id_nutri' => (int)$idNutri
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->obtenerPorId($row['id'], $idNutri);
        }
        return null;
    }

    /**
     * Listar el historial de fórmulas de un paciente.
     */
    public function listarPorPaciente($idPaciente, $idNutri) {
        $stmt = $this->pdo->prepare("SELECT id, nombre_formula, kcal_objetivo, fecha_creacion, observaciones 
                                     FROM formula_desarrollada_cabecera 
                                     WHERE id_paciente = :id_paciente AND id_nutri = :id_nutri 
                                     ORDER BY id DESC");
        $stmt->execute([
            ':id_paciente' => (int)$idPaciente,
            ':id_nutri' => (int)$idNutri
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Eliminar una fórmula asegurando pertenencia.
     */
    public function eliminar($idFormula, $idNutri) {
        $stmt = $this->pdo->prepare("DELETE FROM formula_desarrollada_cabecera WHERE id = :id AND id_nutri = :id_nutri");
        return $stmt->execute([
            ':id' => (int)$idFormula,
            ':id_nutri' => (int)$idNutri
        ]);
    }
}
