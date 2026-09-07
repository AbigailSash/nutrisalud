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

    public function mostrarRecuperarPassword() {
        require_once 'views/auth/recuperar_password.php';
    }

    public function procesarSolicitarRecuperar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim(strtolower($_POST['email'] ?? ''));
            $tipoUsuario = ($_POST['tipo_usuario'] ?? '') === 'paciente' ? 'paciente' : 'nutricionista';

            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                require_once 'models/PasswordReset.php';
                require_once 'services/EmailService.php';

                $resetModel = new PasswordReset();
                $usuarioExiste = false;
                $nombreUsuario = 'Usuario';
                $colorTema = '#2ecc71';

                if ($tipoUsuario === 'nutricionista') {
                    $nutri = $this->model->obtenerPorEmail($email);
                    if ($nutri) {
                        $usuarioExiste = true;
                        $nombreUsuario = $nutri['Nombre'] . ' ' . $nutri['Apellido'];
                        $colorTema = $nutri['Color_Tema'] ?? '#2ecc71';
                    }
                } else {
                    $paciente = $this->pacienteModel->obtenerPorEmail($email);
                    if ($paciente) {
                        $usuarioExiste = true;
                        $nombreUsuario = $paciente['Nombre'] . ' ' . $paciente['Apellido'];
                        // Cargar color del profesional asignado si existe
                        if (!empty($paciente['IdNutri'])) {
                            $nutriAsignado = $this->model->obtenerPorId($paciente['IdNutri']);
                            if ($nutriAsignado && !empty($nutriAsignado['Color_Tema'])) {
                                $colorTema = $nutriAsignado['Color_Tema'];
                            }
                        }
                    }
                }

                if ($usuarioExiste) {
                    $tokenData = $resetModel->crearToken($email, $tipoUsuario, 30);
                    if ($tokenData) {
                        $baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost:8000/';
                        $resetUrl = $baseUrl . 'index.php?action=restablecer_password&token=' . urlencode($tokenData['token']);
                        
                        EmailService::enviarRecuperacionPassword(
                            $email,
                            $nombreUsuario,
                            $resetUrl,
                            $tipoUsuario,
                            $tokenData['minutos'],
                            $colorTema
                        );
                    }
                }

                // Respuesta genérica por seguridad para evitar enumeración de cuentas
                $mensajeExito = "Si el correo ingresado coincide con una cuenta activa, recibirás un enlace para restablecer tu contraseña en los próximos minutos.";
                require_once 'views/auth/recuperar_password.php';
                return;
            } else {
                $error = "Por favor, ingresa un correo electrónico válido.";
                require_once 'views/auth/recuperar_password.php';
                return;
            }
        }
    }

    public function mostrarRestablecerPassword() {
        $token = trim($_GET['token'] ?? '');
        require_once 'models/PasswordReset.php';
        $resetModel = new PasswordReset();
        $tokenValido = $resetModel->validarToken($token);

        require_once 'views/auth/restablecer_password.php';
    }

    public function procesarRestablecerPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = trim($_POST['token'] ?? '');
            $nuevaPassword = $_POST['nueva_password'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';

            require_once 'models/PasswordReset.php';
            $resetModel = new PasswordReset();
            $tokenValido = $resetModel->validarToken($token);

            if (!$tokenValido) {
                $tokenValido = false;
                $error = "El enlace de recuperación ha expirado o ya no es válido.";
                require_once 'views/auth/restablecer_password.php';
                return;
            }

            if (empty($nuevaPassword) || strlen($nuevaPassword) < 6) {
                $error = "La nueva contraseña debe tener al menos 6 caracteres.";
                require_once 'views/auth/restablecer_password.php';
                return;
            }

            if ($nuevaPassword !== $confirmarPassword) {
                $error = "Las contraseñas no coinciden. Por favor, verifícalas.";
                require_once 'views/auth/restablecer_password.php';
                return;
            }

            $email = $tokenValido['Email'];
            $tipoUsuario = $tokenValido['Tipo_Usuario'];
            $actualizado = false;

            if ($tipoUsuario === 'nutricionista') {
                $nutri = $this->model->obtenerPorEmail($email);
                if ($nutri) {
                    $actualizado = $this->model->cambiarPassword($nutri['IdNutri'], $nuevaPassword);
                }
            } else {
                $paciente = $this->pacienteModel->obtenerPorEmail($email);
                if ($paciente) {
                    $actualizado = $this->pacienteModel->cambiarPassword($paciente['IdPaciente'], $nuevaPassword);
                }
            }

            if ($actualizado) {
                // Invalida el token para que no pueda ser reutilizado
                $resetModel->marcarComoUsado($token);

                if (session_status() == PHP_SESSION_NONE) session_start();
                $_SESSION['mensaje'] = "Tu contraseña ha sido actualizada con éxito. Ya puedes iniciar sesión con tu nueva clave.";
                $_SESSION['tipo_mensaje'] = "success";

                if ($tipoUsuario === 'paciente') {
                    header("Location: index.php?action=login_paciente");
                } else {
                    header("Location: index.php?action=login_nutri");
                }
                exit();
            } else {
                $error = "Ocurrió un error al actualizar la contraseña. Inténtalo nuevamente.";
                require_once 'views/auth/restablecer_password.php';
                return;
            }
        }
    }
}
?>
