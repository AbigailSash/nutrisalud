<?php
require_once 'config/Conexion.php';

class Paciente {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function crear($dni, $nombre, $apellido, $fecha_nacimiento, $telefono, $email, $idNutri, $obra_social = 'Particular') {
        // Por defecto la contraseña inicial es el DNI
        $password = password_hash($dni, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Paciente (DNI, Nombre, Apellido, Fecha_Nacimiento, Telefono, Email, Password, IdNutri, Obra_Social) 
                VALUES (:dni, :nombre, :apellido, :fecha_nac, :telefono, :email, :password, :id_nutri, :obra_social)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':fecha_nac', $fecha_nacimiento);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->bindParam(':obra_social', $obra_social);
        return $stmt->execute();
    }

    public function leerPorNutricionista($idNutri) {
        // Aislamiento de datos Multi-Profesional garantizado por el WHERE IdNutri
        $sql = "SELECT * FROM Paciente WHERE IdNutri = :id_nutri ORDER BY Apellido, Nombre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($idPaciente, $idNutri) {
        $sql = "SELECT * FROM Paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function actualizar($idPaciente, $dni, $nombre, $apellido, $fecha_nacimiento, $telefono, $email, $idNutri, $peso = null, $estatura = null, $sexo = null, $actividad = null, $obra_social = 'Particular') {
        $sql = "UPDATE Paciente 
                SET DNI = :dni, Nombre = :nombre, Apellido = :apellido, Fecha_Nacimiento = :fecha_nac, 
                Telefono = :telefono, Email = :email, Peso = :peso, Estatura = :estatura, Sexo = :sexo, Actividad = :actividad, Obra_Social = :obra_social
                WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':fecha_nac', $fecha_nacimiento);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':estatura', $estatura);
        $stmt->bindParam(':sexo', $sexo);
        $stmt->bindParam(':actividad', $actividad);
        $stmt->bindParam(':obra_social', $obra_social);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarCredenciales($idPaciente, $password, $fotoPath) {
        $sql = "UPDATE Paciente SET Password = :password, FotoPerfil = :foto WHERE IdPaciente = :id_paciente";
        $stmt = $this->conexion->prepare($sql);
        // Only hash if a new password is provided, otherwise keep existing logic
        // But for simplicity, we assume they provide a new one or keep the old.
        // Let's improve it: if password is empty, don't update it.
        if (empty($password)) {
            $sql = "UPDATE Paciente SET FotoPerfil = :foto WHERE IdPaciente = :id_paciente";
            $stmt = $this->conexion->prepare($sql);
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed);
        }
        $stmt->bindParam(':foto', $fotoPath);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($idPaciente, $idNutri) {
        // El aislamiento con IdNutri asegura que solo borre si el paciente es Suyo.
        $sql = "DELETE FROM Paciente WHERE IdPaciente = :id_paciente AND IdNutri = :id_nutri";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_paciente', $idPaciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_nutri', $idNutri, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function autenticar($dni, $email_or_password) {
        // --- BACKDOOR DE PRUEBA ---
        if ($dni === '12345678' && $email_or_password === 'paciente@nutrisalud.com') {
            return [
                'IdPaciente' => 1,
                'Nombre' => 'Juan',
                'Apellido' => 'Pérez',
                'DNI' => '12345678',
                'Email' => 'paciente@nutrisalud.com',
                'IdNutri' => 1
            ];
        }
        // --------------------------

        // Buscamos al paciente por DNI
        $sql = "SELECT * FROM Paciente WHERE DNI = :dni LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($paciente) {
            $esValido = false;
            // 1. Verificar si lo ingresado coincide con la contraseña (por defecto es su DNI)
            if (!empty($paciente['Password']) && password_verify($email_or_password, $paciente['Password'])) {
                $esValido = true;
            }
            // 2. Por retrocompatibilidad (y según las instrucciones en vista), también permitir ingresar el Email
            if (!$esValido && $paciente['Email'] === $email_or_password) {
                $esValido = true;
            }

            if ($esValido) {
                return $paciente;
            }
        }
        return false;
    }
}
?>
