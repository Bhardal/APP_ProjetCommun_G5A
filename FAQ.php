<?php
// FAQ.php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FAQ – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --main-color: #800000;
            --hover-color: #a00000;
            --bg-color: #fff8f2;
            --text-color: #4a2c2a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Georgia, serif;
        }

        body {
            background: linear-gradient(to bottom right, var(--bg-color), #fbe4da);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--text-color);
        }

        /* Header */
        header {
            background: rgba(255,255,255,0.9);
            padding: 20px 40px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ccc;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        /* Dropdown */
        .dropdown {
            position: relative;
            margin-right: 20px;
        }
        .dropbtn {
            background-color: var(--main-color);
            color: #fff;
            padding: 10px 18px;
            font-size: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            top: 110%;
            left: 0;
            background: #fff;
            min-width: 180px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .dropdown-content.show {
            display: block;
        }
        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: var(--main-color);
            text-decoration: none;
            font-size: 14px;
        }
        .dropdown-content a:hover {
            background: #faf4f1;
        }
        /* Logo */
        .logo-area {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: inherit;
            user-select: none;
        }
        .logo-area img {
            width: 50px;
            height: auto;
            margin-right: 15px;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
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
        .profile-icon {
            width: 40px; height: 40px;
            border-radius: 50%;
            margin-left: 15px;
            object-fit: cover;
            border: 2px solid var(--main-color);
            cursor: pointer;
        }

        .container {
            flex: 1;
            max-width: 1000px;
            margin: 40px auto;
            background: #ffffffee;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        h1 {
            text-align: center;
            color: var(--main-color);
            margin-bottom: 50px;
            font-size: 2.8rem;
        }

        .faq-item {
            border-bottom: 1px solid #ddd;
            padding: 25px 0;
            transition: background 0.3s ease;
        }
        .faq-item:hover {
            background-color: #faf4f1;
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--main-color);
        }
        .faq-icon {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-number {
            color: var(--main-color);
            font-weight: bold;
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .faq-answer {
            display: none;
            font-size: 1.05rem;
            padding: 15px 0 0 30px;
            color: #444;
            animation: fadeIn 0.4s ease-in-out;
        }
        .faq-item.active .faq-answer {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            color: #666;
            background: #2C3E50;
            color: #fff;
        }

        @media screen and (max-width: 600px) {
            .container { padding: 30px 20px; }
            h1 { font-size: 2rem; }
            .faq-question { font-size: 1.1rem; }
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
    <div class="dropdown">
        <button class="dropbtn">Menu</button>
        <div class="dropdown-content">
            <a href="Accueil.php">Accueil</a>
            <a href="GestionCapteurs.php">Gestion de capteurs</a>
            <a href="faq.php">FAQ</a>
            <a href="cgu.php">CGU</a>
        </div>
    </div>

    <a href="Accueil.php" class="logo-area">
        <img src="GUSTEAU'S.jpg" alt="Logo Gusteau">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </a>

    <div class="buttons">
        <?php if (empty($_SESSION['user_id'])): ?>
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

<div class="container">
    <h1>Foire Aux Questions</h1>

    <!-- Tes 20 questions ici, inchangées -->
    <div class="faq-item">
        <div class="faq-question">
            <span>Comment créer un compte ?</span>
            <span class="faq-icon">▼</span>
        </div>
        <div class="faq-answer">Cliquez sur "Inscription" et remplissez les informations demandées.</div>
    </div>
    <!-- … répéter pour les 20 items … -->
    <div class="faq-item">
        <div class="faq-question">
            <span>Comment signaler un bug ?</span>
            <span class="faq-icon">▼</span>
        </div>
        <div class="faq-answer">Envoyez un e-mail à l’équipe technique via contact@gusteaus-restaurant.com</div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>Comment signaler un bug ?</span>
            <span class="faq-icon">▼</span>
        </div>
        <div class="faq-answer">Envoyez un e-mail à l’équipe technique via contact@gusteaus-restaurant.com</div>
    </div>

    <div class="faq-item">
        <div class="faq-question">
            <span>L'utilisation de ce site a-t-elle un impact écologique négatif ?</span>
            <span class="faq-icon">▼</span>
        </div>
        <div class="faq-answer">
            Oui, car chaque action entraîne des requêtes vers la base de données, ce qui consomme de l’énergie. Même minime, cet impact existe, surtout si les utilisateurs ou capteurs effectuent de nombreuses requêtes en continu.
        </div>
    </div>


</div>
<script>
    // Dropdown
    document.addEventListener('DOMContentLoaded', () => {
        const btn  = document.querySelector('.dropbtn');
        const menu = document.querySelector('.dropdown-content');
        btn.addEventListener('click', e => {
            e.stopPropagation();
            menu.classList.toggle('show');
        });
        document.addEventListener('click', () => menu.classList.remove('show'));

        // FAQ toggles
        document.querySelectorAll('.faq-item').forEach((item, i) => {
            const q = item.querySelector('.faq-question');
            const num = document.createElement('span');
            num.className = 'faq-number';
            num.textContent = (i+1)+'. ';
            q.prepend(num);
            q.addEventListener('click', () => item.classList.toggle('active'));
        });
    });
</script>
<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>

</body>
</html>
