<?php
// GestionCapteurTempHum.php
require 'config.php';
require_once './docs/dbConnexion.php';
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
    <title>Admin – Capteur Température & Humidité</title>
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
            border:none;border-radius:20px; margin-left:15px;
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
        .valeurs {
            font-size:20px; margin:10px 0; color:#2C3E50;
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
        <img src="GUSTEAU'S.jpg" alt="Logo">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </a>
    <div class="buttons">
        <?php if(empty($_SESSION['user_id'])): ?>
            <a href="Inscription.php" class="btn">Inscription</a>
            <a href="Connexion.php" class="btn">Connexion</a>
        <?php else: ?>
            <a href="logout.php" class="btn secondary">Déconnexion</a>
        <?php endif; ?>
        <a href="Profil.php"><img src="Profile.avif" alt="Profil" class="profile-icon"></a>
    </div>
</header>

<div class="capteur-container">
    <h2>Gestion Température &amp; Humidité – Admin</h2>

    <div class="valeurs">
        Température actuelle : <span id="valeurT">-- °C</span><br>
        Humidité actuelle : <span id="valeurH">-- %</span>
    </div>

    <div class="controls">
        <button class="btn" onclick="simulerLecture(false)">Lire capteurs</button>
        <button class="btn" onclick="toggleManuel()">ON/OFF manuel</button>
        <label>Seuil T : <input type="number" id="seuilT" value="25"> °C</label>
        <label>Seuil H : <input type="number" id="seuilH" value="50"> %</label>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" id="auto" onchange="reactiverAuto()">
            Mode auto (toutes les 5 sec)
        </label>
    </div>

    <canvas id="graphiqueTH" height="140"></canvas>

    <h3 style="margin-top:25px;">Historique</h3>
    <ul id="historique"></ul>

    <a href="GestionCapteurs.php" class="back">← Retour à la gestion des capteurs</a>
</div>

<script>
    let logs = [], manuel = false, chart;

    function simulerLecture(isManual) {
        const t = (15 + Math.random()*20).toFixed(1);
        const h = (30 + Math.random()*60).toFixed(0);
        document.getElementById('valeurT').textContent = t + ' °C';
        document.getElementById('valeurH').textContent = h + ' %';
        const seuilT = parseFloat(document.getElementById('seuilT').value);
        const seuilH = parseFloat(document.getElementById('seuilH').value);
        if (!isManual && document.getElementById('auto').checked && !manuel) {
            document.querySelector('#etatT')?.remove();
            document.querySelector('#etatH')?.remove();
        }
        const now = new Date().toLocaleTimeString();
        logs.unshift(`${now} – T:${t}°C, H:${h}%`);
        logs = logs.slice(0,5);
        document.getElementById('historique').innerHTML = logs.map(e=>`<li>${e}</li>`).join('');
        majGraph(now, t, h);
    }

    function toggleManuel() {
        manuel = !manuel;
    }

    function reactiverAuto() {
        if (document.getElementById('auto').checked) manuel = false;
    }

    function initGraph() {
        const ctx = document.getElementById('graphiqueTH').getContext('2d');
        chart = new Chart(ctx, {
            type:'line',
            data: {
                labels: [],
                datasets: [
                    { label:'Temp (°C)', data:[], borderColor:'#FF5733', tension:0.2 },
                    { label:'Humidité (%)', data:[], borderColor:'#337AFF', tension:0.2 }
                ]
            },
            options: { responsive:true, scales:{ y:{ beginAtZero:false } } }
        });
    }

    function majGraph(label, t, h) {
        chart.data.labels.push(label);
        chart.data.datasets[0].data.push(t);
        chart.data.datasets[1].data.push(h);
        if (chart.data.labels.length>10) {
            chart.data.labels.shift();
            chart.data.datasets.forEach(ds=>ds.data.shift());
        }
        chart.update();
    }

    window.onload = ()=>{
        initGraph();
        setInterval(boucle,5000);
    };

    function boucle() {
        if (document.getElementById('auto').checked && !manuel) {
            simulerLecture(false);
        }
    }
</script>

</body>
</html>
