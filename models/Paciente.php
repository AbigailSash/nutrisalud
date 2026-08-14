<?php
// models/Paciente.php
require_once 'config/Conexion.php';

class Paciente {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function crear($dni, $nombre, $apellido, $fecha_nacimiento, $telefono, $email, $idNutri, $obra_social = 'Particular') {
        // Por defecto la contraseña inicial es el DNI
        $password = password_hash($dni, PASSWORD_BCRYPT);
        $sql = "INSERT INTO paciente (DNI, Nombre, Apellido, Fecha_Nacimiento, Telefono, Email, Password, IdNutri, Obra_Social) 
                VALUES (:dni, :nombre, :apellido, :fecha_nac, :telefono, :email, :password, :id_nutri, :obra_social)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nac', $fecha_nacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->bindParam(':obra_social', $obra_social, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function leerPorNutricionista($idNutri) {
        // Aislamiento de datos Multi-Profesional garantizado por el WHERE IdNutri
        $sql = "SELECT * FROM paciente WHERE IdNutri = :id_nutri ORDER BY Apellido ASC, Nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($idPaciente, $idNutri) {
        $sql = "SELECT * FROM paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorIdSolo($idPaciente) {
        $sql = "SELECT * FROM paciente WHERE IdPaciente = :id_paciente";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($idPaciente, $dni, $nombre, $apellido, $fecha_nacimiento, $telefono, $email, $idNutri, $peso = null, $estatura = null, $sexo = null, $actividad = null, $obra_social = 'Particular') {
        $sql = "UPDATE paciente 
                SET DNI = :dni, Nombre = :nombre, Apellido = :apellido, Fecha_Nacimiento = :fecha_nac, 
                Telefono = :telefono, Email = :email, Peso = :peso, Estatura = :estatura, Sexo = :sexo, Actividad = :actividad, Obra_Social = :obra_social
                WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nac', $fecha_nacimiento, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':estatura', $estatura);
        $stmt->bindParam(':sexo', $sexo, PDO::PARAM_STR);
        $stmt->bindParam(':actividad', $actividad);
        $stmt->bindParam(':obra_social', $obra_social, PDO::PARAM_STR);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarCredenciales($idPaciente, $password, $fotoPath) {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE paciente SET Password = :password, FotoPerfil = :foto WHERE IdPaciente = :id_paciente";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':password', $hashed, PDO::PARAM_STR);
            $stmt->bindParam(':foto', $fotoPath, PDO::PARAM_STR);
            $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        } else {
            $sql = "UPDATE paciente SET FotoPerfil = :foto WHERE IdPaciente = :id_paciente";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':foto', $fotoPath, PDO::PARAM_STR);
            $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    public function eliminar($idPaciente, $idNutri) {
        // El aislamiento con IdNutri asegura que solo borre si el paciente pertenece a ese profesional.
        $sql = "DELETE FROM paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function autenticar($dni, $email_or_password) {
        // Buscamos al paciente por DNI
        $sql = "SELECT * FROM paciente WHERE DNI = :dni LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($paciente) {
            $esValido = false;
            // 1. Verificar si lo ingresado coincide con el hash de la contraseña (por defecto es su DNI)
            if (!empty($paciente['Password']) && password_verify($email_or_password, $paciente['Password'])) {
                $esValido = true;
            }
            // 2. Por retrocompatibilidad, verificar si la contraseña coincide con el DNI en texto plano o coincide con su email
            if (!$esValido && ($paciente['Password'] === $email_or_password || $paciente['Email'] === $email_or_password || $paciente['DNI'] === $email_or_password)) {
                $esValido = true;
                // Auto-upgrade a bcrypt si estaba en texto plano
                $this->actualizarCredenciales($paciente['IdPaciente'], $email_or_password, $paciente['FotoPerfil']);
            }

            if ($esValido) {
                return $paciente;
            }
        }
        return false;
    }
}
?>
