<?php
require_once 'config/Conexion.php';

class HistoriaClinica {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function obtenerPorPaciente($idPaciente, $idNutri) {
        $sql = "SELECT * FROM Historia_Clinica WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function guardar($idPaciente, $idNutri, $datosJSON) {
        // Verificar si ya existe
        $existente = $this->obtenerPorPaciente($idPaciente, $idNutri);
        $fechaActual = date('Y-m-d H:i:s');

        if ($existente) {
            $sql = "UPDATE Historia_Clinica 
                    SET Datos_JSON = :datos, FechaUltimaModificacion = :fecha 
                    WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
            $stmt = $this->conexion->prepare($sql);
        } else {
            $sql = "INSERT INTO Historia_Clinica (IdPaciente, IdNutri, FechaUltimaModificacion, Datos_JSON) 
                    VALUES (:id_paciente, :id_nutri, :fecha, :datos)";
            $stmt = $this->conexion->prepare($sql);
        }

        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fechaActual);
        $stmt->bindParam(':datos', $datosJSON);
        
        return $stmt->execute();
    }

    public function guardarCamposCustom($idPaciente, $campos) {
        // Primero, eliminar todos los campos custom existentes para este paciente
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
                foreach ($listaCampos as $campo) {
                    if (!empty($campo['titulo']) || !empty($campo['contenido'])) {
                        $stmtInsert->bindValue(':id_paciente', $idPaciente, PDO::PARAM_INT);
                        $stmtInsert->bindValue(':seccion', $seccion);
                        $stmtInsert->bindValue(':titulo', $campo['titulo'] ?? '');
                        $stmtInsert->bindValue(':contenido', $campo['contenido'] ?? '');
                        $stmtInsert->execute();
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
