<?php
require_once 'models/Paciente.php';
require_once 'models/HistoriaClinica.php';

class PacienteController {
    private $model;

    public function __construct() {
        $this->model = new Paciente();
    }

    public function dashboard_paciente() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        $idNutriAsignado = $_SESSION['IdNutriAsignado'] ?? 1;
        
        // 1. Obtener Datos del Profesional (Single Source of Truth)
        require_once 'models/Nutricionista.php';
        $nutriModel = new Nutricionista();
        $miProfesional = $nutriModel->obtenerPorId($idNutriAsignado);
        
        // Formatear mensaje para WhatsApp
        $waNum = preg_replace('/[^0-9]/', '', $miProfesional['Whatsapp'] ?? '');
        $waMensaje = urlencode("Hola " . ($miProfesional['Nombre'] ?? 'Doc') . ", soy " . ($_SESSION['NombrePaciente'] ?? 'tu paciente') . " y tengo una consulta sobre mi plan alimentario.");
        $waLink = $waNum ? "https://wa.me/{$waNum}?text={$waMensaje}" : "#";

        // 2. Próximo Turno (KPI Superior)
        require_once 'models/Turno.php';
        $turnoModel = new Turno();
        $proximoTurno = $turnoModel->obtenerProximoParaPaciente($idPaciente);

        // 3. Plan Activo y Detalles de Hoy
        require_once 'models/PlanAlimentario.php';
        $planModel = new PlanAlimentario();
        
        $planActivo = $planModel->obtenerPlanActivo($idPaciente);
        $detallesHoy = [];
        
        if ($planActivo) {
            $diaHoy = date('N'); // 1 = Lunes, 7 = Domingo
            $detallesHoy = $planModel->obtenerDetallesPlanHoy($planActivo['IdPlan'], $diaHoy);
        }

        require_once 'views/pacientes/dashboard_paciente.php';
    }

    public function mi_plan() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idPaciente = $_SESSION['IdPaciente'] ?? 1;
        
        require_once 'models/PlanAlimentario.php';
        $planModel = new PlanAlimentario();
        
        $planActivo = $planModel->obtenerPlanActivo($idPaciente);
        $planAgrupado = [];
        $informesVinculados = [];
        
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idPaciente = $_SESSION['IdPaciente'];
            $idNutri = $_SESSION['IdNutriAsignado'];
            
            $password = $_POST['password'] ?? '';
            $foto = $_FILES['foto'] ?? null;
            
            // Handle file upload
            $fotoPath = $_POST['foto_actual'] ?? null;
            if ($foto && $foto['error'] == 0) {
                $dir = 'public/uploads/pacientes/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                
                $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
                $filename = 'paciente_' . $idPaciente . '_' . time() . '.' . $ext;
                $target = $dir . $filename;
                
                if (move_uploaded_file($foto['tmp_name'], $target)) {
                    $fotoPath = $target;
                }
            }

            // Update in DB using a custom method in Paciente model
            $this->model->actualizarCredenciales($idPaciente, $password, $fotoPath);
            
            $_SESSION['mensaje'] = "Perfil actualizado correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mi_perfil_paciente");
            exit();
        }
    }

    public function listar_pacientes() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idNutri = $_SESSION['IdNutri'] ?? 1; // Aislamiento SaaS
        $pacientes = $this->model->leerPorNutricionista($idNutri);
        require_once 'views/pacientes/listar.php';
    }

    public function crear_paciente() {
        require_once 'views/pacientes/crear.php';
    }

    public function guardar_paciente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idNutri = $_SESSION['IdNutri'] ?? 1;

            $dni = $_POST['dni'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $fecha_nac = $_POST['fecha_nacimiento'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $email = $_POST['email'] ?? '';
            $obra_social = $_POST['obra_social'] ?? 'Particular';

            if ($this->model->crear($dni, $nombre, $apellido, $fecha_nac, $telefono, $email, $idNutri, $obra_social)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_pacientes");
                exit();
            } else {
                echo "Error al registrar el paciente en la Base de Datos.";
            }
        }
    }

    public function editar_paciente() {
        $idPaciente = $_GET['id'] ?? null;
        if ($idPaciente) {
            $idNutri = $_SESSION['IdNutri'];
            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            if ($paciente) {
                // Instanciar NutriCalculator para pasar datos a la vista si existen
                require_once 'core/NutriCalculator.php';
                $peso = $paciente['Peso'] ?? 0;
                $estatura_m = ($paciente['Estatura'] ?? 0) / 100; // Asumiendo Estatura en cm en la BD
                $estatura_cm = $paciente['Estatura'] ?? 0;
                $sexo = $paciente['Sexo'] ?? 'M'; // 'M' o 'F'
                $naf = $paciente['Actividad'] ?? 1.2;
                
                // Cálculo de Edad basado en Fecha_Nacimiento
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

                // Si el paciente existe y le pertenece, carga la vista de edición
                require_once 'views/pacientes/editar.php';
                return;
            }
        }
        // Si no existe o intenta editar uno de otro profesional, redirige
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_pacientes");
    }

    public function actualizar_paciente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idNutri = $_SESSION['IdNutri'];
            $idPaciente = $_POST['id_paciente'] ?? '';
            
            $dni = $_POST['dni'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $fecha_nac = $_POST['fecha_nacimiento'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // Nuevos datos antropométricos
            $peso = isset($_POST['peso']) && $_POST['peso'] !== '' ? $_POST['peso'] : null;
            $estatura = isset($_POST['estatura']) && $_POST['estatura'] !== '' ? $_POST['estatura'] : null;
            $sexo = $_POST['sexo'] ?? null;
            $actividad = isset($_POST['actividad']) && $_POST['actividad'] !== '' ? $_POST['actividad'] : null;
            $obra_social = $_POST['obra_social'] ?? 'Particular';

            if ($this->model->actualizar($idPaciente, $dni, $nombre, $apellido, $fecha_nac, $telefono, $email, $idNutri, $peso, $estatura, $sexo, $actividad, $obra_social)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_pacientes");
                exit();
            } else {
                echo "Error al actualizar el paciente.";
            }
        }
    }

    public function eliminar_paciente() {
        $idPaciente = $_GET['id'] ?? null;
        if ($idPaciente) {
            $idNutri = $_SESSION['IdNutri'];
            $this->model->eliminar($idPaciente, $idNutri);
        }
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_pacientes");
        exit();
    }

    public function ver_historia_clinica() {
        $idPaciente = $_GET['id'] ?? null;
        if ($idPaciente) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'];
            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            if ($paciente) {
                $historiaModel = new HistoriaClinica();
                $historia = $historiaModel->obtenerPorPaciente($idPaciente, $idNutri);
                $datosHistoria = $historia ? json_decode($historia['Datos_JSON'], true) : [];
                $camposCustom = $historiaModel->obtenerCamposCustom($idPaciente);
                require_once 'views/pacientes/historia_clinica.php';
                return;
            }
        }
        header("Location: index.php?action=listar_pacientes");
    }

    public function guardar_historia_clinica() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'];
            $idPaciente = $_POST['id_paciente'] ?? null;
            
            if ($idPaciente) {
                // Extraer campos_custom
                $camposCustom = $_POST['campos_custom'] ?? [];
                
                // Collect all POST data into a JSON string except action, id_paciente and campos_custom
                $datos = $_POST;
                unset($datos['id_paciente'], $datos['action'], $datos['campos_custom']);
                
                // --- Backend Calculation Integration ---
                require_once 'services/NutriCalculoService.php';
                $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
                $sexo = $paciente['Sexo'] ?? 'M';
                $talla = $datos['talla'] ?? 0;
                $peso = $datos['peso_actual'] ?? 0;
                $muneca = $datos['circ_muneca'] ?? 0;
                $pesoUsual = $datos['peso_usual'] ?? 0;
                
                $resultadosAntropo = NutriCalculoService::evaluarAntropometria($talla, $peso, $muneca, $pesoUsual, $sexo);
                $datos = array_merge($datos, $resultadosAntropo);
                // ---------------------------------------

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
    }

    // --- Módulo: Imprimir Ficha Médica ---
    public function imprimir_ficha_medica() {
        $idPaciente = $_GET['id'] ?? null;
        if ($idPaciente) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'];

            $paciente = $this->model->obtenerPorId($idPaciente, $idNutri);
            
            if (!$paciente) {
                echo "Paciente no encontrado.";
                return;
            }
            
            $historiaModel = new HistoriaClinica();
            $historia = $historiaModel->obtenerPorPaciente($idPaciente, $idNutri);
            $datosHistoria = $historia ? json_decode($historia['Datos_JSON'], true) : [];

            require 'views/pacientes/ficha_medica_print.php';
        } else {
            echo "ID de paciente inválido.";
        }
    }
}
?>
