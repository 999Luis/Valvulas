<?php
$host = "localhost:3308";
$user = "root";
$pass = "";
$db = "controlvalvulasbd";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

//Validación de los datos recibidos

if (isset($_POST['calle']) && isset($_POST['litros'])) {
    $calle = $_POST['calle'];
    $litros = $_POST['litros'];
    $tiempo = $_POST['segundos'];
    //Guardar datos en la bd
    $sql = "INSERT INTO consumo (calle_id, litros, segundos)
VALUES ('$calle', '$litros', '$tiempo')";

    if (mysqli_query($conexion, $sql)) {
        echo "Registro guardado en la Base de Datos";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }
}
mysqli_close($conexion);
