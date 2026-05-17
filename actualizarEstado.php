<?php
include 'conexion.php';
$id = $_GET['id'];

mysqli_query($conexion, "UPDATE estado_sistema SET estado_valvula = NOT estado_valvula WHERE id = '$id'");
$consulta = mysqli_query($conexion, "SELECT estado_valvula, flujo_actual FROM estado_sistema WHERE id = '$id'");
$fila = mysqli_fetch_assoc($consulta);

$accion = ($fila['estado_valvula'] == 1) ? 'Abierto' : 'Cerrado';
$flujo = $fila['flujo_actual'];

mysqli_query($conexion, "INSERT INTO log_eventos(dispositivo_id, accion, caudal_momento)
                         VALUES ('$id', '$accion', '$flujo')");
echo "OK";
?>