<?php
// Profil.php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = htmlspecialchars(trim($_POST['nom']    ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email  = htmlspecialchars(filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $notifications = isset($_POST['notifications']) ? 1 : 0;


    if ($nom === '' || $prenom === '') {
        $errors[] = 'Nom et prénom obligatoires.';
    }
    if (!$email) {
        $errors[] = 'E-mail invalide.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
    UPDATE users
    SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone, notifications_active = :notifications
    WHERE id = :id
");
            $stmt->execute([
                ':nom'       => $nom,
                ':prenom'    => $prenom,
                ':email'     => $email,
                ':telephone' => $telephone,
                ':notifications' => $notifications,
                ':id'        => $userId,
            ]);

            $success = true;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Rechargement des infos utilisateur
try {
    $stmt = $pdo->prepare("SELECT nom, prenom, email, telephone, created_at, notifications_active FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

} catch(PDOException $e){
    echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style>
        * {
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Georgia',serif;
        }
        body{
            background-color: white;
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
        main{
            width:100%;min-height:100vh;
            background:url("Resto.png") center/cover no-repeat;
            position:relative;
        }
        .overlay{
            width:100%;height:100%;
            background:rgba(255,255,255,0.05);
            padding-top:5%
        }
        .content{
            margin: auto;
            padding:40px;
            background:rgba(255,255,255,0.9);
            border-radius:15px;
            max-width:500px;width:90%;
            box-shadow:0 0 15px rgba(0,0,0,0.1);
            text-align:center;
        }
        .content h1{font-size:28px;color:#2C3E50;margin-bottom:20px;}
        .content form { text-align: left; }
        .content label { display: block; margin: 12px 0 6px; font-weight: bold; }
        .content input {
            width:100%; padding:8px; border:1px solid #ccc; border-radius:8px;
            font-size: 15px;
        }
        .content button {
            background:#800000; color:#fff; padding:10px 18px;
            border:none; border-radius:20px; cursor:pointer;
            margin-top:20px; width:100%;
            transition:all .3s;
        }
        .content button:hover { background: #a00d0d; }
        .errors { text-align:left; color:#a00; margin-bottom:20px; }
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
    <div class="overlay">
        <div class="content">
            <h1>Mon Profil</h1>

            <?php if ($success): ?>
                <p style="color:green;">Profil mis à jour avec succès !</p>
            <?php endif; ?>

            <?php if ($errors): ?>
                <ul class="errors">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="post">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom"
                       value="<?= htmlspecialchars($user['nom']) ?>" required>

                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom"
                       value="<?= htmlspecialchars($user['prenom']) ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($user['email']) ?>" required>

                <label for="telephone">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" required
                       value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">

                <label>Inscrit le</label>
                <input type="text" value="<?= htmlspecialchars($user['created_at']) ?>" disabled>

                <!-- Case à cocher : notifications -->
                <div style="display: flex; align-items: center; margin-top: 15px;">
                    <input type="checkbox" id="notifications" name="notifications" value="1" <?= $user['notifications_active'] ? 'checked' : '' ?> style="margin: 0 10px 0 0; width: 16px; height: 16px;">
                    <label for="notifications" style="margin: 0; font-size: 14px;">Je souhaite recevoir les notifications par mail</label>
                </div>

                <button type="submit">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>
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

</body>
</html>
