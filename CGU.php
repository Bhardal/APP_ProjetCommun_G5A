<?php
// cgu.php
require 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CGU – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --main-color: #800000;
            --bg-gradient: linear-gradient(to bottom right, #fff8f2, #f9e9e0);
            --text-color: #4a2c2a;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family: Georgia, serif; }
        body {
            background: var(--bg-gradient);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: var(--text-color);
        }
        /* Header */
        header {
            background: rgba(255,255,255,0.9);
            padding: 20px 40px;
            display: flex; align-items: center;
            border-bottom: 1px solid #ccc;
            position: sticky; top:0; z-index:10;
        }
        .dropdown { position: relative; margin-right:20px; }
        .dropbtn {
            background: var(--main-color); color:#fff;
            padding:10px 18px; border:none; border-radius:4px;
            cursor:pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute; top:110%; left:0;
            background:#fff; min-width:180px;
            border:1px solid #ccc; box-shadow:0 4px 8px rgba(0,0,0,0.1);
            z-index:100;
        }
        .dropdown-content.show { display:block; }
        .dropdown-content a {
            display:block; padding:10px 15px;
            color: var(--main-color); text-decoration:none;
        }
        .dropdown-content a:hover { background:#faf4f1; }
        .logo-area {
            display:flex; align-items:center; text-decoration:none; color:inherit;
        }
        .logo-area img { width:50px; margin-right:15px; }
        .logo-text { font-size:24px; font-weight:bold; }
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
            width:40px;height:40px;border-radius:50%;
            margin-left:15px;object-fit:cover;
            border:2px solid var(--main-color);cursor:pointer;
        }

        /* Container CGU */
        .container {
            flex:1;
            max-width:900px;
            margin:40px auto;
            background: #ffffffee;
            padding:50px;
            border-radius:16px;
            box-shadow:0 8px 25px rgba(0,0,0,0.1);
        }
        h1 {
            text-align:center;
            color: var(--main-color);
            font-size:2.5rem;
            margin-bottom:40px;
        }
        h2 {
            font-size:1.5rem;
            color: var(--main-color);
            margin-top:30px;
            margin-bottom:15px;
            border-left:4px solid var(--main-color);
            padding-left:12px;
        }
        p {
            margin-bottom:20px;
            font-size:1.1rem;
            line-height:1.6;
        }
        a { color: var(--main-color); text-decoration:none; }
        a:hover { text-decoration:underline; }

        footer {
            text-align:center;
            padding:20px;
            font-size:0.9rem;
            background: #2C3E50;
            color: #fff;
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
    <h1>Conditions Générales d’Utilisation</h1>

    <section>
        <h2>1. Présentation du site</h2>
        <p><strong>Gusteau’s Restaurant</strong> est une plateforme de gestion de capteurs dédiée à l’amélioration de l’environnement d’un restaurant. Certaines fonctionnalités sont réservées aux membres connectés.</p>
    </section>

    <section>
        <h2>2. Acceptation des CGU</h2>
        <p>En accédant et utilisant ce site, vous acceptez pleinement et entièrement les présentes conditions d’utilisation. Si vous êtes en désaccord, veuillez cesser l’utilisation de nos services.</p>
    </section>

    <section>
        <h2>3. Accès et utilisation</h2>
        <p>L’accès est libre pour certaines pages, mais des fonctionnalités spécifiques requièrent une inscription. Toute tentative d’intrusion, d’accès non autorisé ou de contournement est strictement interdite.</p>
    </section>

    <section>
        <h2>4. Données personnelles</h2>
        <p>Vos données personnelles sont traitées de manière confidentielle. Elles ne seront jamais partagées sans votre consentement. Vous pouvez en demander la suppression en contactant l’administrateur.</p>
    </section>

    <section>
        <h2>5. Responsabilité</h2>
        <p>Nous faisons de notre mieux pour fournir un site fiable, mais nous ne garantissons pas l’absence d’erreurs ou d’interruptions. L’utilisateur accepte d’utiliser le site à ses propres risques.</p>
    </section>

    <section>
        <h2>6. Propriété intellectuelle</h2>
        <p>Tout le contenu du site, y compris textes, images, logos et code, appartient à Gusteau’s Restaurant. Toute reproduction sans autorisation est interdite.</p>
    </section>

    <section>
        <h2>7. Modifications</h2>
        <p>Nous nous réservons le droit de modifier les CGU à tout moment. Les utilisateurs sont invités à les consulter régulièrement pour rester informés des changements.</p>
    </section>

    <section>
        <h2>8. Contact</h2>
        <p>Pour toute question ou demande relative à ces conditions, contactez-nous à : <a href="mailto:contact@gusteaus-restaurant.com">contact@gusteaus-restaurant.com</a></p>
    </section>
</div>

<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn  = document.querySelector('.dropbtn');
        const menu = document.querySelector('.dropdown-content');
        btn.addEventListener('click', e => {
            e.stopPropagation();
            menu.classList.toggle('show');
        });
        document.addEventListener('click', () => menu.classList.remove('show'));
    });
</script>

</body>
</html>
