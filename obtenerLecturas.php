<?php
include 'conexion.php';

$res = mysqli_query($conexion, "SELECT id, flujo_actual, nivel_tanque, estado_valvula FROM estado_sistema");

$datos = [];

while ($fila = mysqli_fetch_assoc($res)){
    $datos["sf" . $fila['id']] = $fila['flujo_actual'];
    if ($fila['id'] == 1) {
        $datos["sp"] = $fila['nivel_tanque'];
        $datos["estado" . $fila['id']] = $fila['estado_valvula'];
    }
}
echo json_encode($datos);
?>