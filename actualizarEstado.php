<?php
include 'conexion.php';

// Usar prepared statements para seguridad
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID de dispositivo inválido']);
    exit;
}

// Obtener estado actual y flujo
$stmt = $conexion->prepare("SELECT estado_valvula, flujo_actual, nombre_dispositivo FROM estado_sistema WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();

if (!$fila) {
    echo json_encode(['error' => 'Dispositivo no encontrado']);
    exit;
}

// Alternar el estado de la válvula
$nuevo_estado = ($fila['estado_valvula'] == 1) ? 0 : 1;
$stmt_update = $conexion->prepare("UPDATE estado_sistema SET estado_valvula = ? WHERE id = ?");
$stmt_update->bind_param("ii", $nuevo_estado, $id);
$stmt_update->execute();

$accion = ($nuevo_estado == 1) ? 'Abierto' : 'Cerrado';
$flujo = $fila['flujo_actual'];
$nombre_dispositivo = $fila['nombre_dispositivo'];

// Registrar el evento
$stmt_log = $conexion->prepare("INSERT INTO log_eventos(dispositivo_id, accion, caudal_momento) VALUES (?, ?, ?)");
$stmt_log->bind_param("isi", $id, $accion, $flujo);
$stmt_log->execute();

// Si se cierra el dispositivo, calcular y guardar consumo
if ($nuevo_estado == 0) {
    // Obtener la última apertura de este dispositivo
    $stmt_abertura = $conexion->prepare(
        "SELECT fecha_hora FROM log_eventos WHERE dispositivo_id = ? AND accion = 'Abierto' 
         ORDER BY fecha_hora DESC LIMIT 1"
    );
    $stmt_abertura->bind_param("i", $id);
    $stmt_abertura->execute();
    $resultado_abertura = $stmt_abertura->get_result();
    $abertura = $resultado_abertura->fetch_assoc();
    
    if ($abertura) {
        // Calcular tiempo en segundos
        $tiempo_abierto = strtotime(date('Y-m-d H:i:s')) - strtotime($abertura['fecha_hora']);
        
        // Calcular consumo en litros (flujo está en L/min)
        $consumo_litros = ($flujo * $tiempo_abierto) / 60;
        
        // Guardar consumo en consumo_final
        $stmt_consumo = $conexion->prepare(
            "INSERT INTO consumo_final(calle_id, litros_totales, tiempo_segundos) 
             VALUES (?, ?, ?)"
        );
        $stmt_consumo->bind_param("idi", $id, $consumo_litros, $tiempo_abierto);
        $stmt_consumo->execute();
    }
}

echo json_encode([
    'status' => 'OK',
    'dispositivo_id' => $id,
    'nombre' => $nombre_dispositivo,
    'accion' => $accion,
    'flujo' => $flujo,
    'nuevo_estado' => $nuevo_estado
]);
?>
