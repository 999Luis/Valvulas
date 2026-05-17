<?php
header('Content-Type: text/plain');
include 'conexion.php';

// Recibir datos de la ESP con validación
$sf1 = isset($_POST['sf1']) ? floatval($_POST['sf1']) : 0;
$sf2 = isset($_POST['sf2']) ? floatval($_POST['sf2']) : 0;
$sf3 = isset($_POST['sf3']) ? floatval($_POST['sf3']) : 0;
$nivel = isset($_POST['nivel']) ? floatval($_POST['nivel']) : 0;

// Usar prepared statements para actualizar las lecturas
$stmt1 = $conexion->prepare("UPDATE estado_sistema SET flujo_actual = ?, nivel_tanque_pct = ? WHERE id = 1");
$stmt1->bind_param("dd", $sf1, $nivel);
$stmt1->execute();

$stmt2 = $conexion->prepare("UPDATE estado_sistema SET flujo_actual = ? WHERE id = 2");
$stmt2->bind_param("d", $sf2);
$stmt2->execute();

$stmt3 = $conexion->prepare("UPDATE estado_sistema SET flujo_actual = ? WHERE id = 3");
$stmt3->bind_param("d", $sf3);
$stmt3->execute();

// Obtener los estados de las válvulas
$stmt_estados = $conexion->prepare("SELECT id, estado_valvula FROM estado_sistema ORDER BY id");
$stmt_estados->execute();
$resultado = $stmt_estados->get_result();

$ordenes = [];
while ($fila = $resultado->fetch_assoc()) {
    $ordenes[] = $fila['estado_valvula'];
}

echo implode(",", $ordenes);
?>
