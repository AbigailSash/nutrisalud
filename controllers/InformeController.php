<?php
require_once 'models/InformeEducativo.php';
require_once 'models/Paciente.php';

class InformeController {
    private $model;
    private $pacienteModel;
    private $nutriModel;

    public function __construct() {
        $this->model = new InformeEducativo();
        $this->pacienteModel = new Paciente();
        require_once 'models/Nutricionista.php';
        $this->nutriModel = new Nutricionista();
    }

    public function listar_informes() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $informes = $this->model->leerPorNutricionista($idNutri);
        require_once 'views/informes/index.php';
    }

    public function crear_informe() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $pacientes = $this->pacienteModel->leerPorNutricionista($idNutri);
        require_once 'views/informes/form.php';
    }

    public function guardar_informe() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            $idPaciente = $_POST['id_paciente'];
            $fecha = date('Y-m-d');
            
            // Recuperar datos del perfil del profesional
            $perfil = $this->nutriModel->obtenerPorId($idNutri);
            
            $nombreProfesional = ($perfil['Nombre'] ?? 'Dra.') . ' ' . ($perfil['Apellido'] ?? 'Nutrición');
            $especialidad = $perfil['Especialidad'] ?? 'Lic. en Nutrición';
            $matricula = $perfil['Matricula'] ?? 'M.P. 12345';
            $logoUrl = !empty($perfil['Logo_URL']) ? $perfil['Logo_URL'] : 'assets/logo.png';
            $instagram = $perfil['Instagram'] ?? '@nutri.salud';
            $whatsapp = $perfil['Whatsapp'] ?? '+54 9 11 1234-5678';
            $direccion = $perfil['Direccion'] ?? 'Consultorio';

            // Reconstruimos el JSON basado en el formulario y el perfil
            $informeData = [
                'header' => [
                    'profesional' => [
                        'logo_url' => $logoUrl,
                        'nombre_completo' => $nombreProfesional,
                        'especialidad' => $especialidad,
                        'matricula' => $matricula
                    ],
                    'objetivo_general' => $_POST['objetivo_general'] ?? '',
                    'recordatorio_clave' => $_POST['recordatorio_clave'] ?? ''
                ],
                'diagnostico_positivo' => [],
                'ejes_trabajo' => [],
                'objetivos_corto_plazo' => [],
                'footer' => [
                    'mensaje_motivacional' => $_POST['mensaje_motivacional'] ?? '¡Vos podés lograrlo!',
                    'contacto' => [
                        'instagram' => $instagram,
                        'whatsapp' => $whatsapp,
                        'direccion' => $direccion
                    ],
                    'firma' => [
                        'nombre' => $nombreProfesional,
                        'titulo' => $especialidad,
                        'matricula' => $matricula
                    ]
                ]
            ];

            // Procesar Ejes de Trabajo
            if (isset($_POST['eje_titulo']) && is_array($_POST['eje_titulo'])) {
                foreach ($_POST['eje_titulo'] as $i => $titulo) {
                    $informeData['ejes_trabajo'][] = [
                        'numero' => $i + 1,
                        'titulo' => $titulo,
                        'subtitulo' => $_POST['eje_subtitulo'][$i] ?? '',
                        'acciones' => array_filter(array_map('trim', explode("\n", $_POST['eje_acciones'][$i] ?? ''))),
                        'ilustracion_url' => 'https://cdn-icons-png.flaticon.com/512/2906/2906274.png' // Icono genérico
                    ];
                }
            }

            // Procesar Diagnósticos
            if (isset($_POST['diag_texto']) && is_array($_POST['diag_texto'])) {
                foreach ($_POST['diag_texto'] as $i => $texto) {
                    $informeData['diagnostico_positivo'][] = [
                        'id' => $i + 1,
                        'texto' => $texto,
                        'icono_url' => 'https://cdn-icons-png.flaticon.com/512/190/190411.png' // Icono check
                    ];
                }
            }

            $jsonContenido = json_encode($informeData, JSON_UNESCAPED_UNICODE);

            if ($this->model->crear($idPaciente, $idNutri, $fecha, $jsonContenido)) {
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_informes");
                exit();
            } else {
                echo "Error al guardar el informe.";
            }
        }
    }

    public function ver_informe() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idInforme = $_GET['id'] ?? 0;
        
        $informeRow = $this->model->obtenerPorId($idInforme);
        if ($informeRow) {
            $informe = json_decode($informeRow['Contenido_JSON'], true);
            $paciente = $this->pacienteModel->obtenerPorId($informeRow['IdPaciente'], $informeRow['IdNutri'] ?? $_SESSION['IdNutri'] ?? 1); // fallback if accessed by patient
            
            // SINGLE SOURCE OF TRUTH: Sobreescribimos con datos en vivo del Nutri
            $perfilEnVivo = $this->nutriModel->obtenerPorId($informeRow['IdNutri']);
            
            $informe['header']['profesional']['nombre_completo'] = ($perfilEnVivo['Nombre'] ?? 'Dra.') . ' ' . ($perfilEnVivo['Apellido'] ?? '');
            $informe['header']['profesional']['especialidad'] = $perfilEnVivo['Especialidad'] ?? '';
            $informe['header']['profesional']['matricula'] = $perfilEnVivo['Matricula'] ?? '';
            if (!empty($perfilEnVivo['Logo_URL'])) {
                $informe['header']['profesional']['logo_url'] = $perfilEnVivo['Logo_URL'];
            }
            
            $informe['footer']['contacto']['instagram'] = $perfilEnVivo['Instagram'] ?? '';
            $informe['footer']['contacto']['whatsapp'] = $perfilEnVivo['Whatsapp'] ?? '';
            $informe['footer']['contacto']['direccion'] = $perfilEnVivo['Direccion'] ?? '';
            $informe['footer']['firma']['nombre'] = $informe['header']['profesional']['nombre_completo'];
            $informe['footer']['firma']['titulo'] = $informe['header']['profesional']['especialidad'];
            $informe['footer']['firma']['matricula'] = $informe['header']['profesional']['matricula'];
            
            $biografia = $perfilEnVivo['Biografia'] ?? '';

            require_once 'views/informes/ver.php';
        } else {
            echo "Informe no encontrado.";
        }
    }

    public function eliminar_informe() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $idInforme = $_GET['id'] ?? 0;
        
        $this->model->eliminar($idInforme, $idNutri);
        if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=listar_informes");
        exit();
    }
}
?>
