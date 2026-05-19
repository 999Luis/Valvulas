<?php
require('fpdf182/fpdf.php');
include 'conexion.php';

date_default_timezone_set('America/Mexico_City'); 

$hoy = date('Y-m-d');

class PDF extends FPDF {
    function Header() {
        global $hoy;
        $this->SetFont('Arial', 'B', 13);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 10, 'SISTEMA DE CONTROL DE VALVULAS - LA GARITA', 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 5, 'Reporte Diario', 0, 1, 'C');
        $this->Ln(6);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(127, 140, 141);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// --- TÍTULO Y FECHA DEL DÍA ---
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0);
$pdf->Cell(35, 6, 'Fecha del Reporte:', 0, 0);
$pdf->SetFont('Arial', '', 10);
// Imprime la fecha de hoy formateada más amigable (Día/Mes/Año)
$pdf->Cell(0, 6, date('d/m/Y', strtotime($hoy)), 0, 1, 'L');
$pdf->Ln(4);

// --- CABECERA DE LA TABLA (Ancho total: 190) ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(41, 128, 185); // Azul elegante
$pdf->SetTextColor(255);

$pdf->Cell(40, 7, 'Dispositivo', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'Accion', 1, 0, 'C', true);
$pdf->Cell(32, 7, 'Caudal (L/min)', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Tiempo Abierto', 1, 0, 'C', true);
$pdf->Cell(28, 7, 'Consumo Est.', 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Fecha y Hora', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);

// Filtramos las horas extremas del día de hoy: desde las 00:00:00 hasta las 23:59:59
$inicio_dia = $hoy . ' 00:00:00';
$fin_dia    = $hoy . ' 23:59:59';

// Consulta SQL con el filtro estricto para el día de hoy
$query_logs = "SELECT E.id_log, E.dispositivo_id, E.accion, E.caudal_momento, E.fecha_hora, S.nombre_dispositivo 
               FROM log_eventos E 
               JOIN estado_sistema S ON E.dispositivo_id = S.id 
               WHERE E.fecha_hora BETWEEN ? AND ?
               ORDER BY E.fecha_hora DESC";

$stmt = $conexion->prepare($query_logs);
$stmt->bind_param("ss", $inicio_dia, $fin_dia);
$stmt->execute();
$res_logs = $stmt->get_result();

if ($res_logs->num_rows == 0) {
    // Si todavía no hacen pruebas hoy, pinta este aviso centralizado
    $pdf->Cell(190, 8, 'No se han registrado movimientos ni operaciones el dia de hoy.', 1, 1, 'C');
} else {
    while ($log = $res_logs->fetch_assoc()) {
        $tiempo_texto = '-';
        $consumo_texto = '-';
        
        if ($log['accion'] == 'Cerrado') {
            $stmt_apertura = $conexion->prepare(
                "SELECT fecha_hora FROM log_eventos 
                 WHERE dispositivo_id = ? AND accion = 'Abierto' AND fecha_hora < ? 
                 ORDER BY fecha_hora DESC LIMIT 1"
            );
            $stmt_apertura->bind_param("is", $log['dispositivo_id'], $log['fecha_hora']);
            $stmt_apertura->execute();
            $res_apertura = $stmt_apertura->get_result()->fetch_assoc();
            
            if ($res_apertura) {
                $segundos = strtotime($log['fecha_hora']) - strtotime($res_apertura['fecha_hora']);
                if ($segundos > 0) {
                    $minutos = $segundos / 60;
                    $tiempo_texto = ($segundos < 60) ? $segundos . " seg" : round($minutos, 1) . " min";
                    $caudal = $log['caudal_momento'];
                    $litros = ($caudal * $segundos) / 60;
                    $consumo_texto = round($litros, 2) . " L";
                }
            }
        }
        
        // Pintar renglón de datos
        $pdf->Cell(40, 6, $log['nombre_dispositivo'], 1, 0, 'L');
        $pdf->Cell(20, 6, $log['accion'], 1, 0, 'C');
        $pdf->Cell(32, 6, round($log['caudal_momento'], 2) . ' L/min', 1, 0, 'R');
        $pdf->Cell(30, 6, $tiempo_texto, 1, 0, 'C');
        
        if ($consumo_texto != '-') {
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(28, 6, $consumo_texto, 1, 0, 'R');
            $pdf->SetFont('Arial', '', 9);
        } else {
            $pdf->Cell(28, 6, $consumo_texto, 1, 0, 'R');
        }
        
        $pdf->Cell(40, 6, date('d/m/Y H:i:s', strtotime($log['fecha_hora'])), 1, 1, 'C');
    }
}

// Genera el archivo descargable
$pdf->Output('I', 'Reporte_Diario_Valvulas.pdf');
?>