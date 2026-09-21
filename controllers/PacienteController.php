<?php
// controllers/PacienteController.php
require_once 'models/Paciente.php';
require_once 'models/HistoriaClinica.php';

class PacienteController {
    private $model;

    public function __construct() {
        $this->model = new Paciente();
    }

    public function dashboard_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        
        // 1. Obtener paciente para determinar con exactitud su Nutricionista asignado
        $pacienteData = $this->model->obtenerPorIdSolo($idPaciente);
        $idNutriAsignado = $pacienteData['IdNutri'] ?? ($_SESSION['IdNutriAsignado'] ?? 1);
        
        // 2. Datos del Profesional (Single Source of Truth)
        require_once 'models/Nutricionista.php';
        $nutriModel = new Nutricionista();
        $nutricionista = $nutriModel->obtenerPorId($idNutriAsignado);
        
        if (!empty($nutricionista['Color_Tema'])) {
            $_SESSION['ColorTema'] = $nutricionista['Color_Tema'];
        }
        $colorTema = $_SESSION['ColorTema'] ?? '#2ecc71';

        // 3. Próximo Turno
        require_once 'models/Turno.php';
        $turnoModel = new Turno();
        $proximoTurno = $turnoModel->obtenerProximoParaPaciente($idPaciente);

        // 4. Plan Activo y Detalles de Hoy
        require_once 'models/PlanAlimentario.php';
        $planModel = new PlanAlimentario();
        
        $planActivo = $planModel->obtenerPlanActivo($idPaciente);
        
        $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diaHoy = (int)date('N'); // 1 = Lunes, 7 = Domingo
        $nombreDiaHoy = $diasNombres[$diaHoy] ?? 'Hoy';
        
        $comidasHoy = [];
        if ($planActivo) {
            $detallesHoy = $planModel->obtenerDetallesPlanHoy($planActivo['IdPlan'], $diaHoy);
            if (!empty($detallesHoy)) {
                foreach($detallesHoy as $item) {
                    $comidasHoy[$item['Momento_Comida']][] = $item;
                }
            }
        }

        require_once 'views/pacientes/dashboard_paciente.php';
    }

    public function mi_plan() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        
        require_once 'models/PlanAlimentario.php';
        $planModel = new PlanAlimentario();
        
        $planActivo = $planModel->obtenerPlanActivo($idPaciente);
        $planAgrupado = [];
        
        if ($planActivo) {
            $detalles = $planModel->obtenerDetallesPlan($planActivo['IdPlan']);
            $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
            foreach($detalles as $d) {
                $nombreDia = $diasNombres[$d['IdDia']] ?? 'Día ' . $d['IdDia'];
                $planAgrupado[$nombreDia][$d['Momento_Comida']][] = $d;
            }
        }
        
        require_once 'views/pacientes/mi_plan.php';
    }

    public function mi_perfil_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        $idNutri = $_SESSION['IdNutriAsignado'] ?? 1;

        $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
        require_once 'views/pacientes/mi_perfil.php';
    }

    public function actualizar_mi_perfil_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idPaciente = (int)($_SESSION['IdPaciente'] ?? 0);
            
            $password = $_POST['password'] ?? '';
            $foto = $_FILES['foto'] ?? null;
            
            $fotoPath = $_POST['foto_actual'] ?? null;
            if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
                $dir = 'public/uploads/pacientes/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $filename = 'paciente_' . $idPaciente . '_' . time() . '.' . $ext;
                    $target = $dir . $filename;
                    
                    if (move_uploaded_file($foto['tmp_name'], $target)) {
                        $fotoPath = $target;
                    }
                }
            }

            $this->model->actualizarCredenciales($idPaciente, $password, $fotoPath);
            
            $_SESSION['mensaje'] = "Perfil actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mi_perfil_paciente");
            exit();
        }
    }

    public function listar_pacientes() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $pacientes = $this->model->leerPorNutricionista($idNutri);
        require_once 'views/pacientes/listar.php';
    }

    public function crear_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        require_once 'views/pacientes/crear.php';
    }

    public function guardar_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;

            $dni = trim($_POST['dni'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $fecha_nac = $_POST['fecha_nacimiento'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $obra_social = trim($_POST['obra_social'] ?? 'Particular');

            if ($this->model->crear($dni, $nombre, $apellido, $fecha_nac, $telefono, $email, $idNutri, $obra_social)) {
                // Obtener datos del profesional para personalizar el correo institucional
                require_once 'models/Nutricionista.php';
                $nutriModel = new Nutricionista();
                $nutri = $nutriModel->obtenerPorId($idNutri);

                // Envío automático de correo con credenciales de acceso
                if (!empty($email)) {
                    require_once 'services/EmailService.php';
                    $claveInicial = $dni; // La contraseña inicial por defecto es el DNI
                    EmailService::enviarCredencialesAltaPaciente($email, $nombre, $apellido, $dni, $claveInicial, $nutri ?: []);
                    $_SESSION['mensaje'] = "Paciente registrado correctamente. Se enviaron las credenciales de acceso a {$email}.";
                } else {
                    $_SESSION['mensaje'] = "Paciente registrado correctamente.";
                }

                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?action=listar_pacientes");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al registrar el paciente.";
                $_SESSION['tipo_mensaje'] = "danger";
                header("Location: index.php?action=crear_paciente");
                exit();
            }
        }
    }

    public function editar_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = (int)($_GET['id'] ?? 0);
        if ($idPaciente) {
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            if ($paciente) {
                require_once 'core/NutriCalculator.php';
                $peso = (float)($paciente['Peso'] ?? 0);
                $estatura_m = ((float)($paciente['Estatura'] ?? 0)) / 100;
                $estatura_cm = (float)($paciente['Estatura'] ?? 0);
                $sexo = $paciente['Sexo'] ?? 'M';
                $naf = (float)($paciente['Actividad'] ?? 1.2);
                
                $edad = 0;
                if (!empty($paciente['Fecha_Nacimiento'])) {
                    $fechaNac = new DateTime($paciente['Fecha_Nacimiento']);
                    $hoy = new DateTime();
                    $edad = $hoy->diff($fechaNac)->y;
                }

                $calc_imc = NutriCalculator::calcularIMC($peso, $estatura_m);
                $calc_pesoIdeal = NutriCalculator::calcularPesoIdeal($estatura_cm, $sexo);
                $calc_geb = NutriCalculator::calcularGEB($peso, $estatura_cm, $edad, $sexo);
                $calc_get = NutriCalculator::calcularGET($calc_geb, $naf);

                require_once 'views/pacientes/editar.php';
                return;
            }
        }
        $_SESSION['mensaje'] = "Paciente no encontrado.";
        $_SESSION['tipo_mensaje'] = "warning";
        header("Location: index.php?action=listar_pacientes");
        exit();
    }

    public function actualizar_paciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            
            $dni = trim($_POST['dni'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $fecha_nac = $_POST['fecha_nacimiento'] ?? '';
            $telefono = trim($_POST['telefono'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            $peso = (isset($_POST['peso']) && $_POST['peso'] !== '') ? (float)$_POST['peso'] : null;
            $estatura = (isset($_POST['estatura']) && $_POST['estatura'] !== '') ? (int)$_POST['estatura'] : null;
            $sexo = $_POST['sexo'] ?? 'M';
            $actividad = (isset($_POST['actividad']) && $_POST['actividad'] !== '') ? (float)$_POST['actividad'] : null;
            $obra_social = trim($_POST['obra_social'] ?? 'Particular');

            if ($this->model->actualizar($idPaciente, $dni, $nombre, $apellido, $fecha_nac, $telefono, $email, $idNutri, $peso, $estatura, $sexo, $actividad, $obra_social)) {
                $_SESSION['mensaje'] = "Datos del paciente actualizados exitosamente.";
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?action=listar_pacientes");
                exit();
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el paciente.";
                $_SESSION['tipo_mensaje'] = "danger";
                header("Location: index.php?action=editar_paciente&id=" . $idPaciente);
                exit();
            }
        }
    }

    public function eliminar_paciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = (int)($_GET['id'] ?? 0);
        if ($idPaciente) {
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $this->model->eliminar($idPaciente, $idNutri);
            $_SESSION['mensaje'] = "Paciente eliminado correctamente.";
            $_SESSION['tipo_mensaje'] = "info";
        }
        header("Location: index.php?action=listar_pacientes");
        exit();
    }

    public function ver_historia_clinica() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = (int)($_GET['id'] ?? 0);
        if ($idPaciente) {
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            if ($paciente) {
                $historiaModel = new HistoriaClinica();
                $historia = $historiaModel->obtenerPorPaciente($idPaciente, $idNutri);
                $datosHistoria = $historia ? json_decode($historia['Datos_JSON'], true) : [];
                $camposCustom = $historiaModel->obtenerCamposCustom($idPaciente);

                require_once 'models/EvaluacionRiesgoCV.php';
                $riesgoModel = new EvaluacionRiesgoCV();
                $evaluacionesCV = $riesgoModel->listarPorPaciente($idPaciente, $idNutri);
                $ultimaEvaluacionCV = $riesgoModel->obtenerUltimaPorPaciente($idPaciente, $idNutri);

                require_once 'views/pacientes/historia_clinica.php';
                return;
            }
        }
        $_SESSION['mensaje'] = "Paciente no encontrado.";
        $_SESSION['tipo_mensaje'] = "warning";
        header("Location: index.php?action=listar_pacientes");
        exit();
    }

    public function guardar_historia_clinica() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $idPaciente = (int)($_POST['id_paciente'] ?? 0);
            
            if ($idPaciente) {
                $camposCustom = $_POST['campos_custom'] ?? [];
                
                $datos = $_POST;
                unset($datos['id_paciente'], $datos['action'], $datos['campos_custom']);
                
                require_once 'services/NutriCalculoService.php';
                $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
                $sexo = $paciente['Sexo'] ?? 'M';
                $talla = $datos['talla'] ?? 0;
                $peso = $datos['peso_actual'] ?? 0;
                $muneca = $datos['circ_muneca'] ?? 0;
                $pesoUsual = $datos['peso_usual'] ?? 0;
                $cintura = $datos['circ_cintura'] ?? 0;
                $cadera = $datos['circ_cadera'] ?? 0;
                $relacionCC = $datos['relacion_cc'] ?? null;
                
                $resultadosAntropo = NutriCalculoService::evaluarAntropometria($talla, $peso, $muneca, $pesoUsual, $sexo, $cintura, $cadera, $relacionCC);
                $datos = array_merge($datos, $resultadosAntropo);

                $datosJSON = json_encode($datos, JSON_UNESCAPED_UNICODE);
                $historiaModel = new HistoriaClinica();
                $historiaModel->guardar($idPaciente, $idNutri, $datosJSON);
                $historiaModel->guardarCamposCustom($idPaciente, $camposCustom);

                $_SESSION['mensaje'] = "Historia Clínica guardada exitosamente.";
                $_SESSION['tipo_mensaje'] = "success";
                header("Location: index.php?action=ver_historia_clinica&id=" . $idPaciente);
                exit();
            }
        }
        header("Location: index.php?action=listar_pacientes");
        exit();
    }

    public function imprimir_ficha_medica() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idPaciente = (int)($_GET['id'] ?? 0);
        $idNutri = $_SESSION['IdNutri'] ?? 0;

        if ($idPaciente && $idNutri) {
            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            
            if (!$paciente) {
                echo "<h3 style='font-family:sans-serif; text-align:center; margin-top:50px;'>Paciente no encontrado o sin permisos.</h3>";
                return;
            }
            
            $historiaModel = new HistoriaClinica();
            $historia = $historiaModel->obtenerPorPaciente($idPaciente, $idNutri);
            $datosHistoria = $historia ? json_decode($historia['Datos_JSON'], true) : [];

            require_once 'models/EvaluacionRiesgoCV.php';
            $riesgoModel = new EvaluacionRiesgoCV();
            $ultimaEvaluacionCV = $riesgoModel->obtenerUltimaPorPaciente($idPaciente, $idNutri);

            require 'views/pacientes/ficha_medica_print.php';
        } else {
            echo "<h3 style='font-family:sans-serif; text-align:center; margin-top:50px;'>Acceso denegado. Debes iniciar sesión.</h3>";
        }
    }

    /**
     * API JSON: Guardar Evaluación de Riesgo Cardiovascular (HEARTS / OMS)
     */
    public function api_guardar_evaluacion_cv() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = $_SESSION['IdNutri'] ?? 0;
        if (!$idNutri) {
            echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
            exit();
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $idPaciente = (int)($input['id_paciente'] ?? 0);
        if ($idPaciente <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de paciente requerido']);
            exit();
        }

        require_once 'core/HeartsRiskCalculator.php';
        require_once 'models/EvaluacionRiesgoCV.php';

        // Calcular en backend para validación clínica
        $calculo = HeartsRiskCalculator::calcular($input);

        $datosGuardar = [
            'id_paciente'               => $idPaciente,
            'id_nutri'                  => $idNutri,
            'antecedente_ecv'           => !empty($input['antecedente_ecv']) ? 1 : 0,
            'antecedente_erc'           => !empty($input['antecedente_erc']) ? 1 : 0,
            'diabetes'                  => !empty($input['diabetes']) ? 1 : 0,
            'tabaquismo'                => !empty($input['tabaquismo']) ? 1 : 0,
            'edad'                      => (int)($input['edad'] ?? 50),
            'sexo'                      => strtoupper($input['sexo'] ?? 'M') === 'F' ? 'F' : 'M',
            'presion_sistolica'         => (int)($input['presion_sistolica'] ?? 120),
            'con_colesterol'            => !empty($input['con_colesterol']) ? 1 : 0,
            'colesterol_total'          => isset($input['colesterol_total']) && $input['colesterol_total'] !== '' ? (float)$input['colesterol_total'] : null,
            'peso'                      => isset($input['peso']) && $input['peso'] !== '' ? (float)$input['peso'] : null,
            'altura'                    => isset($input['altura']) && $input['altura'] !== '' ? (float)$input['altura'] : null,
            'imc'                       => isset($input['imc']) && $input['imc'] !== '' ? (float)$input['imc'] : null,
            'porcentaje_riesgo'         => $calculo['porcentaje_riesgo'],
            'categoria_riesgo'          => $calculo['categoria_riesgo'],
            'recomendacion_terapeutica' => $calculo['texto_recomendaciones']
        ];

        $riesgoModel = new EvaluacionRiesgoCV();
        $idInsertado = $riesgoModel->guardar($datosGuardar);

        if ($idInsertado) {
            echo json_encode([
                'success' => true,
                'message' => 'Evaluación de Riesgo Cardiovascular guardada exitosamente.',
                'evaluacion_id' => $idInsertado,
                'calculo' => $calculo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar la evaluación o paciente no autorizado.'
            ]);
        }
        exit();
    }

    /**
     * API JSON: Listar Historial de Evaluaciones de Riesgo CV de un Paciente
     */
    public function api_historial_evaluaciones_cv() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        $idNutri = $_SESSION['IdNutri'] ?? 0;
        $idPaciente = (int)($_GET['id_paciente'] ?? 0);

        if (!$idNutri || !$idPaciente) {
            echo json_encode(['success' => false, 'message' => 'Parámetros insuficientes']);
            exit();
        }

        require_once 'models/EvaluacionRiesgoCV.php';
        $riesgoModel = new EvaluacionRiesgoCV();
        $historial = $riesgoModel->listarPorPaciente($idPaciente, $idNutri);

        echo json_encode([
            'success' => true,
            'data' => $historial
        ]);
        exit();
    }
}
?>
