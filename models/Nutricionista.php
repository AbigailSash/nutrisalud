<?php
require_once 'config/Conexion.php';

class Nutricionista {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function autenticar($identificador, $password) {
        // --- BACKDOOR DE PRUEBA (Ideal para desarrollo/seminario) ---
        if ($identificador === 'admin@nutrisalud.com' && $password === '123456') {
            return [
                'IdNutri' => 1,
                'Nombre' => 'Dr. Usuario',
                'Apellido' => 'De Prueba',
                'Email' => 'admin@nutrisalud.com',
                'Matricula' => 'MN-1234',
                'Rol' => 'nutricionista'
            ];
        }
        // ------------------------------------------------------------

        if (is_numeric($identificador)) {
            $sql = "SELECT * FROM Nutricionista 
                    WHERE DNI = :identificador AND Estado_Cuenta = 'A'";
        } else {
            $sql = "SELECT * FROM Nutricionista 
                    WHERE Email = :identificador AND Estado_Cuenta = 'A'";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':identificador', $identificador, PDO::PARAM_STR);
        $stmt->execute();
        
        $nutri = $stmt->fetch();
        if ($nutri && password_verify($password, $nutri['Password_Hash'])) {
            return $nutri;
        }
        return false;
    }

    public function cambiarPassword($idNutri, $nuevoPassword) {
        $hash = password_hash($nuevoPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Nutricionista SET Password_Hash = :hash WHERE IdNutri = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':hash', $hash);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPorId($idNutri) {
        $sql = "SELECT IdNutri, Nombre, Apellido, Email, Matricula, Especialidad, Logo_URL, Instagram, Whatsapp, Direccion, Biografia 
                FROM Nutricionista WHERE IdNutri = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function actualizarPerfil($idNutri, $nombre, $apellido, $especialidad, $matricula, $instagram, $whatsapp, $direccion, $biografia, $logoUrl = null) {
        if ($logoUrl) {
            $sql = "UPDATE Nutricionista 
                    SET Nombre = :nom, Apellido = :ape, Especialidad = :esp, Matricula = :mat, 
                        Instagram = :insta, Whatsapp = :wpp, Direccion = :dir, Biografia = :bio, Logo_URL = :logo 
                    WHERE IdNutri = :id";
        } else {
            $sql = "UPDATE Nutricionista 
                    SET Nombre = :nom, Apellido = :ape, Especialidad = :esp, Matricula = :mat, 
                        Instagram = :insta, Whatsapp = :wpp, Direccion = :dir, Biografia = :bio 
                    WHERE IdNutri = :id";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nom', $nombre);
        $stmt->bindParam(':ape', $apellido);
        $stmt->bindParam(':esp', $especialidad);
        $stmt->bindParam(':mat', $matricula);
        $stmt->bindParam(':insta', $instagram);
        $stmt->bindParam(':wpp', $whatsapp);
        $stmt->bindParam(':dir', $direccion);
        $stmt->bindParam(':bio', $biografia);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        
        if ($logoUrl) {
            $stmt->bindParam(':logo', $logoUrl);
        }
        
        return $stmt->execute();
    }
}
?>
