<?php
// models/HistoriaClinica.php
require_once 'config/Conexion.php';

class HistoriaClinica {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function obtenerPorPaciente($idPaciente, $idNutri) {
        $sql = "SELECT * FROM historia_clinica WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($idPaciente, $idNutri, $datosJSON) {
        $existente = $this->obtenerPorPaciente($idPaciente, $idNutri);
        $fechaActual = date('Y-m-d H:i:s');

        if ($existente) {
            $sql = "UPDATE historia_clinica 
                    SET Datos_JSON = :datos, FechaUltimaModificacion = :fecha 
                    WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
            $stmt = $this->conexion->prepare($sql);
        } else {
            $sql = "INSERT INTO historia_clinica (IdPaciente, IdNutri, FechaUltimaModificacion, Datos_JSON) 
                    VALUES (:id_paciente, :id_nutri, :fecha, :datos)";
            $stmt = $this->conexion->prepare($sql);
        }

        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fechaActual, PDO::PARAM_STR);
        $stmt->bindParam(':datos', $datosJSON, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function guardarCamposCustom($idPaciente, $campos) {
        // Eliminar campos custom anteriores para este paciente
        $sqlDelete = "DELETE FROM historia_clinica_campos_custom WHERE paciente_id = :id_paciente";
        $stmtDelete = $this->conexion->prepare($sqlDelete);
        $stmtDelete->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmtDelete->execute();

        // Insertar los nuevos campos
        if (!empty($campos) && is_array($campos)) {
            $sqlInsert = "INSERT INTO historia_clinica_campos_custom (paciente_id, seccion, titulo, contenido) 
                          VALUES (:id_paciente, :seccion, :titulo, :contenido)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);

            foreach ($campos as $seccion => $listaCampos) {
                if (is_array($listaCampos)) {
                    foreach ($listaCampos as $campo) {
                        if (!empty($campo['titulo']) || !empty($campo['contenido'])) {
                            $stmtInsert->bindValue(':id_paciente', $idPaciente, PDO::PARAM_INT);
                            $stmtInsert->bindValue(':seccion', $seccion, PDO::PARAM_STR);
                            $stmtInsert->bindValue(':titulo', $campo['titulo'] ?? '', PDO::PARAM_STR);
                            $stmtInsert->bindValue(':contenido', $campo['contenido'] ?? '', PDO::PARAM_STR);
                            $stmtInsert->execute();
                        }
                    }
                }
            }
        }
        return true;
    }

    public function obtenerCamposCustom($idPaciente) {
        $sql = "SELECT * FROM historia_clinica_campos_custom WHERE paciente_id = :id_paciente ORDER BY id ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $camposAgrupados = [];
        foreach ($resultados as $fila) {
            $camposAgrupados[$fila['seccion']][] = $fila;
        }
        return $camposAgrupados;
    }
}
?>
