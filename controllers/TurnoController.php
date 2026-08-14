<?php
// controllers/TurnoController.php
require_once 'models/Turno.php';

class TurnoController {
    private $model;

    public function __construct() {
        $this->model = new Turno();
    }

    public function listar_turnos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $turnos = $this->model->leerPorNutricionista($idNutri);
        require_once 'views/turnos/index.php';
    }

    public function agendar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        require_once 'models/Paciente.php';
        $pacienteModel = new Paciente();
        $pacientes = $pacienteModel->leerPorNutricionista($idNutri);
        $turno = null;
        require_once 'views/turnos/form.php';
    }

    public function guardar_turno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $hora = $_POST['hora'] ?? '09:00';
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            
            if ($this->model->agendar($fecha, $hora, $idPaciente, $idNutri)) {
                $_SESSION['mensaje'] = "Turno agendado correctamente.";
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?action=listar_turnos");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al agendar el turno.";
                $_SESSION['tipo_mensaje'] = "danger";
                header("Location: index.php?action=listar_turnos");
                exit();
            }
        }
    }

    public function editar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_GET['id'] ?? 0);
        $turno = $this->model->obtenerPorId($idTurno, $idNutri);
        
        if ($turno) {
            require_once 'models/Paciente.php';
            $pacienteModel = new Paciente();
            $pacientes = $pacienteModel->leerPorNutricionista($idNutri);
            require_once 'views/turnos/form.php';
        } else {
            $_SESSION['mensaje'] = "Turno no encontrado.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: index.php?action=listar_turnos");
            exit();
        }
    }

    public function actualizar_turno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $idTurno = (int)($_POST['id_turno'] ?? 0);
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $hora = $_POST['hora'] ?? '09:00';
            $estado = $_POST['estado'] ?? 'Pendiente';
            
            if ($this->model->actualizar($idTurno, $fecha, $hora, $estado, $idNutri)) {
                $_SESSION['mensaje'] = "El turno ha sido actualizado al estado: " . htmlspecialchars($estado) . ".";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el turno.";
                $_SESSION['tipo_mensaje'] = "danger";
            }
            header("Location: index.php?action=listar_turnos");
            exit();
        }
    }

    public function eliminar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_GET['id'] ?? 0);
        
        if ($this->model->eliminar($idTurno, $idNutri)) {
            $_SESSION['mensaje'] = "El turno ha sido eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "info";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar el turno.";
            $_SESSION['tipo_mensaje'] = "danger";
        }
        header("Location: index.php?action=listar_turnos");
        exit();
    }

    public function guardar_turno_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idPaciente = $_SESSION['IdPaciente'] ?? 1;
            $idNutri = $_SESSION['IdNutriAsignado'] ?? 1;
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $hora = $_POST['hora'] ?? '10:00';
            
            if ($this->model->agendar($fecha, $hora, $idPaciente, $idNutri)) {
                $_SESSION['mensaje'] = "Tu turno ha sido solicitado exitosamente. Tu profesional lo confirmará.";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "No se pudo solicitar el turno. Intenta nuevamente.";
                $_SESSION['tipo_mensaje'] = "danger";
            }
            header("Location: index.php?action=mis_turnos");
            exit();
        }
    }

    public function mis_turnos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        $turnos = $this->model->leerPorPaciente($idPaciente);
        require_once 'views/pacientes/turnos.php';
    }

    public function solicitar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        require_once 'views/pacientes/solicitar_turno.php';
    }

    public function cancelar_turno_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idTurno = (int)($_GET['id'] ?? 0);
        $idPaciente = (int)($_SESSION['IdPaciente'] ?? 0);
        
        if ($idTurno > 0 && $idPaciente > 0) {
            $this->model->cancelarPorPaciente($idTurno, $idPaciente);
            $_SESSION['mensaje'] = "El turno ha sido cancelado.";
            $_SESSION['tipo_mensaje'] = "info";
        }
        header("Location: index.php?action=mis_turnos");
        exit();
    }
}
?>
