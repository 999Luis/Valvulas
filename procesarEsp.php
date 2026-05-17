<?php
include 'conexion.php';

// Recibir datos de la ESP
$sf1 = $_POST['sf1'] ?? 0;
$sf2 = $_POST['sf2'] ?? 0;
$sf3 = $_POST['sf3'] ?? 0;
$nivel = $_POST['nivel'] ?? 0;

// Actualizar las lecturas
mysqli_query($conexion, "UPDATE estado_sistema SET flujo_actual = '$sf1', nivel_tanque = '$nivel' WHERE id = 1");
mysqli_query($conexion, "UPDATE estado_sistema SET flujo_actual = '$sf2' WHERE id = 2");
mysqli_query($conexion, "UPDATE estado_sistema SET flujo_actual = '$sf3' WHERE id = 3");

// Obtener los estados de las válvulas
$res = mysqli_query($conexion, "SELECT id, estado_valvula FROM estado_sistema");
$ordenes = [];
while ($fila = mysqli_fetch_assoc($res)) {
    $ordenes[] = $fila['estado_valvula'];
}

echo implode(",", $ordenes); 
?>