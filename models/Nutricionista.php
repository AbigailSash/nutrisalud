<?php
// models/Nutricionista.php
require_once 'config/Conexion.php';

class Nutricionista {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function autenticar($identificador, $password) {
        if (is_numeric($identificador)) {
            $sql = "SELECT * FROM nutricionista 
                    WHERE DNI = :identificador AND Estado_Cuenta = 'A'";
        } else {
            $sql = "SELECT * FROM nutricionista 
                    WHERE Email = :identificador AND Estado_Cuenta = 'A'";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':identificador', $identificador, PDO::PARAM_STR);
        $stmt->execute();
        
        $nutri = $stmt->fetch();
        if ($nutri && !empty($nutri['Password_Hash']) && password_verify($password, $nutri['Password_Hash'])) {
            return $nutri;
        }
        return false;
    }

    public function cambiarPassword($idNutri, $nuevoPassword) {
        $hash = password_hash($nuevoPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE nutricionista SET Password_Hash = :hash WHERE IdNutri = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerPorId($idNutri) {
        $sql = "SELECT IdNutri, DNI, Matricula, Nombre, Apellido, Email, Rol, Telefono, Estado_Cuenta, Especialidad, Logo_URL, Instagram, Whatsapp, Direccion, Biografia, Color_Tema 
                FROM nutricionista WHERE IdNutri = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM nutricionista WHERE Email = :email AND Estado_Cuenta = 'A' LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarColorTema($idNutri, $colorTema) {
        // Validar formato hex color
        if (!preg_match('/^#[a-f0-9]{6}$/i', $colorTema) && !preg_match('/^#[a-f0-9]{3}$/i', $colorTema)) {
            $colorTema = '#2ecc71';
        }
        $sql = "UPDATE nutricionista SET Color_Tema = :color WHERE IdNutri = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':color', $colorTema, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarPerfil($idNutri, $nombre, $apellido, $especialidad, $matricula, $instagram, $whatsapp, $direccion, $biografia, $logoUrl = null) {
        if ($logoUrl) {
            $sql = "UPDATE nutricionista 
                    SET Nombre = :nom, Apellido = :ape, Especialidad = :esp, Matricula = :mat, 
                        Instagram = :insta, Whatsapp = :wpp, Direccion = :dir, Biografia = :bio, Logo_URL = :logo 
                    WHERE IdNutri = :id";
        } else {
            $sql = "UPDATE nutricionista 
                    SET Nombre = :nom, Apellido = :ape, Especialidad = :esp, Matricula = :mat, 
                        Instagram = :insta, Whatsapp = :wpp, Direccion = :dir, Biografia = :bio 
                    WHERE IdNutri = :id";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nom', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':ape', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':esp', $especialidad, PDO::PARAM_STR);
        $stmt->bindParam(':mat', $matricula, PDO::PARAM_STR);
        $stmt->bindParam(':insta', $instagram, PDO::PARAM_STR);
        $stmt->bindParam(':wpp', $whatsapp, PDO::PARAM_STR);
        $stmt->bindParam(':dir', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $biografia, PDO::PARAM_STR);
        $stmt->bindParam(':id', $idNutri, PDO::PARAM_INT);
        
        if ($logoUrl) {
            $stmt->bindParam(':logo', $logoUrl, PDO::PARAM_STR);
        }
        
        return $stmt->execute();
    }
}
?>
