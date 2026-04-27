<?php
/*session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /AuraTerra/public/login');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AuraTerra Clima</title>
</head>
<body>

<h2>Consultar clima</h2>

<input type="text" id="ciudad" placeholder="Ej: Resistencia">
<button onclick="consultarActual()">Clima actual</button>
<button onclick="consultarPronostico()">Pronóstico 5 días</button>

<pre id="resultado"></pre>

<script>
function consultarActual() {
    const ciudad = document.getElementById("ciudad").value;

    fetch(`/AuraTerra/public/clima/actual?ciudad=${ciudad}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("resultado").textContent =
                JSON.stringify(data, null, 2);
        });
}

function consultarPronostico() {
    const ciudad = document.getElementById("ciudad").value;

    fetch(`/AuraTerra/public/clima/pronostico?ciudad=${ciudad}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("resultado").textContent =
                JSON.stringify(data, null, 2);
        });
}
</script>

</body>
</html>*/


session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /AuraTerra/public/login');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AuraTerra</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        input {
            padding: 10px;
            border-radius: 8px;
            border: none;
            width: 200px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            margin: 5px;
            cursor: pointer;
            background: #ffffff;
            color: #333;
            font-weight: bold;
        }

        .card {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 200px;
        }

        .main-card {
            width: 260px;
            font-size: 18px;
        }
    </style>
</head>

<body>

<h1>🌤 AuraTerra</h1>

<input type="text" id="ciudad" placeholder="Ej: Resistencia">
<br><br>

<button onclick="consultarActual()">Clima actual</button>
<button onclick="consultarPronostico()">Pronóstico</button>

<div id="actual"></div>
<div id="pronostico"></div>

<script>

function icono(desc) {
    if (desc.includes("lluvia")) return "🌧";
    if (desc.includes("nube")) return "☁";
    if (desc.includes("claro")) return "☀";
    return "🌡";
}

// 🔵 CLIMA ACTUAL
function consultarActual() {
    const ciudad = document.getElementById("ciudad").value;

    fetch("http://localhost/AuraTerra/public/clima/actual?ciudad=" + ciudad, {
        credentials: 'include'
    })
    .then(r => r.json())
    .then(data => {

        const c = data.data;

        document.getElementById("actual").innerHTML = `
            <div class="card main-card">
                <h2>📍 ${c.ubicacion}</h2>
                <h1>${icono(c.descripcion)} ${c.temperatura}°C</h1>
                <p>${c.descripcion}</p>
                <p>💧 ${c.humedad}%</p>
                <p>💨 ${c.viento} m/s</p>
            </div>
        `;
    });
}

// 🟣 PRONÓSTICO
function consultarPronostico() {
    const ciudad = document.getElementById("ciudad").value;

    fetch("http://localhost/AuraTerra/public/clima/pronostico?ciudad=" + ciudad, {
        credentials: 'include'
    })
    .then(r => r.json())
    .then(data => {

        let html = "";

        data.data.forEach(item => {

            if (item.dt_txt.includes("12:00:00")) {

                const fecha = item.dt_txt.split(" ")[0];
                const temp = item.main.temp;
                const desc = item.weather[0].description;

                html += `
                    <div class="card">
                        <h3>${fecha}</h3>
                        <h2>${icono(desc)} ${temp}°C</h2>
                        <p>${desc}</p>
                    </div>
                `;
            }
        });

        document.getElementById("pronostico").innerHTML = html;
    });
}

</script>

</body>
</html>