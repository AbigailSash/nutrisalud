<?php
class Nutricionista {
    // Conexión interna de la base de datos
    private $conexion;
    private $tabla = "Nutricionista";

    // Atributos del objeto
    public $idNutri;
    public $matricula;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $estadoCuenta;

    public function __construct($db) {
        $this->conexion = $db;
    }

    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " (IdNutri, Matricula, Nombre, Apellido, Email, Telefono, Estado_Cuenta) 
                  VALUES (:id, :matricula, :nombre, :apellido, :email, :telefono, :estado)";

        $stmt = $this->conexion->prepare($query);

        $stmt->bindParam(":id", $this->idNutri);
        $stmt->bindParam(":matricula", $this->matricula);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":estado", $this->estadoCuenta);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function leerTodo() {
        $query = "SELECT * FROM " . $this->tabla;
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
