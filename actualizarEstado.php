<?php
include 'conexion.php';

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

// Cambiar el estado de la válvula
$nuevo_estado = ($fila['estado_valvula'] == 1) ? 0 : 1;
$stmt_update = $conexion->prepare("UPDATE estado_sistema SET estado_valvula = ? WHERE id = ?");
$stmt_update->bind_param("ii", $nuevo_estado, $id);
$stmt_update->execute();

$accion = ($nuevo_estado == 1) ? 'Abierto' : 'Cerrado';
$flujo = $fila['flujo_actual'];
$nombre_dispositivo = $fila['nombre_dispositivo'];

// Registrar el evento en la bd
$stmt_log = $conexion->prepare("INSERT INTO log_eventos(dispositivo_id, accion, caudal_momento) VALUES (?, ?, ?)");
$stmt_log->bind_param("isi", $id, $accion, $flujo);
$stmt_log->execute();


if ($nuevo_estado == 0) {
    // 1. Obtener la última apertura
    $stmt_abertura = $conexion->prepare(
        "SELECT fecha_hora FROM log_eventos WHERE dispositivo_id = ? AND accion = 'Abierto' 
         ORDER BY fecha_hora DESC LIMIT 1"
    );
    $stmt_abertura->bind_param("i", $id);
    $stmt_abertura->execute();
    $resultado_abertura = $stmt_abertura->get_result();
    $abertura = $resultado_abertura->fetch_assoc();

    if ($abertura) {
        $fecha_apertura = $abertura['fecha_hora'];
        $fecha_cierre = date('Y-m-d H:i:s');

        // Calcular tiempo real en segundos
        $tiempo_abierto = strtotime($fecha_cierre) - strtotime($fecha_apertura);

        // ← AQUÍ, si el tiempo es mayor a 1 hora, algo salió mal
        if ($tiempo_abierto > 300) {
            $consumo_litros = 0;
            $tiempo_abierto = 0;
        }

        // 2. NUEVO: Calcular el caudal PROMEDIO real que hubo entre la apertura y el cierre
        // Esto evita que un pico de ruido al final arruine toda la métrica
        $stmt_promedio = $conexion->prepare(
            "SELECT AVG(caudal_momento) as flujo_promedio FROM log_eventos 
             WHERE dispositivo_id = ? AND fecha_hora BETWEEN ? AND ?"
        );
        $stmt_promedio->bind_param("iss", $id, $fecha_apertura, $fecha_cierre);
        $stmt_promedio->execute();
        $res_promedio = $stmt_promedio->get_result()->fetch_assoc();

        // Si no hay registros intermedios, usamos el flujo actual por seguridad, mínimo 0
        $flujo_real = ($res_promedio['flujo_promedio'] > 0) ? $res_promedio['flujo_promedio'] : $fila['flujo_actual'];

        // Calcular consumo final en litros basado en el promedio real
        $consumo_litros = ($flujo_real * $tiempo_abierto) / 60;

        // Si el tiempo es menor a 2 segundos o el consumo es ridículamente bajo, lo dejamos en 0
        if ($tiempo_abierto < 2 || $flujo_real < 0.1) {
            $consumo_litros = 0;
        }

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
