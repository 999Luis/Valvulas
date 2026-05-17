<?php
require('fpdf182/fpdf.php');
include 'conexion.php';

// Obtener parámetros
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$dispositivo_id = isset($_GET['dispositivo']) ? intval($_GET['dispositivo']) : 0;

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado
$pdf->Cell(0, 10, 'REPORTE DE CONSUMO DE AGUA', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Sistema de Control de Valvulas', 0, 1, 'C');
$pdf->Ln(5);

// Información del reporte
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 5, 'Fecha de Inicio:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $fecha_inicio, 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 5, 'Fecha de Fin:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $fecha_fin, 0, 1);

$pdf->Ln(5);

// Tabla de consumos
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(41, 128, 185);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(30, 7, 'ID', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Dispositivo', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Litros', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Tiempo (min)', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Fecha', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);

// Consultar consumos
$query = "SELECT c.id_consumo, s.nombre_dispositivo, c.litros_totales, 
                 c.tiempo_segundos, c.fecha_registro
          FROM consumo_final c
          JOIN estado_sistema s ON c.calle_id = s.id
          WHERE c.fecha_registro BETWEEN ? AND ?";

$params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
$types = 'ss';

if ($dispositivo_id > 0) {
    $query .= " AND c.calle_id = ?";
    $params[] = $dispositivo_id;
    $types .= 'i';
}

$query .= " ORDER BY c.fecha_registro DESC";

$stmt = $conexion->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();

$total_litros = 0;
$total_tiempo = 0;
$total_registros = 0;

while ($fila = $resultado->fetch_assoc()) {
    $tiempo_min = round($fila['tiempo_segundos'] / 60, 2);
    $litros = round($fila['litros_totales'], 2);
    
    $pdf->Cell(30, 6, $fila['id_consumo'], 1, 0);
    $pdf->Cell(40, 6, substr($fila['nombre_dispositivo'], 0, 15), 1, 0);
    $pdf->Cell(30, 6, $litros . ' L', 1, 0, 'R');
    $pdf->Cell(35, 6, $tiempo_min, 1, 0, 'R');
    $pdf->Cell(35, 6, date('d/m H:i', strtotime($fila['fecha_registro'])), 1, 1);
    
    $total_litros += $litros;
    $total_tiempo += $fila['tiempo_segundos'];
    $total_registros++;
}

// Totales
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(236, 240, 241);
$total_tiempo_min = round($total_tiempo / 60, 2);
$pdf->Cell(30, 7, '', 1);
$pdf->Cell(40, 7, 'TOTALES:', 1, 0, 'R', true);
$pdf->Cell(30, 7, round($total_litros, 2) . ' L', 1, 0, 'R', true);
$pdf->Cell(35, 7, $total_tiempo_min, 1, 0, 'R', true);
$pdf->Cell(25, 7, '', 1);
$pdf->Cell(35, 7, '', 1, 1, true);

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'Reporte generado: ' . date('d/m/Y H:i:s'), 0, 1);
$pdf->Cell(0, 5, 'Total registros: ' . $total_registros, 0, 1);
if ($total_registros > 0) {
    $promedio_litros = round($total_litros / $total_registros, 2);
    $pdf->Cell(0, 5, 'Consumo promedio: ' . $promedio_litros . ' L por ciclo', 0, 1);
}

// Generar PDF
$pdf->Output('D', 'reporte_consumo_' . date('Ymd_His') . '.pdf');
?>
