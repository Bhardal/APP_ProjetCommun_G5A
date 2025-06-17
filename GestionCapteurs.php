<?php
// GestionCapteurs.php
require 'config.php';
require_once './docs/dbConnexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des capteurs – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Georgia',serif; }
        html, body { height: 100%; }
        body {
            display: flex;
            flex-direction: column;
            background: url("Resto.png") center/cover no-repeat;
            color: #800000;
        }
        header {
            background-color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ccc;
            position: relative;
        }
        /* Dropdown */
        .dropdown {
            position: relative;
            margin-right: 20px;
        }
        .dropbtn {
            background-color: #800000;
            color: #fff;
            padding: 10px 18px;
            font-size: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dropbtn:hover {
            background-color: #a00d0d;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            top: 110%;
            left: 0;
            background-color: #fff;
            min-width: 180px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .dropdown-content.show {
            display: block;
        }
        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: #800000;
            text-decoration: none;
            font-size: 14px;
        }
        .dropdown-content a:hover {
            background-color: #f5f5f5;
        }
        /* Logo */
        .logo-area {
            display: flex;
            align-items: center;
        }
        .logo-area img {
            width: 50px;
            height: auto;
            margin-right: 15px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #800000;
        }
        a.logo-area {
            text-decoration: none;
            color: inherit;
            user-select: none;
        }
        a.logo-area:focus {
            outline: none;
        }
        /* Boutons de droite */
        .buttons {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        .btn {
            background-color: #800000;
            color: #fff;
            padding: 10px 18px;
            border-radius: 20px;
            margin-left: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: pulse 2.5s infinite;
            font-size: 15px;
        }
        .btn:hover {
            background-color: #a00d0d;
            transform: scale(1.05);
        }
        .btn.secondary {
            background-color: #fff;
            color: #800000;
            border: 2px solid #800000;
            animation: none;
        }
        .btn.secondary:hover {
            background-color: #f5f5f5;
        }
        @keyframes pulse {
            0%,100% { box-shadow:0 0 0 0 rgba(128,0,0,0.4); }
            50%     { box-shadow:0 0 0 10px rgba(128,0,0,0); }
        }
        .profile-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            margin-left: 15px;
            object-fit: cover;
            border: 2px solid #800000;
            cursor: pointer;
        }
        main {
            flex: 1;
            padding:60px 20px;
            min-height:calc(100vh - 120px);
        }
        .sensor-management {
            max-width:1000px; margin:auto;
            background:rgba(255,255,255,0.9);
            padding:40px; border-radius:15px;
            box-shadow:0 0 15px rgba(0,0,0,0.1);
        }
        .sensor-management h1 {
            text-align:center; font-size:28px; margin-bottom:30px; color:#2C3E50;
        }
        .sensor-cards {
            display:flex; flex-wrap:wrap; justify-content:center; gap:30px;
        }
        .sensor-card {
            background:#fff; border:1px solid #ddd;
            border-radius:12px; padding:20px; width:220px; text-align:center;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
        }
        .sensor-card h3 { font-size:18px; margin-bottom:15px; color:#800000; }
        .sensor-link { text-decoration:none; color:inherit; }
        .sensor-link:hover { text-decoration:underline; }
        .sensor-card p {
            font-size:15px; color:#555; margin-bottom:20px;
        }
        footer {
            background-color:#2C3E50; color:#fff;
            padding:20px; text-align:center; font-size:14px;
        }
        @media (max-width:768px) {
            header { flex-wrap:wrap; }
            .sensor-cards { flex-direction:column; align-items:center; }
        }
    </style>
</head>
<body>

<header>
    <?php if (!empty($_SESSION['user_id'])): ?>
        <!-- Menu déroulant (visible uniquement quand connecté) -->
        <div class="dropdown">
            <button class="dropbtn">Menu</button>
            <div class="dropdown-content">
                <a href="accueil.php">Accueil</a>
                <a href="gestionCapteurs.php">Gestion de capteurs</a>
                <a href="faq.php">FAQ</a>
                <a href="cgu.php">CGU</a>
            </div>
        </div>
    <?php endif; ?>


    <!-- Logo central -->
    <a href="accueil.php" class="logo-area">
        <img src="GUSTEAU'S.jpg" alt="Logo Gusteau">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </a>


    <div class="buttons">
        <?php if (empty($_SESSION['user_id'])): ?>
            <a href="inscription.php" class="btn">Inscription</a>
            <a href="connexion.php"   class="btn">Connexion</a>
        <?php else: ?>
            <a href="logout.php"      class="btn secondary">Déconnexion</a>
        <?php endif; ?>

        <a href="profil.php">
            <img src="Profile.avif" alt="Profil" class="profile-icon">
        </a>
    </div>

</header>

<main>
    <div class="sensor-management">
        <h1>Gestion des capteurs</h1>
        <div class="sensor-cards">
            <!-- Capteurs… -->
            <div class="sensor-card">
                <h3><a href="gestionCapteurLumiere.php" class="sensor-link">💡 Capteur de lumière</a></h3>
                <p>Valeur actuelle : -- lux</p>
                <a href="gestionCapteurLumiere.php" class="btn secondary">Gérer ce capteur</a>
            </div>
            <div class="sensor-card">
                <h3><a href="gestionCapteurDistance.php" class="sensor-link">📏 Capteur de distance</a></h3>
                <p>Valeur actuelle : -- cm</p>
                <a href="gestionCapteurDistance.php" class="btn secondary">Gérer ce capteur</a>
            </div>
            <div class="sensor-card">
                <h3><a href="gestionCapteurSon.php" class="sensor-link">🔊 Capteur de son</a></h3>
                <p>Valeur actuelle : -- dB</p>
                <a href="gestionCapteurSon.php" class="btn secondary">Gérer ce capteur</a>
            </div>
            <div class="sensor-card">
                <h3><a href="gestionCapteurGaz.php" class="sensor-link">🛢️ Capteur de gaz</a></h3>
                <p>Valeur actuelle : -- ppm</p>
                <a href="gestionCapteurGaz.php" class="btn secondary">Gérer ce capteur</a>
            </div>
            <div class="sensor-card">
                <h3><a href="gestionCapteurTempHum.php" class="sensor-link">🌡️ Capteur Température & Humidité</a></h3>
                <p>Temp : -- °C<br>Humidité : -- %</p>
                <a href="gestionCapteurTempHum.php" class="btn secondary">Gérer ce capteur</a>
            </div>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>

<!-- Script d'ouverture/fermeture du dropdown -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn  = document.querySelector('.dropbtn');
        const menu = document.querySelector('.dropdown-content');

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            menu.classList.remove('show');
        });
    });
</script>

</body>
</html>
