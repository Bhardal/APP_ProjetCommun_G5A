<?php
// Connexion.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $pass  = $_POST['password']  ?? '';

    if (!$email) {
        $errors[] = "E-mail invalide.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($pass, $user['password'])) {
                $errors[] = "Identifiants incorrects.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                header("Location: accueil.php");
                exit;
            }
        } catch(PDOException $e){
            echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Georgia', serif;
        }
        body {
            background: url("Resto.png") no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
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
        .form-container {
            margin: auto;
            margin-top: 5%;
            margin-bottom: 5%;
            background-color: rgba(255,255,255,0.95);
            padding: 40px; border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            width: 100%; max-width: 400px;
        }
        .form-container h2 {
            text-align: center; color: #800000; margin-bottom: 30px;
        }
        .errors { list-style: none; margin-bottom: 20px; }
        .errors li { color: #a00; margin-bottom: 5px; }
        .form-container label { display: block; margin-top: 15px; color: #333; font-weight: bold; }
        .form-container input {
            width: 100%; padding: 10px; margin-top: 8px;
            border: 1px solid #ccc; border-radius: 8px; font-size: 16px;
        }
        .form-container button {
            background-color: #800000; color: #fff;
            padding: 12px; margin-top: 25px; width: 100%;
            border: none; border-radius: 25px; font-size: 16px;
            cursor: pointer; transition: background-color 0.3s ease;
        }
        .form-container button:hover { background-color: #a00d0d; }
        .forgot-link, .register-link, .back-link {
            display: block; text-align: center; margin-top: 15px;
            color: #800000; text-decoration: none; font-size: 15px;
        }
        .forgot-link:hover, .register-link:hover, .back-link:hover {
            text-decoration: underline;
        }
        footer {
            background-color:#2C3E50; color:#fff; padding:20px;
            text-align:center; font-size:14px;
        }
        @media (max-width:768px) {
            header { flex-direction:column; align-items:flex-start; }
            .buttons { margin-top:10px; flex-wrap:wrap; }
            .hero-content { padding:20px; }
            .commitment-cards { flex-direction:column; align-items:center; }
        }
    </style>
</head>

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

<body>
<div class="form-container">
    <h2>Connexion</h2>

    <?php if (isset($_GET['registered'])): ?>
        <p class="success">Inscription réussie ! Vous pouvez vous connecter.</p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <ul class="errors">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="post">
        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <a href="mot-de-passe-oublie.php" class="forgot-link">Mot de passe oublié ?</a>
    <a href="inscription.php" class="register-link">Pas encore de compte ? Créer un compte</a>
    <a href="accueil.php" class="back-link">← Retour à l'accueil</a>
</div>

</body>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const dropbtn = document.querySelector('.dropbtn');
        const menu    = document.querySelector('.dropdown-content');

        dropbtn.addEventListener('click', function(e){
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        document.addEventListener('click', function(){
            menu.classList.remove('show');
        });
    });
</script>
<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>


</html>
