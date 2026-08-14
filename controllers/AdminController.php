<?php
// controllers/AdminController.php
require_once 'models/Nutricionista.php';
require_once 'config/Conexion.php';

class AdminController {
    private $model;
    private $conexion;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['success' => false, 'message' => 'Acceso denegado. Se requiere rol de administrador.']);
            exit();
        }

        $this->model = new Nutricionista();
        $this->conexion = Conexion::conectar();
    }

    public function dashboard() {
        require_once 'views/admin/dashboard.php';
    }

    public function listar_nutricionistas() {
        require_once 'views/admin/nutricionistas.php';
    }

    public function obtener_nutricionistas() {
        try {
            $sql = "SELECT n.IdNutri, n.DNI, n.Matricula, n.Nombre, n.Apellido, n.Email, n.Estado_Cuenta,
                           (SELECT COUNT(*) FROM paciente p WHERE p.IdNutri = n.IdNutri) AS TotalPacientes
                    FROM nutricionista n
                    WHERE n.Rol = 'nutricionista'
                    ORDER BY n.Nombre ASC, n.Apellido ASC";
            
            $stmt = $this->conexion->query($sql);
            $nutricionistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $nutricionistas]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function guardar_nutricionista() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                $dni = trim($_POST['dni'] ?? '');
                $nombre = trim($_POST['nombre'] ?? '');
                $apellido = trim($_POST['apellido'] ?? '');
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $matricula = trim($_POST['matricula'] ?? '');
                $password = $_POST['password'] ?? '';
                $estado = trim($_POST['estado'] ?? '') ?: 'A';

                // Validar email único
                $sqlCheck = "SELECT IdNutri FROM nutricionista WHERE Email = :email AND IdNutri != :id";
                $stmtCheck = $this->conexion->prepare($sqlCheck);
                $stmtCheck->execute([':email' => $email, ':id' => $id ?: 0]);
                if ($stmtCheck->rowCount() > 0) {
                    throw new Exception('El correo electrónico ya está en uso por otro nutricionista.');
                }

                // Validar DNI único
                if (!empty($dni)) {
                    $sqlCheckDni = "SELECT IdNutri FROM nutricionista WHERE DNI = :dni AND IdNutri != :id";
                    $stmtCheckDni = $this->conexion->prepare($sqlCheckDni);
                    $stmtCheckDni->execute([':dni' => $dni, ':id' => $id ?: 0]);
                    if ($stmtCheckDni->rowCount() > 0) {
                        throw new Exception('El DNI ya está registrado.');
                    }
                }

                // Validar Matrícula única
                if (!empty($matricula)) {
                    $sqlCheckMat = "SELECT IdNutri FROM nutricionista WHERE Matricula = :mat AND IdNutri != :id";
                    $stmtCheckMat = $this->conexion->prepare($sqlCheckMat);
                    $stmtCheckMat->execute([':mat' => $matricula, ':id' => $id ?: 0]);
                    if ($stmtCheckMat->rowCount() > 0) {
                        throw new Exception('La matrícula ya está registrada por otro profesional.');
                    }
                }

                if (empty($id)) {
                    // Create
                    if (empty($password)) $password = '123456';
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    
                    $sql = "INSERT INTO nutricionista (DNI, Nombre, Apellido, Email, Password_Hash, Matricula, Rol, Estado_Cuenta) 
                            VALUES (:dni, :nom, :ape, :email, :hash, :mat, 'nutricionista', :estado)";
                    $stmt = $this->conexion->prepare($sql);
                    $stmt->execute([
                        ':dni' => $dni,
                        ':nom' => $nombre,
                        ':ape' => $apellido,
                        ':email' => $email,
                        ':hash' => $hash,
                        ':mat' => $matricula,
                        ':estado' => $estado
                    ]);
                } else {
                    // Update
                    if (!empty($password)) {
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        $sql = "UPDATE nutricionista 
                                SET DNI = :dni, Nombre = :nom, Apellido = :ape, Email = :email, 
                                    Password_Hash = :hash, Matricula = :mat, Estado_Cuenta = :estado 
                                WHERE IdNutri = :id AND Rol = 'nutricionista'";
                        $stmt = $this->conexion->prepare($sql);
                        $stmt->execute([
                            ':dni' => $dni,
                            ':nom' => $nombre,
                            ':ape' => $apellido,
                            ':email' => $email,
                            ':hash' => $hash,
                            ':mat' => $matricula,
                            ':estado' => $estado,
                            ':id' => $id
                        ]);
                    } else {
                        $sql = "UPDATE nutricionista 
                                SET DNI = :dni, Nombre = :nom, Apellido = :ape, Email = :email, 
                                    Matricula = :mat, Estado_Cuenta = :estado 
                                WHERE IdNutri = :id AND Rol = 'nutricionista'";
                        $stmt = $this->conexion->prepare($sql);
                        $stmt->execute([
                            ':dni' => $dni,
                            ':nom' => $nombre,
                            ':ape' => $apellido,
                            ':email' => $email,
                            ':mat' => $matricula,
                            ':estado' => $estado,
                            ':id' => $id
                        ]);
                    }
                }

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Nutricionista guardado exitosamente.']);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function cambiar_estado_nutricionista() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
                
                $sqlCheck = "SELECT Estado_Cuenta FROM nutricionista WHERE IdNutri = :id AND Rol = 'nutricionista'";
                $stmtCheck = $this->conexion->prepare($sqlCheck);
                $stmtCheck->execute([':id' => $id]);
                $nutri = $stmtCheck->fetch();

                if (!$nutri) throw new Exception('Nutricionista no encontrado.');

                $nuevoEstado = $nutri['Estado_Cuenta'] === 'A' ? 'I' : 'A';

                $sqlUpdate = "UPDATE nutricionista SET Estado_Cuenta = :estado WHERE IdNutri = :id";
                $stmtUpdate = $this->conexion->prepare($sqlUpdate);
                $stmtUpdate->execute([':estado' => $nuevoEstado, ':id' => $id]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.', 'nuevo_estado' => $nuevoEstado]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
}
?>
