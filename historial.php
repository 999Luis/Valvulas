<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Control de Agua</title>
</head>
<div class="menu">
    <h1>Historial</h1>
    <nav class="nav-links">
        <a href="index.php">Inicio</a>
        <a href="historial.php">Historial</a>
    </nav>
</div>

<body>
    <div style="text-align: right; margin-bottom: 15px;">
    <a href="generarReporte.php" target="_blank" style="background-color: #2980b9; color: white; padding: 10px 20px; margin-top: 10px; text-align: center; text-decoration: none; font-family: 'Poppins', sans-serif; font-weight: bold; border-radius: 5px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: background 0.3s;">
        Descargar Reporte
    </a>
</div>
    <div class="contenedores">
        <table>
            <thead>
                <th>Válvula</th>
                <th>Acción</th>
                <th>Flujo</th>
                <th>Fecha</th>
            </thead>
            <tbody id="contenedor">

            </tbody>
        </table>
    </div>

    <script>
        function mostrarHistorial() {
            fetch('mostrarHistorial.php')
                .then(res => res.json())
                .then(eventos => {
                    const tablaBody = document.getElementById("contenedor");
                    tablaBody.innerHTML = "";
                    eventos.forEach(evento => {
                        let fila = `
                                <tr>
                                    <td>${evento.dispositivo}</td>
                                    <td>${evento.accion}</td>
                                    <td>${evento.caudal}</td>
                                    <td>${evento.fecha_hora}</td>
                                </tr>
                            `;
                        tablaBody.innerHTML += fila;
                    });
                });
        }
        mostrarHistorial();
        setInterval(mostrarHistorial, 3000);
    </script>
</body>

</html>