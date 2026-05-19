<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilos.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .alerta-tanque {
            color: #e74c3c;;
            margin-left: 10px;
            margin-top: 10px;
            display: inline-block;
            font-size: 20px;
        }

        .info-adicional {
            display: block;
            margin-top: 8px;
            color: #555;
            font-size: 0.95rem;
        }
    </style>
    <title>Control de Agua</title>
</head>
<div class="menu">
    <h1>Control de valvulas</h1>
    <nav class="nav-links ">
        <a href="#">Inicio</a>
        <a href="historial.php">Historial</a>
    </nav>
</div>

<body>
    <div class="contenedores">
        <div>
            <section class="contenedor">
                <article class="card" id="bomba">
                    <span class="calle">Bomba principal</span>
                    <span class="textInf" id="flujo1"> Flujo: 0.0 L/min</span>
                    <span class="textInf info-adicional" id="ultimoConsumo1" style="color:#00d4ff">Último consumo: 0 L</span>
                    <button class="btn" onclick="abrirValvula(1)" id="btnBomba">Abrir Bomba</button>
                </article>
            </section>
            <section class="contenedor">
                <article class="card" id="valvula1">
                    <span class="calle">Calle 1</span>
                    <span class="textInf" id="flujo2"> Flujo: 0.0 L/min</span>
                    <span class="textInf info-adicional" id="ultimoConsumo2" style="color:#00d4ff">Último consumo: 0 L</span>
                    <button class="btn" onclick="abrirValvula(2)" id="btnV1">Abrir Válvula</button>
                </article>
                <article class="card" id="valvula2">
                    <span class="calle">Calle 2</span>
                    <span class="textInf" id="flujo3"> Flujo: 0.0 L/min</span>
                    <span class="textInf info-adicional" id="ultimoConsumo3" style="color:#00d4ff">Último consumo: 0 L</span>
                    <button class="btn" onclick="abrirValvula(3)" id="btnV2">Abrir Válvula</button>
                </article>
            </section>
        </div>
        <div class="informacion">
            <h2 id="info">Información</h2>
            <!-- nivel del tanque en porcentaje -->
            <article class="card" id="infoNvl">
                <span class="calle">Nivel del tanque</span>
                <span class="textInf" id="nivelTanque"> Nivel: 0%</span>
                <span class="alerta-tanque" id="alertaTanque"></span>
            </article>
        </div>
    </div>

    <script>
        // Cambiar el estado de las válvulas en la BD
        function abrirValvula(idCalle) {
            fetch("actualizarEstado.php?id=" + idCalle)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'OK') {
                        console.log("Válvula " + data.nombre + " - " + data.accion);
                        const btnId = idCalle === 1 ? 'btnBomba' : 'btnV' + (idCalle - 1);
                        const btn = document.getElementById(btnId);
                        if (btn) {
                            btn.textContent = data.accion === 'Abierto' ? 'Cerrar Válvula' : 'Abrir Válvula';
                        }
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        // Leer datos en tiempo real
        function leerDatos() {
            fetch('obtenerLecturas.php')
                .then(res => res.json())
                .then(datos => {
                    document.getElementById("flujo1").innerHTML = "Flujo: " + (datos.sf1 ? datos.sf1 + " L/min" : "0.0 L/min");
                    document.getElementById("flujo2").innerHTML = "Flujo: " + (datos.sf2 ? datos.sf2 + " L/min" : "0.0 L/min");
                    document.getElementById("flujo3").innerHTML = "Flujo: " + (datos.sf3 ? datos.sf3 + " L/min" : "0.0 L/min");
                    document.getElementById("nivelTanque").innerHTML = "Nivel: " + (datos.sp ? datos.sp + "%" : "0%");
                    document.getElementById("ultimoConsumo1").innerHTML = "Último consumo: " + (datos.ultimo1 ? datos.ultimo1 + " L" : "0 L");
                    document.getElementById("ultimoConsumo2").innerHTML = "Último consumo: " + (datos.ultimo2 ? datos.ultimo2 + " L" : "0 L");
                    document.getElementById("ultimoConsumo3").innerHTML = "Último consumo: " + (datos.ultimo3 ? datos.ultimo3 + " L" : "0 L");

                    const nivelTanque = parseFloat(datos.sp) || 0;
                    const umbralTanque = 20;
                    const alertText = nivelTanque > 0 && nivelTanque <= umbralTanque ?
                        'ALERTA: Nivel bajo del tanque' :
                        '';
                    document.getElementById("alertaTanque").innerHTML = alertText;

                    const estadoBomba = datos.estado1 == 1 ? "#2ecc71" : "#e74c3c";
                    const estadoV1 = datos.estado2 == 1 ? "#2ecc71" : "#e74c3c";
                    const estadoV2 = datos.estado3 == 1 ? "#2ecc71" : "#e74c3c";

                    document.getElementById("btnBomba").style.backgroundColor = estadoBomba;
                    document.getElementById("btnV1").style.backgroundColor = estadoV1;
                    document.getElementById("btnV2").style.backgroundColor = estadoV2;
                })
                .catch(err => console.error('Error al obtener datos:', err));
        }

        // Actualizar datos cada 2 segundos
        setInterval(leerDatos, 2000);

        // Cargar datos al iniciar
        leerDatos();
    </script>
</body>

</html>