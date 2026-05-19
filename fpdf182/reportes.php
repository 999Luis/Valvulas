<?php
include 'conexion.php';

// Últimos 10 registros de consumo
$stmt = $conexion->prepare(
    "SELECT c.id_consumo, s.nombre_dispositivo, c.litros_totales, 
            c.tiempo_segundos, c.fecha_registro
     FROM consumo_final c
     JOIN estado_sistema s ON c.calle_id = s.id
     ORDER BY c.fecha_registro DESC
     LIMIT 10"
);
$stmt->execute();
$consumos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_litros    = 0;
$total_tiempo    = 0;
$total_registros = count($consumos);

foreach ($consumos as $registro) {
    $total_litros += $registro['litros_totales'];
    $total_tiempo += $registro['tiempo_segundos'];
}

$promedio_litros  = $total_registros > 0 ? round($total_litros / $total_registros, 2) : 0;
$total_tiempo_min = round($total_tiempo / 60, 2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Control de Válvulas</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar span {
            font-size: 14px;
            color: #7f8c8d;
        }

        .btn-pdf {
            padding: 8px 20px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-pdf:hover { background: #c0392b; }

        .estadisticas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .tarjeta-stats {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }

        .tarjeta-stats h3 {
            margin: 0 0 8px 0;
            color: #7f8c8d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        .tabla-consumo h3 {
            padding: 16px 20px;
            margin: 0;
            font-size: 14px;
            color: #2c3e50;
            border-bottom: 1px solid #ecf0f1;
        }

        table { width: 100%; border-collapse: collapse; }

        table th {
            background: #34495e;
            color: white;
            padding: 11px 14px;
            text-align: left;
            font-size: 13px;
        }

        table td {
            padding: 11px 14px;
            border-bottom: 1px solid #ecf0f1;
            font-size: 13px;
            color: whitesmoke;
        }

        table tr:last-child td { border-bottom: none; }
        table tr:hover td { background: #34495e; }

        .empty {
            padding: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="menu">
        <h1>Reportes</h1>
        <nav class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="historial.php">Historial</a>
            <a href="reportes.php">Reportes</a>
        </nav>
    </div>

    <div class="container">

        <div class="top-bar">
            <span>Últimos <?php echo $total_registros; ?> registros — <?php echo date('d/m/Y'); ?></span>
            <a href="generarReporte.php" class="btn-pdf">⬇ Descargar PDF</a>
        </div>

        <!-- Tarjetas -->
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
                <div class="valor"><?php echo $total_tiempo_min; ?> min</div>
            </div>
        </div>

        <!-- Tabla -->
        
            <?php if (!empty($consumos)): ?>
            <h3>Detalle de consumos</h3>
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
                    <?php foreach ($consumos as $r):
                        $t_min  = round($r['tiempo_segundos'] / 60, 2);
                        $flujo  = $t_min > 0 ? round($r['litros_totales'] / $t_min, 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['nombre_dispositivo']); ?></td>
                        <td><?php echo round($r['litros_totales'], 2); ?> L</td>
                        <td><?php echo $t_min; ?></td>
                        <td><?php echo $flujo; ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($r['fecha_registro'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty">No hay registros de consumo aún.</div>
            <?php endif; ?>

    </div>
</body>
</html>