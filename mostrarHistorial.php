<?php
header('Content-Type: application/json');
include 'conexion.php';

// Usar prepared statements
$stmt = $conexion->prepare(
    "SELECT S.nombre_dispositivo, E.accion, E.caudal_momento, E.fecha_hora
     FROM log_eventos E
     JOIN estado_sistema S ON E.dispositivo_id = S.id
     ORDER BY E.fecha_hora DESC LIMIT 10"
);

$stmt->execute();
$res = $stmt->get_result();
$eventos = [];

while($fila = $res->fetch_assoc()) {
    $eventos[] = [
        'dispositivo' => $fila['nombre_dispositivo'],
        'accion' => $fila['accion'],
        'caudal' => round($fila['caudal_momento'], 2),
        'fecha_hora' => date('d/m/Y H:i:s', strtotime($fila['fecha_hora']))
    ];
}

echo json_encode($eventos);
?>
