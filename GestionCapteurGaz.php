<?php
// GestionCapteurGaz.php
require 'config.php';
require_once './dbConnexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// if (empty($_SESSION['user_id'])) {
//     header('Location: Connexion.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin – Capteur de gaz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Georgia',serif; }
        body {
            background: url("Resto.png") no-repeat center center fixed;
            background-size: cover;
            color: #800000;
        }
        header {
            background: rgba(255,255,255,0.9);
            padding: 20px 40px;
            display: flex; align-items: center;
            border-bottom:1px solid #ccc;
            position: sticky; top:0; z-index:10;
        }
        a.logo-area {
            display:flex; align-items:center;
            text-decoration:none; color:inherit; user-select:none;
        }
        a.logo-area img { width:50px; margin-right:15px; }
        .logo-text { font-size:24px; font-weight:bold; }
        .buttons { margin-left:auto; display:flex; align-items:center; }
        .btn {
            background:#800000; color:#fff; padding:10px 18px;
            border:none; border-radius:20px; margin-left:15px;
            text-decoration:none; transition:all .3s; animation:pulse 2.5s infinite;
            font-size:15px;
        }
        .btn:hover { background:#a00d0d; transform:scale(1.05); }
        .btn.secondary {
            background:#fff; color:#800000; border:2px solid #800000; animation:none;
        }
        .btn.secondary:hover { background:#f5f5f5; }
        @keyframes pulse {
            0%,100% { box-shadow:0 0 0 0 rgba(128,0,0,0.4); }
            50%     { box-shadow:0 0 0 10px rgba(128,0,0,0); }
        }
        .profile-icon {
            width:40px; height:40px; border-radius:50%;
            margin-left:15px; object-fit:cover;
            border:2px solid #800000; cursor:pointer;
        }
        .capteur-container {
            background: rgba(255,255,255,0.95);
            padding:40px; border-radius:15px;
            box-shadow:0 0 15px rgba(0,0,0,0.2);
            text-align:center; max-width:700px; width:95%;
            margin:40px auto;
        }
        .capteur-container h2 {
            color:#800000; margin-bottom:20px;
        }
        .valeur-gaz {
            font-size:24px; font-weight:bold;
            margin:15px 0; color:#2C3E50;
        }
        .etat {
            font-size:18px; margin:10px 0; color:#444;
        }
        .controls { margin-top:20px; }
        .controls .btn { margin:10px; }
        label { margin:0 10px; font-weight:bold; }
        input[type="number"] {
            width:80px; padding:5px; border:1px solid #ccc;
            border-radius:8px; text-align:center;
        }
        .checkbox {
            margin-top:15px; font-size:14px; color:#2C3E50;
        }
        canvas {
            margin-top:20px; background:#fff;
            border-radius:10px; box-shadow:0 0 5px rgba(0,0,0,0.1);
        }
        ul {
            list-style:none; padding:0; margin-top:10px;
            font-size:14px; color:#333;
        }
        a.back {
            display:block; margin-top:25px;
            color:#800000; text-decoration:none; font-size:15px;
        }
        a.back:hover { text-decoration:underline; }
    </style>
</head>
<body>

<header>
    <a href="Accueil.php" class="logo-area">
        <img src="GUSTEAU'S.jpg" alt="Logo Gusteau">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </a>
    <div class="buttons">
        <?php if(empty($_SESSION['user_id'])): ?>
            <a href="Inscription.php" class="btn">Inscription</a>
            <a href="Connexion.php" class="btn">Connexion</a>
        <?php else: ?>
            <a href="logout.php" class="btn secondary">Déconnexion</a>
        <?php endif; ?>
        <a href="Profil.php">
            <img src="Profile.avif" alt="Profil" class="profile-icon">
        </a>
    </div>
</header>

<div class="capteur-container">
    <h2>Gestion du capteur de gaz</h2>

    <div class="valeur-gaz">
        Concentration actuelle : <span id="valeur">---</span> ppm
    </div>

    <div class="etat" id="etat-action">🛢️ Action : —</div>

    <div class="controls">
        <button class="btn" onclick="simulerLectureGaz(false)">Lire le capteur</button>
        <button class="btn" onclick="toggleActionManuelle()">ON/OFF manuel</button>
        <label>Seuil : <input type="number" id="seuil" value="200"> ppm</label>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" id="auto" onchange="reactiverAutomatique()">
            Mode automatique (lecture toutes les 5 sec)
        </label>
    </div>

    <canvas id="graphiqueGaz" height="120"></canvas>

    <h3 style="margin-top:25px;">Historique des lectures</h3>
    <ul id="historique"></ul>

    <a href="GestionCapteurs.php" class="back">← Retour à la gestion des capteurs</a>
</div>

<script>
    let logs = [], actionManuel = false, chart;

    function fetchDataFromDatabase() {
        return fetch('./docs/getDataGaz.php')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                let last = data[data.length-1]
                let id = last.id;
                let value = last.intensite;
                var dateString = last.date,
                    dateTimeParts = dateString.split(" "),
                    dateParts = dateTimeParts[0].split("-"),
                    timeParts = dateTimeParts[1].split(":");
                let date = new Date(dateParts[0], parseInt(dateParts[1], 10) - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);

                const now = Math.floor(Date.now() / 1000);
                return {
                    ts: now,
                    data: value
                };
            })

    }

    function simulerLectureGaz(isManual) {
        // const val = Math.floor(Math.random()*500); // simulation ppm
        fetchDataFromDatabase()
            .then(datas => {
                const val = datas.data;
                document.getElementById('valeur').textContent = val + ' ppm';
                const seuil = parseInt(document.getElementById('seuil').value,10);
                if (!isManual && document.getElementById('auto').checked && !actionManuel) {
                    document.getElementById('etat-action').textContent =
                        '🛢️ Action : ' + (val < seuil ? 'ON (auto)' : 'OFF (auto)');
                }
                const now = new Date().toLocaleTimeString();
                logs.unshift(`${now} – ${val} ppm`);
                logs = logs.slice(0,5);
                document.getElementById('historique').innerHTML =
                    logs.map(e=>`<li>${e}</li>`).join('');
                updateGraph(now,val);
            });
    }

    function toggleActionManuelle() {
        actionManuel = !actionManuel;
        document.getElementById('etat-action').textContent =
            '🛢️ Action : ' + (actionManuel?'ON (manuel)':'OFF (manuel)');
    }

    function reactiverAutomatique() {
        if (document.getElementById('auto').checked) actionManuel=false;
    }

    function boucleAuto() {
        if (document.getElementById('auto').checked && !actionManuel)
            simulerLectureGaz(false);
    }

    function initGraph() {
        const ctx = document.getElementById('graphiqueGaz').getContext('2d');
        chart = new Chart(ctx,{
            type:'line',
            data:{labels:[],datasets:[{
                    label:'Concentration de gaz (ppm)',
                    data:[],
                    borderColor:'#800000',
                    backgroundColor:'rgba(128,0,0,0.1)',
                    tension:0.2
                }]},
            options:{responsive:true, scales:{y:{beginAtZero:true}}}
        });
    }

    function updateGraph(label,val) {
        chart.data.labels.push(label);
        chart.data.datasets[0].data.push(val);
        if (chart.data.labels.length>10) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }
        chart.update();
    }

    window.onload = ()=>{
        initGraph();
        setInterval(boucleAuto,5000);
    };
</script>

</body>
</html>
