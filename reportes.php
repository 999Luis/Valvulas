<?php
include 'conexion.php';

// Parámetros por defecto: solo fecha actual
$fecha_inicio = date('Y-m-d');
$fecha_fin = date('Y-m-d');

// Consultar consumos
$query = "SELECT c.id_consumo, s.nombre_dispositivo, s.id as dispositivo_id, c.litros_totales, 
                 c.tiempo_segundos, c.fecha_registro
          FROM consumo_final c
          JOIN estado_sistema s ON c.calle_id = s.id
          WHERE c.fecha_registro BETWEEN ? AND ?";

$params = [$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59'];
$types = 'ss';

$query .= " ORDER BY c.fecha_registro DESC";

$stmt = $conexion->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$consumos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calcular estadísticas
$total_litros = 0;
$total_tiempo = 0;
$consumo_por_dispositivo = [];
$consumo_por_dia = [];
$historial_litros = [];
$historial_fechas = [];

foreach ($consumos as $registro) {
    $total_litros += $registro['litros_totales'];
    $total_tiempo += $registro['tiempo_segundos'];
}

$promedio_litros = count($consumos) > 0 ? round($total_litros / count($consumos), 2) : 0;
$promedio_tiempo = count($consumos) > 0 ? round($total_tiempo / count($consumos), 2) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Control de Válvulas</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .filtros {
            background: #f8f9fa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        
        .filtro-grupo {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filtro-grupo label {
            font-weight: bold;
            font-size: 12px;
        }
        
        .btn-pdf {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-pdf:hover {
            background: #c0392b;
        }
        
        .estadisticas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .tarjeta-stats {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        
        .tarjeta-stats h3 {
            margin: 0 0 10px 0;
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .tarjeta-stats .valor {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .tabla-consumo {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th {
            background: #34495e;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="menu">
        <h1>Control de Válvulas - Reportes</h1>
        <nav class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="historial.php">Historial</a>
            <a href="reportes.php">Reportes</a>
        </nav>
    </div>

    <div class="container">
        <!-- Filtros -->
        <div class="filtros" style="align-items: center;">
            <div class="filtro-grupo">
                <label style="color: black;">Fecha: <?php echo date('d/m/Y'); ?></label>
            </div>
            <?php
            $pdf_url = 'generarReporte.php?fecha_inicio=' . urlencode($fecha_inicio) . 
                       '&fecha_fin=' . urlencode($fecha_fin);
            ?>
            <a href="<?php echo $pdf_url; ?>" class="btn-pdf">Descargar PDF</a>
        </div>

        <!-- Estadísticas -->
        <div class="estadisticas">
            <div class="tarjeta-stats">
                <h3>Consumo Total</h3>
                <div class="valor"><?php echo round($total_litros, 2); ?> L</div>
            </div>
            
            <div class="tarjeta-stats" style="border-left-color: #2ecc71;">
                <h3>Consumo Promedio</h3>
                <div class="valor"><?php echo $promedio_litros; ?> L</div>
            </div>
            
            <div class="tarjeta-stats" style="border-left-color: #f39c12;">
                <h3>Tiempo Total</h3>
                <div class="valor"><?php echo round($promedio_tiempo); ?> min</div>
            </div>
        </div>

        <!-- Tabla de Consumos -->
        <?php if (!empty($consumos)): ?>
        <div class="tabla-consumo">
            <h3 style="padding: 20px 20px 0; margin: 0;">Detalle de Consumos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Dispositivo</th>
                        <th>Consumo (L)</th>
                        <th>Tiempo (min)</th>
                        <th>Flujo Prom. (L/min)</th>
                        <th>Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consumos as $registro): 
                        $tiempo_min = round($registro['tiempo_segundos'] / 60, 2);
                        $flujo_promedio = $tiempo_min > 0 ? round($registro['litros_totales'] / $tiempo_min, 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['nombre_dispositivo']); ?></td>
                        <td><?php echo round($registro['litros_totales'], 2); ?> L</td>
                        <td><?php echo $tiempo_min; ?></td>
                        <td><?php echo $flujo_promedio; ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($registro['fecha_registro'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px;">
            <p style="color: #7f8c8d; font-size: 16px;">No hay datos de consumo para el rango seleccionado.</p>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
