<?php
// controllers/AuthController.php
require_once 'models/Nutricionista.php';
require_once 'models/Paciente.php';

class AuthController {
    private $model;
    private $pacienteModel;

    public function __construct() {
        $this->model = new Nutricionista();
        $this->pacienteModel = new Paciente();
    }

    public function mostrarLoginNutri() {
        require_once 'views/auth/login_nutri.php';
    }

    public function procesarLoginNutri() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identificador = trim($_POST['identificador'] ?? '');
            $password = $_POST['password'] ?? '';

            $nutri = $this->model->autenticar($identificador, $password);

            if ($nutri) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION['IdNutri'] = $nutri['IdNutri'];
                $_SESSION['NombreNutri'] = $nutri['Nombre'];
                $_SESSION['ApellidoNutri'] = $nutri['Apellido'];
                $_SESSION['EspecialidadNutri'] = $nutri['Especialidad'] ?? 'Lic. en Nutrición';
                $_SESSION['MatriculaNutri'] = $nutri['Matricula'] ?? '';
                $_SESSION['LogoNutri'] = $nutri['Logo_URL'] ?? null;
                $_SESSION['ColorTema'] = $nutri['Color_Tema'] ?? '#2ecc71';
                $_SESSION['user_rol'] = $nutri['Rol'] ?? 'nutricionista';
                
                if ($_SESSION['user_rol'] === 'admin') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php?action=dashboard");
                }
                exit();
            } else {
                $error = "Credenciales incorrectas o cuenta inactiva.";
                require_once 'views/auth/login_nutri.php';
            }
        }
    }

    public function mostrarLoginPaciente() {
        require_once 'views/auth/login_paciente.php';
    }

    public function procesarLoginPaciente() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dni = trim($_POST['dni'] ?? '');
            $email = trim($_POST['email'] ?? '');

            $pacienteAutenticado = $this->pacienteModel->autenticar($dni, $email);

            if ($pacienteAutenticado) {
                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION['IdPaciente'] = $pacienteAutenticado['IdPaciente'];
                $_SESSION['NombrePaciente'] = $pacienteAutenticado['Nombre'] . ' ' . $pacienteAutenticado['Apellido'];
                $_SESSION['IdNutriAsignado'] = $pacienteAutenticado['IdNutri'];
                
                header("Location: index.php?action=dashboard_paciente");
                exit();
            } else {
                $error = "DNI o Contraseña incorrectos.";
                require_once 'views/auth/login_paciente.php';
            }
        }
    }

    public static function verificarSesion() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['IdNutri'])) {
            $_SESSION['mensaje'] = "Debes iniciar sesión para acceder a este módulo.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: index.php?action=login_nutri");
            exit();
        }
    }

    public static function verificarSesionPaciente() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['IdPaciente'])) {
            $_SESSION['mensaje'] = "Debes iniciar sesión como paciente.";
            $_SESSION['tipo_mensaje'] = "warning";
            header("Location: index.php?action=login_paciente");
            exit();
        }
    }

    public function mostrarCambiarPassword() {
        self::verificarSesion();
        require_once 'views/auth/cambiar_password.php';
    }

    public function procesarCambiarPassword() {
        self::verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevoPassword = $_POST['nueva_password'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';

            if ($nuevoPassword === $confirmarPassword && strlen($nuevoPassword) >= 6) {
                $idNutri = $_SESSION['IdNutri'];
                if ($this->model->cambiarPassword($idNutri, $nuevoPassword)) {
                    $mensaje = "Contraseña actualizada exitosamente.";
                } else {
                    $error = "Error al actualizar la contraseña.";
                }
            } else {
                $error = "Las contraseñas no coinciden o son muy cortas (mínimo 6 caracteres).";
            }
            require_once 'views/auth/cambiar_password.php';
        }
    }
}
?>
