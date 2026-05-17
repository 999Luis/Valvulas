<?php
include 'conexion.php';

//Consulta del historial
$consulta = "SELECT S.nombre_dispositivo, E.accion, E.caudal_momento, E.fecha_hora
        FROM log_eventos E
        JOIN estado_sistema S ON E.dispositivo_id = S.id
        ORDER BY E.fecha_hora DESC LIMIT 10"; 

$res = mysqli_query($conexion, $consulta);
$eventos = [];

while($fila = mysqli_fetch_assoc($res)) {
    $eventos[] = $fila;
}

echo json_encode($eventos);
?>