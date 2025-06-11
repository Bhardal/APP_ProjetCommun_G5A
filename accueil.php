<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gusteau's Restaurant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* … (votre CSS inchangé) … */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Georgia', serif;
        }
        body {
            background-color: white;
            color: #800000;
        }
        header {
            background-color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
        }
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
        .buttons {
            display: flex;
            align-items: center;
        }
        .btn {
            background-color: #800000;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            margin-left: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            animation: pulse 2.5s infinite;
        }
        .btn:hover {
            background-color: #a00d0d;
            transform: scale(1.05);
        }
        .btn.secondary {
            background-color: white;
            color: #800000;
            border: 2px solid #800000;
            animation: none;
        }
        .btn.secondary:hover {
            background-color: #f5f5f5;
        }
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(128, 0, 0, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(128, 0, 0, 0);
            }
        }
        .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 15px;
            object-fit: cover;
            border: 2px solid #800000;
            cursor: pointer;
        }
        main {
            width: 100%;
            height: 100vh;
            background-image: url("Resto.png");
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .overlay {
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #2C3E50;
            background-color: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .hero-content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #2C3E50;
        }
        .hero-content p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #444;
        }
        .cta-buttons .btn {
            margin: 0 10px;
        }
        .commitments, .about, blockquote {
            padding: 60px 20px;
            text-align: center;
        }
        .commitments {
            background-color: #f9f9f9;
        }
        .commitments h2 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #2C3E50;
        }
        .commitment-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }
        .card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            max-width: 250px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #800000;
        }
        .card p {
            font-size: 15px;
            color: #555;
        }
        .about p {
            max-width: 700px;
            margin: auto;
            font-size: 17px;
            color: #555;
        }
        blockquote {
            font-style: italic;
            font-size: 18px;
            color: #444;
        }
        footer {
            background-color: #2C3E50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }
            .buttons {
                margin-top: 10px;
                flex-wrap: wrap;
            }
            .hero-content {
                padding: 20px;
            }
            .commitment-cards {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo-area">
        <img src="GUSTEAU'S.jpg" alt="Logo Gusteau">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </div>
    <div class="buttons">
        <!-- Liens mis à jour vers les fichiers PHP -->
        <a href="Inscription.php" class="btn">Inscription</a>
        <a href="Connexion.php"   class="btn">Connexion</a>
        <img src="Profile.avif" alt="Profil" class="profile-icon">
    </div>
</header>

<main>
    <div class="overlay">
        <div class="hero-content">
            <h1>Une nouvelle expérience vous attend chez Gusteau’s</h1>
            <p>Notre restaurant s’équipe de technologies intelligentes pour améliorer le confort et le service à chaque instant.</p>
            <div class="cta-buttons">
                <!-- Et ici aussi -->
                <a href="Connexion.php" class="btn">Se connecter</a>
                <a href="Inscription.php" class="btn secondary">Créer un compte</a>
            </div>
        </div>
    </div>
</main>

<section class="commitments">
    <h2>Nos engagements</h2>
    <div class="commitment-cards">
        <div class="card">
            <h3>🍽️ Confort optimal</h3>
            <p>Chaque détail est pensé pour offrir une atmosphère agréable à tout moment de la journée.</p>
        </div>
        <div class="card">
            <h3>🔒 Technologie discrète</h3>
            <p>Nos systèmes sont invisibles, mais améliorent constamment votre expérience sans intrusion.</p>
        </div>
        <div class="card">
            <h3>🌱 Éco-responsabilité</h3>
            <p>Nous utilisons des solutions intelligentes pour réduire la consommation énergétique de notre restaurant.</p>
        </div>
    </div>
</section>

<section class="about">
    <h2>À propos de nous</h2><br>
    <p>Chez Gusteau’s, tradition et innovation cohabitent. Nos équipes allient savoir-faire culinaire et technologies connectées pour offrir un service toujours plus fluide et personnalisé, sans jamais sacrifier l’élégance.</p>
</section>

<blockquote>
    <h2>Nos clients</h2><br>
    “Je n’avais jamais remarqué, mais tout était parfaitement éclairé, c’était magique !”<br>— Sophie, cliente régulière
</blockquote>

<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>

</body>
</html>
