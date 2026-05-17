<?php
header('Content-Type: application/json');
include 'conexion.php';

// Usar prepared statements
$stmt = $conexion->prepare("SELECT id, flujo_actual, nivel_tanque_pct, estado_valvula FROM estado_sistema ORDER BY id");
$stmt->execute();
$res = $stmt->get_result();

$datos = [];

while ($fila = $res->fetch_assoc()) {
    $datos["sf" . $fila['id']] = round($fila['flujo_actual'], 2);
    $datos["estado" . $fila['id']] = $fila['estado_valvula'];
    
    if ($fila['id'] == 1) {
        $datos["sp"] = round($fila['nivel_tanque_pct'], 2);
    }
}

for ($i = 1; $i <= 3; $i++) {
    $stmt_consumo = $conexion->prepare(
        "SELECT litros_totales FROM consumo_final WHERE calle_id = ? ORDER BY fecha_registro DESC LIMIT 1"
    );
    $stmt_consumo->bind_param("i", $i);
    $stmt_consumo->execute();
    $resultado_consumo = $stmt_consumo->get_result();
    $fila_consumo = $resultado_consumo->fetch_assoc();
    $datos["ultimo" . $i] = $fila_consumo ? round($fila_consumo['litros_totales'], 2) : 0;
}

echo json_encode($datos);
?>
