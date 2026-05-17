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
                    <button class="btn" onclick="abrirValvula(1)" id="btnBomba">Abrir Bomba</button>
                </article>
            </section>
            <section class="contenedor">
                <article class="card">
                    <span class="calle">Calle 1</span>
                    <span class="textInf" id="flujo2"> Flujo: 0.0 L/min</span>
                    <div class="numFam">
                        <label for="numFam">Ingrese la cantidad de familias:</label>
                        <input type="number" placeholder="número de familias">
                        <button class="btnF">Enviar</button>
                    </div>
                    <button class="btn" onclick="abrirValvula(2)" id="btnV1">Abrir Válvula</button>
                    <div class="numFam">
                        <label for="numFam">Tiempo estimado de encendido: 00:00 min </label>
                    </div>
                </article>
                <article class="card">
                    <span class="calle">Calle 2</span>
                    <span class="textInf" id="flujo3"> Flujo: 0.0 L/min</span>
                    <div class="numFam">
                        <label for="numFam">Ingrese la cantidad de familias:</label>
                        <input type="number" placeholder="número de familias">
                        <button class="btnF">Enviar</button>
                    </div>
                    <button class="btn" onclick="abrirValvula(3)" id="btnV2">Abrir Válvula</button>
                    <div class="numFam">
                        <label for="numFam">Tiempo estimado de encendido: 00:00 min </label>
                    </div>
                </article>
            </section>
        </div>
        <div class="informacion">
            <h2 id="info">Información</h2>
            <!-- capacidad del tanque-->
            <article class="card" id="infoNvl">
                <span class="calle">Capacidad del tanque</span>
                <span class="textInf" id="nivelTanque"> Nivel: 0.0 L/min</span>
            </article>
            <!-- fugas -->
            <article class="card" id="infoFlj">
                <span class="calle">Estado del flujo</span>
                <span class="textInf" id="estadoFlujo"> Flujo: normal</span>
            </article>
        </div>
    </div>

    <script>
        //Cambiar el estado de las válvulas en la BD
        function abrirValvula(idCalle) {
            fetch("actualizarEstado.php?id=" + idCalle)
                .then(res => res.text())
                .then(data => {
                    console.log("Estado cambiado en la Base de Datos");
                })
        }

        //leer datos
        function leerDatos() {
            fetch('obtenerLecturas.php')
                .then(res => res.json())
                .then(datos => {
                    document.getElementById("flujo1").innerHTML = "Flujo: " + datos.sf1;
                    document.getElementById("flujo2").innerHTML = "Flujo: " + datos.sf2;
                    document.getElementById("flujo3").innerHTML = "Flujo: " + datos.sf3;
                    document.getElementById("nivelTanque").innerHTML = "Nivel: " + datos.sp;
                })

            const ibtnBomba = document.getElementById("btnBomba");
            ibtnBomba.style.backgroundColor = (datos.estadoB == "1") ? "#2ecc71" : "#e74c3c";

            const ibtnV1 = document.getElementById("btnV1");
            ibtnV1.style.backgroundColor = (datos.estadoB == "1") ? "#2ecc71" : "#e74c3c";

            const ibtnV2 = document.getElementById("btnV2");
            ibtnV2.style.backgroundColor = (datos.estadoB == "1") ? "#2ecc71" : "#e74c3c";

            function MostrarHistorial() {
                fetch('mostrarHistorial.php')
                    .then(res => res.json())
                    .then(eventos => {
                        const tabla = document.getElementById
                    })

            }

        }

        setInterval(leerDatos, 2000);
    </script>
</body>

</html>