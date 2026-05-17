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
    <div class="contenedores">
        <table>
            <thead>
                <th>Válvula</th>
                <th>Acción</th>
                <th>Caudal</th>
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
                                    <td>${evento.nombre_dispositivo}</td>
                                    <td>${evento.accion}</td>
                                    <td>${evento.caudal_momento}</td>
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