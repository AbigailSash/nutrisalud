<?php
require_once 'models/Nutricionista.php';

class PerfilController {
    private $model;

    public function __construct() {
        $this->model = new Nutricionista();
    }

    public function mi_perfil() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $idNutri = $_SESSION['IdNutri'] ?? 1;
        $perfil = $this->model->obtenerPorId($idNutri);
        
        require_once 'views/perfil/index.php';
    }

    public function actualizar_perfil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $especialidad = $_POST['especialidad'] ?? '';
            $matricula = $_POST['matricula'] ?? '';
            $instagram = $_POST['instagram'] ?? '';
            $whatsapp = $_POST['whatsapp'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $biografia = $_POST['biografia'] ?? '';
            
            $logoUrl = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                // Simplificación: subir logo a assets/
                $uploadDir = 'public/assets/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['logo']['name']);
                $uploadPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    $logoUrl = $uploadPath;
                }
            }
            
            if ($this->model->actualizarPerfil($idNutri, $nombre, $apellido, $especialidad, $matricula, $instagram, $whatsapp, $direccion, $biografia, $logoUrl)) {
                // Sincronización del "Estado Global" (Persistencia en sesión)
                $_SESSION['NombreNutri'] = $nombre;
                $_SESSION['ApellidoNutri'] = $apellido;
                $_SESSION['EspecialidadNutri'] = $especialidad;
                $_SESSION['MatriculaNutri'] = $matricula;
                if ($logoUrl) {
                    $_SESSION['LogoNutri'] = $logoUrl;
                }
                
                if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mi_perfil");
                exit();
            } else {
                echo "Error al actualizar el perfil.";
            }
        }
    }

    public function actualizar_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $idNutri = $_SESSION['IdNutri'] ?? 1;
            
            // En un sistema real verificaríamos password actual, omitido por simplicidad
            $nueva = $_POST['nueva_password'] ?? '';
            $confirmar = $_POST['confirmar_password'] ?? '';
            
            if ($nueva === $confirmar && strlen($nueva) >= 6) {
                if ($this->model->cambiarPassword($idNutri, $nueva)) {
                    if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mi_perfil");
                    exit();
                }
            }
            if (session_status() == PHP_SESSION_NONE) session_start();
            $_SESSION['mensaje'] = "Operación realizada correctamente.";
            $_SESSION['tipo_mensaje'] = "success";
            header("Location: index.php?action=mi_perfil");
            exit();
        }
    }
}
?>
