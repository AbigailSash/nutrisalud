<?php
// controllers/PerfilController.php
require_once 'models/Nutricionista.php';

class PerfilController {
    private $model;

    public function __construct() {
        $this->model = new Nutricionista();
    }

    public function mi_perfil() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $perfil = $this->model->obtenerPorId($idNutri);
        
        require_once 'views/perfil/index.php';
    }

    public function actualizar_perfil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $especialidad = trim($_POST['especialidad'] ?? '');
            $matricula = trim($_POST['matricula'] ?? '');
            $instagram = trim($_POST['instagram'] ?? '');
            $whatsapp = trim($_POST['whatsapp'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $biografia = trim($_POST['biografia'] ?? '');
            
            $logoUrl = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'public/assets/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $filename = 'logo_' . $idNutri . '_' . time() . '.' . $ext;
                    $uploadPath = $uploadDir . $filename;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                        $logoUrl = $uploadPath;
                    }
                }
            }
            
            if ($this->model->actualizarPerfil($idNutri, $nombre, $apellido, $especialidad, $matricula, $instagram, $whatsapp, $direccion, $biografia, $logoUrl)) {
                $_SESSION['NombreNutri'] = $nombre;
                $_SESSION['ApellidoNutri'] = $apellido;
                $_SESSION['EspecialidadNutri'] = $especialidad;
                $_SESSION['MatriculaNutri'] = $matricula;
                if ($logoUrl) {
                    $_SESSION['LogoNutri'] = $logoUrl;
                }
                
                $_SESSION['mensaje'] = "Perfil actualizado correctamente.";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el perfil.";
                $_SESSION['tipo_mensaje'] = "danger";
            }
            header("Location: index.php?action=mi_perfil");
            exit();
        }
    }

    public function actualizar_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            
            $nueva = $_POST['nueva_password'] ?? '';
            $confirmar = $_POST['confirmar_password'] ?? '';
            
            if (strlen($nueva) < 6) {
                $_SESSION['mensaje'] = "La contraseña debe tener al menos 6 caracteres.";
                $_SESSION['tipo_mensaje'] = "danger";
            } elseif ($nueva !== $confirmar) {
                $_SESSION['mensaje'] = "Las contraseñas no coinciden.";
                $_SESSION['tipo_mensaje'] = "danger";
            } else {
                if ($this->model->cambiarPassword($idNutri, $nueva)) {
                    $_SESSION['mensaje'] = "Contraseña actualizada exitosamente.";
                    $_SESSION['tipo_mensaje'] = "success";
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar la contraseña en la base de datos.";
                    $_SESSION['tipo_mensaje'] = "danger";
                }
            }
            header("Location: index.php?action=mi_perfil");
            exit();
        }
    }

    public function actualizar_color_tema() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) session_start();
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            
            // Soporta tanto petición AJAX con JSON o Formulario POST tradicional
            $rawInput = file_get_contents('php://input');
            $jsonInput = json_decode($rawInput, true);
            $color = trim($jsonInput['color_tema'] ?? $_POST['color_tema'] ?? '#2ecc71');
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_POST['is_ajax']) || !empty($jsonInput);
            
            if ($this->model->actualizarColorTema($idNutri, $color)) {
                $_SESSION['ColorTema'] = $color;
                
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'status' => 'success', 
                        'color' => $color, 
                        'message' => 'Color guardado automáticamente.'
                    ]);
                    exit();
                }
                
                $_SESSION['mensaje'] = "Tema de color personalizado guardado exitosamente.";
                $_SESSION['tipo_mensaje'] = "success";
            } else {
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Error al actualizar el color.'
                    ]);
                    exit();
                }
                
                $_SESSION['mensaje'] = "Error al actualizar el color del tema.";
                $_SESSION['tipo_mensaje'] = "danger";
            }
            header("Location: index.php?action=mi_perfil");
            exit();
        }
    }
}
?>
