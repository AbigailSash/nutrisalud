<?php
// controllers/TurnoController.php
// Controlador principal para la gestión de turnos, calendario interactivo FullCalendar v6 y notificaciones.

require_once 'models/Turno.php';
require_once 'models/Paciente.php';
require_once 'models/Nutricionista.php';
require_once 'services/MailerService.php';

class TurnoController {
    private $model;
    private $pacienteModel;
    private $nutriModel;

    public function __construct() {
        $this->model = new Turno();
        $this->pacienteModel = new Paciente();
        $this->nutriModel = new Nutricionista();
    }

    /**
     * Vista principal de turnos (FullCalendar v6 + Vista Lista + Modales).
     */
    public function listar_turnos() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';
        
        $turnos = $this->model->leerPorNutricionista($idNutri);
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
        $nutri = $this->nutriModel->obtenerPorId($idNutri);
        
        require_once 'views/turnos/index.php';
    }

    /**
     * API JSON: Feed de eventos para FullCalendar v6.
     */
    public function api_eventos_calendario() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $inicio = $_GET['start'] ?? null;
        $fin = $_GET['end'] ?? null;
        $estado = $_GET['estado'] ?? null;

        $turnos = $this->model->obtenerParaCalendario($idNutri, $inicio, $fin, $estado);
        $eventos = [];

        foreach ($turnos as $t) {
            $horaStr = substr($t['Hora'], 0, 5);
            $startDateTime = $t['Fecha'] . 'T' . $horaStr . ':00';
            $endDateTime = date('Y-m-d\TH:i:s', strtotime($t['Fecha'] . ' ' . $t['Hora'] . ' + 45 minutes'));

            // Paleta de colores por estado
            $bgColor = '#f59e0b'; // Pendiente (Ámbar)
            $borderColor = '#d97706';
            $textColor = '#ffffff';

            if ($t['Estado_Turno'] === 'Confirmado') {
                $bgColor = '#10b981'; // Verde esmeralda
                $borderColor = '#059669';
            } elseif ($t['Estado_Turno'] === 'Atendido') {
                $bgColor = '#3b82f6'; // Azul
                $borderColor = '#2563eb';
            } elseif ($t['Estado_Turno'] === 'Cancelado') {
                $bgColor = '#ef4444'; // Rojo
                $borderColor = '#dc2626';
            }

            $eventos[] = [
                'id' => (string)$t['IdTurno'],
                'title' => ($t['PacienteApellido'] ?? '') . ', ' . ($t['PacienteNombre'] ?? ''),
                'start' => $startDateTime,
                'end' => $endDateTime,
                'backgroundColor' => $bgColor,
                'borderColor' => $borderColor,
                'textColor' => $textColor,
                'classNames' => ['turno-item-fc', 'estado-' . strtolower($t['Estado_Turno'])],
                'extendedProps' => [
                    'id_turno' => (int)$t['IdTurno'],
                    'id_paciente' => (int)$t['IdPaciente'],
                    'paciente_nombre' => trim(($t['PacienteNombre'] ?? '') . ' ' . ($t['PacienteApellido'] ?? '')),
                    'paciente_dni' => $t['PacienteDNI'] ?? '',
                    'paciente_telefono' => $t['PacienteTelefono'] ?? '',
                    'paciente_email' => $t['PacienteEmail'] ?? '',
                    'paciente_obra_social' => $t['PacienteObraSocial'] ?? 'Particular',
                    'estado' => $t['Estado_Turno'],
                    'modalidad' => $t['Modalidad'] ?? 'Presencial',
                    'motivo' => $t['Motivo_Consulta'] ?? 'Consulta Nutricional',
                    'link' => $t['Link_Reunion'] ?? '',
                    'direccion' => $t['Direccion'] ?? '',
                    'notas' => $t['Notas'] ?? '',
                    'fecha' => $t['Fecha'],
                    'hora' => $horaStr
                ]
            ];
        }

        echo json_encode($eventos);
        exit();
    }

    /**
     * API JSON: Guardar nuevo turno desde el modal del calendario.
     */
    public function api_guardar_turno_calendario() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit();
        }

        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idPaciente = (int)($_POST['id_paciente'] ?? 0);
        $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
        $hora = trim($_POST['hora'] ?? '09:00');
        $modalidad = trim($_POST['modalidad'] ?? 'Presencial');
        $motivo = trim($_POST['motivo_consulta'] ?? 'Consulta Nutricional');
        $link = trim($_POST['link_reunion'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $notas = trim($_POST['notas'] ?? '');
        $estado = trim($_POST['estado'] ?? 'Confirmado');
        $notificar = !empty($_POST['notificar_paciente']);

        if ($idPaciente <= 0 || empty($fecha) || empty($hora)) {
            echo json_encode(['success' => false, 'error' => 'Por favor complete todos los campos obligatorios.']);
            exit();
        }

        $idTurno = $this->model->agendarConDetalles($fecha, $hora, $idPaciente, $idNutri, $modalidad, $motivo, $link, $direccion, $notas, $estado);

        if ($idTurno) {
            $emailStatus = 'no_enviado';
            if ($notificar) {
                try {
                    $paciente = $this->pacienteModel->obtenerPorId($idPaciente, $idNutri);
                    $nutri = $this->nutriModel->obtenerPorId($idNutri);
                    $datosTurno = [
                        'Fecha' => $fecha,
                        'Hora' => $hora,
                        'Modalidad' => $modalidad,
                        'Motivo_Consulta' => $motivo,
                        'Link_Reunion' => $link,
                        'Direccion' => $direccion,
                        'Notas' => $notas,
                        'Estado_Turno' => $estado
                    ];
                    $resPaciente = MailerService::enviarConfirmacionTurnoPaciente($datosTurno, $paciente, $nutri);
                    $resNutri = MailerService::enviarNotificacionTurnoNutricionista($datosTurno, $paciente, $nutri);
                    $emailStatus = ($resPaciente && $resNutri) ? 'enviado_ok' : 'parcial';
                } catch (Throwable $e) {
                    error_log("Error enviando email de turno: " . $e->getMessage());
                    $emailStatus = 'error_envio';
                }
            }

            echo json_encode([
                'success' => true,
                'id_turno' => $idTurno,
                'mensaje' => 'Turno agendado correctamente.',
                'email_status' => $emailStatus
            ]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al guardar el turno en la base de datos.']);
            exit();
        }
    }

    /**
     * API JSON: Reprogramar turno (Drag & Drop en calendario o modal rápido).
     */
    public function api_reprogramar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit();
        }

        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_POST['id_turno'] ?? 0);
        $nuevaFecha = trim($_POST['fecha'] ?? '');
        $nuevaHora = trim($_POST['hora'] ?? '');
        $notificar = !empty($_POST['notificar_paciente']);

        if ($idTurno <= 0 || empty($nuevaFecha) || empty($nuevaHora)) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos para reprogramación.']);
            exit();
        }

        // Obtener datos previos para el correo
        $turnoPrevio = $this->model->obtenerDetalleCompleto($idTurno, $idNutri);
        if (!$turnoPrevio) {
            echo json_encode(['success' => false, 'error' => 'Turno no encontrado o sin permisos.']);
            exit();
        }

        $fechaAnt = $turnoPrevio['Fecha'];
        $horaAnt  = $turnoPrevio['Hora'];

        if ($this->model->reprogramar($idTurno, $nuevaFecha, $nuevaHora, $idNutri)) {
            if ($notificar) {
                try {
                    $paciente = $this->pacienteModel->obtenerPorId($turnoPrevio['IdPaciente'], $idNutri);
                    $nutri = $this->nutriModel->obtenerPorId($idNutri);
                    $datosActualizados = array_merge($turnoPrevio, [
                        'Fecha' => $nuevaFecha,
                        'Hora' => $nuevaHora
                    ]);
                    MailerService::enviarReprogramacionTurno($datosActualizados, $paciente, $nutri, $fechaAnt, $horaAnt);
                } catch (Throwable $e) {
                    error_log("Error notificando reprogramación: " . $e->getMessage());
                }
            }

            echo json_encode([
                'success' => true,
                'mensaje' => 'Turno reprogramado exitosamente.'
            ]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el horario del turno.']);
            exit();
        }
    }

    /**
     * API JSON: Cambiar estado del turno (Confirmado, Atendido, Cancelado, Pendiente).
     */
    public function api_cambiar_estado_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit();
        }

        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_POST['id_turno'] ?? 0);
        $estado = trim($_POST['estado'] ?? '');
        $motivoCancelacion = trim($_POST['motivo_cancelacion'] ?? '');
        $notificar = !empty($_POST['notificar_paciente']);

        $estadosValidos = ['Pendiente', 'Confirmado', 'Atendido', 'Cancelado'];
        if ($idTurno <= 0 || !in_array($estado, $estadosValidos)) {
            echo json_encode(['success' => false, 'error' => 'Estado inválido o turno no especificado.']);
            exit();
        }

        $turno = $this->model->obtenerDetalleCompleto($idTurno, $idNutri);
        if (!$turno) {
            echo json_encode(['success' => false, 'error' => 'Turno no encontrado.']);
            exit();
        }

        if ($this->model->actualizarEstado($idTurno, $estado, $idNutri)) {
            if ($notificar && $estado === 'Cancelado') {
                try {
                    $paciente = $this->pacienteModel->obtenerPorId($turno['IdPaciente'], $idNutri);
                    $nutri = $this->nutriModel->obtenerPorId($idNutri);
                    MailerService::enviarCancelacionTurno($turno, $paciente, $nutri, $motivoCancelacion);
                } catch (Throwable $e) {
                    error_log("Error notificando cancelación: " . $e->getMessage());
                }
            }

            echo json_encode([
                'success' => true,
                'mensaje' => "El turno ha sido actualizado a: $estado"
            ]);
            exit();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al cambiar el estado del turno.']);
            exit();
        }
    }

    /**
     * API JSON: Obtener detalle completo de un turno.
     */
    public function api_detalle_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_GET['id'] ?? 0);

        $turno = $this->model->obtenerDetalleCompleto($idTurno, $idNutri);
        if ($turno) {
            // Añadir enlaces de utilidad
            $nutri = $this->nutriModel->obtenerPorId($idNutri);
            $nombreNutri = trim(($nutri['Nombre'] ?? '') . ' ' . ($nutri['Apellido'] ?? ''));
            $wppUrl = !empty($turno['PacienteTelefono']) ? MailerService::generarWhatsAppUrl($turno['PacienteTelefono'], "Hola {$turno['PacienteNombre']}, te contacto desde NutriSalud por tu turno.") : '';
            $gcalUrl = MailerService::generarGoogleCalendarUrl("Consulta Nutricional - {$turno['PacienteNombre']} {$turno['PacienteApellido']}", $turno['Fecha'], $turno['Hora'], 45, $turno['Motivo_Consulta'] ?? '', $turno['Modalidad'] === 'Online' ? $turno['Link_Reunion'] : $turno['Direccion']);

            $turno['whatsapp_url'] = $wppUrl;
            $turno['gcal_url'] = $gcalUrl;

            echo json_encode(['success' => true, 'turno' => $turno]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Turno no encontrado.']);
        }
        exit();
    }

    public function agendar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
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
            $modalidad = $_POST['modalidad'] ?? 'Presencial';
            $motivo = $_POST['motivo_consulta'] ?? 'Consulta Nutricional';
            $link = $_POST['link_reunion'] ?? null;
            $direccion = $_POST['direccion'] ?? null;
            $notas = $_POST['notas'] ?? null;
            $estado = $_POST['estado'] ?? 'Pendiente';
            
            if ($this->model->agendarConDetalles($fecha, $hora, $idPaciente, $idNutri, $modalidad, $motivo, $link, $direccion, $notas, $estado)) {
                $_SESSION['mensaje'] = "Turno agendado correctamente.";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al agendar el turno.";
                $_SESSION['tipo_mensaje'] = "danger";
            }
            header("Location: index.php?action=listar_turnos");
            exit();
        }
    }

    public function editar_turno() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idTurno = (int)($_GET['id'] ?? 0);
        $turno = $this->model->obtenerPorId($idTurno, $idNutri);
        
        if ($turno) {
            $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
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
            $modalidad = $_POST['modalidad'] ?? 'Presencial';
            $motivo = $_POST['motivo_consulta'] ?? 'Consulta Nutricional';
            $link = $_POST['link_reunion'] ?? null;
            $direccion = $_POST['direccion'] ?? null;
            $notas = $_POST['notas'] ?? null;
            
            if ($this->model->actualizarCompleto($idTurno, $fecha, $hora, $estado, $modalidad, $motivo, $link, $direccion, $notas, $idNutri)) {
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
            $modalidad = $_POST['modalidad'] ?? 'Presencial';
            $motivo = $_POST['motivo_consulta'] ?? 'Consulta Nutricional';
            
            if ($this->model->agendarConDetalles($fecha, $hora, $idPaciente, $idNutri, $modalidad, $motivo)) {
                // Notificar al nutricionista de la nueva solicitud
                try {
                    $paciente = $this->pacienteModel->obtenerPorIdSolo($idPaciente);
                    $nutri = $this->nutriModel->obtenerPorId($idNutri);
                    $datosTurno = [
                        'Fecha' => $fecha,
                        'Hora' => $hora,
                        'Modalidad' => $modalidad,
                        'Motivo_Consulta' => $motivo,
                        'Estado_Turno' => 'Pendiente'
                    ];
                    MailerService::enviarNotificacionTurnoNutricionista($datosTurno, $paciente, $nutri);
                } catch (Throwable $e) {
                    error_log("Error notificando solicitud de turno: " . $e->getMessage());
                }

                $_SESSION['mensaje'] = "Tu turno ha sido solicitado exitosamente. Tu profesional lo confirmará a la brevedad.";
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
