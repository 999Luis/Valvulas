<?php
$host = "localhost:3308";
$user = "root";
$pass = "";
$db   = "controlvalvulasbd";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>