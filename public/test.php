<?php
// 1. Importamos las clases necesarias desde sus nuevas ubicaciones
require_once "../app/Config/Database.php";
require_once "../app/Models/Nutricionista.php";

// 2. Instanciamos la conexión y obtenemos el enlace activo
$database = new Conexion();
$db = $database->conectar();

if ($db) {
    echo "<h3>¡Conexión POO exitosa a la Base de Datos NutriSalud!</h3>";

    // 3. Instanciamos el objeto de la clase Nutricionista
    $nutri = new Nutricionista($db);

    // 4. Asignamos valores a las propiedades del objeto (Datos de prueba)
    $nutri->idNutri = 1;
    $nutri->matricula = "MN-9876";
    $nutri->nombre = "María Alejandra";
    $nutri->apellido = "Gómez";
    $nutri->email = "mariale.nutri@email.com";
    $nutri->telefono = "3704123456";
    $nutri->estadoCuenta = "A"; // 'A' de Activo

    // 5. Invocamos al método para guardarlo
    // En este entorno de prueba, comento la creación para que no lance error por duplicado si refrescas:
    /*
    if($nutri->crear()) {
        echo "<p style='color: green;'>-> El Nutricionista se registró exitosamente.</p>";
    } else {
        echo "<p style='color: red;'>-> Hubo un error al registrar el profesional.</p>";
    }
    */

    // 6. Consultamos la tabla para listar
    echo "<h4>Listado actual de Nutricionistas en el sistema:</h4>";
    $datos = $nutri->leerTodo();
    
    while ($fila = $datos->fetch(PDO::FETCH_ASSOC)) {
        echo "<b>ID:</b> " . $fila['IdNutri'] . " | " .
             "<b>Profesional:</b> " . $fila['Nombre'] . " " . $fila['Apellido'] . " | " .
             "<b>Matrícula:</b> " . $fila['Matricula'] . "<br>";
    }

    echo "<br><a href='index.php'>Volver al Dashboard MVC</a>";
}
?>
