<?php
require_once 'models/Turno.php';

class TurnoController {
    private $model;

    public function __construct() {
        $this->model = new Turno();
    }

    public function listar_turnos() {
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $turnos = $this->model->leerPorNutricionista($idNutri);
        require_once 'views/turnos/index.php';
    }

    public function agendar_turno() {
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        require_once 'models/Paciente.php';
        $pacienteModel = new Paciente();
        $pacientes = $pacienteModel->leerPorNutricionista($idNutri);
        $turno = null; // Para que el form funcione igual en crear y editar
        require_once 'views/turnos/form.php';
    }

    public function guardar_turno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $idPaciente = $_POST['id_paciente'];
            
            if ($this->model->agendar($fecha, $hora, $idPaciente, $idNutri)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_turnos");
                exit();
            } else {
                echo "Error al crear el turno.";
            }
        }
    }

    public function editar_turno() {
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = $_GET['id'] ?? 0;
        $turno = $this->model->obtenerPorId($idTurno, $idNutri);
        
        if ($turno) {
            require_once 'models/Paciente.php';
            $pacienteModel = new Paciente();
            $pacientes = $pacienteModel->leerPorNutricionista($idNutri);
            require_once 'views/turnos/form.php';
        } else {
            echo "Turno no encontrado.";
        }
    }

    public function actualizar_turno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $idTurno = $_POST['id_turno'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $estado = $_POST['estado'];
            
            if ($this->model->actualizar($idTurno, $fecha, $hora, $estado, $idNutri)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION['mensaje'] = "El estado del turno fue actualizado a " . $estado . ".";
                $_SESSION['tipo_mensaje'] = "success";
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_turnos");
                exit();
            } else {
                echo "Error al actualizar el turno.";
            }
        }
    }

    public function eliminar_turno() {
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = $_GET['id'] ?? 0;
        
        if ($this->model->eliminar($idTurno, $idNutri)) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "El turno ha sido eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "danger";
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_turnos");
            exit();
        } else {
            echo "Error al eliminar el turno.";
        }
    }

    public function guardar_turno_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $idPaciente = $_SESSION['IdPaciente'] ?? 1;
            $idNutri = $_SESSION['IdNutriAsignado'] ?? 1; // Usar IdNutriAsignado en vez de IdNutri
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            
            if ($this->model->agendar($fecha, $hora, $idPaciente, $idNutri)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mis_turnos");
                exit();
            } else {
                echo "Error al solicitar el turno.";
            }
        }
    }

    // Funciones exclusivas del paciente
    public function mis_turnos() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        $turnos = $this->model->leerPorPaciente($idPaciente);
        require_once 'views/pacientes/turnos.php';
    }

    public function solicitar_turno() {
        require_once 'views/pacientes/solicitar_turno.php';
    }

    public function cancelar_turno_paciente() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idTurno = $_GET['id'] ?? 0;
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        $idNutri = $_SESSION['IdNutriAsignado'] ?? 1;
        
        // Primero verificamos que el turno le pertenece (opcional si filtramos bien, pero lo forzamos a cancelar actualizando estado)
        $this->model->actualizar($idTurno, $_GET['f'], $_GET['h'], 'Cancelado', $idNutri);
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mis_turnos");
        exit();
    }
}
?>
