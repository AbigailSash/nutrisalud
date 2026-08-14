<?php
require_once 'models/Nutricionista.php';
require_once 'models/Paciente.php';

class AuthController {
    private $model;
    private $pacienteModel;

    public function __construct() {
        $this->model = new Nutricionista();
        $this->pacienteModel = new Paciente();
    }

    // Muestra el formulario de login
    public function mostrarLoginNutri() {
        require_once 'views/auth/login_nutri.php';
    }

    // Procesa los datos del formulario de login
    public function procesarLoginNutri() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $identificador = $_POST['identificador'] ?? '';
            $password = $_POST['password'] ?? '';

            $nutri = $this->model->autenticar($identificador, $password);

            if ($nutri) {
                // Inicio de sesión exitoso
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['IdNutri'] = $nutri['IdNutri'];
                $_SESSION['NombreNutri'] = $nutri['Nombre'];
                $_SESSION['ApellidoNutri'] = $nutri['Apellido'];
                $_SESSION['EspecialidadNutri'] = $nutri['Especialidad'] ?? 'Nutricionista Profesional';
                $_SESSION['MatriculaNutri'] = $nutri['Matricula'] ?? '';
                $_SESSION['LogoNutri'] = $nutri['Logo_URL'] ?? null;
                $_SESSION['user_rol'] = $nutri['Rol'] ?? 'nutricionista';
                
                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION['mensaje'] = "Operación realizada correctamente.";
                $_SESSION['tipo_mensaje'] = "success";
                
                if ($_SESSION['user_rol'] === 'admin') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php?action=dashboard");
                }
                exit();
            } else {
                // Error de credenciales
                $error = "Credenciales incorrectas o cuenta inactiva.";
                require_once 'views/auth/login_nutri.php';
            }
        }
    }

    // Muestra el formulario de login para pacientes
    public function mostrarLoginPaciente() {
        require_once 'views/auth/login_paciente.php';
    }

    // Procesa el login de paciente usando DNI y Email
    public function procesarLoginPaciente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dni = $_POST['dni'] ?? '';
            $email = $_POST['email'] ?? '';

            // Implementación rápida de autenticación de paciente usando DNI y Email
            // Ya que la tabla no tiene contraseña
            
            // Requeriremos crear un método específico en el modelo
            $pacienteAutenticado = $this->pacienteModel->autenticar($dni, $email);

            if ($pacienteAutenticado) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['IdPaciente'] = $pacienteAutenticado['IdPaciente'];
                $_SESSION['NombrePaciente'] = $pacienteAutenticado['Nombre'] . ' ' . $pacienteAutenticado['Apellido'];
                $_SESSION['IdNutriAsignado'] = $pacienteAutenticado['IdNutri'];
                
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=dashboard_paciente");
                exit();
            } else {
                $error = "DNI o Email incorrectos.";
                require_once 'views/auth/login_paciente.php';
            }
        }
    }

    // Validar si la sesión está activa para proteger las rutas de nutri
    public static function verificarSesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['IdNutri'])) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=login_nutri");
            exit();
        }
    }

    // Validar si la sesión está activa para proteger las rutas de paciente
    public static function verificarSesionPaciente() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['IdPaciente'])) {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=login_paciente");
            exit();
        }
    }

    // Muestra el formulario para cambiar contraseña
    public function mostrarCambiarPassword() {
        self::verificarSesion();
        require_once 'views/auth/cambiar_password.php';
    }

    // Procesa el cambio de contraseña
    public function procesarCambiarPassword() {
        self::verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                $error = "Las contraseñas no coinciden o son muy cortas (mín. 6 caracteres).";
            }
            require_once 'views/auth/cambiar_password.php';
        }
    }
}
?>
