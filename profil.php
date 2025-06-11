<?php
// Profil.php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require 'config.php';
if (empty($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}

// Récupère les infos de l’utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil – Gusteau’s Restaurant</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Georgia',serif;}
        body{background-color:white;color:#800000;}
        header{
            background:white;
            padding:20px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:1px solid #ccc;
        }
        /* Logo cliquable */
        a.logo-area{
            display:flex;
            align-items:center;
            text-decoration:none;
            color:inherit;
            user-select:none;
        }
        a.logo-area:focus{outline:none;}
        .logo-area img{width:50px;height:auto;margin-right:15px;}
        .logo-text{font-size:24px;font-weight:bold;color:#800000;}
        /* Boutons */
        .buttons{display:flex;align-items:center;}
        .btn{
            background:#800000;color:#fff;
            padding:10px 18px;border-radius:20px;
            margin-left:15px;text-decoration:none;
            transition:all .3s;animation:pulse 2.5s infinite;
            font-size:15px;
        }
        .btn:hover{background:#a00d0d;transform:scale(1.05);}
        .btn.secondary{background:#fff;color:#800000;border:2px solid #800000;animation:none;}
        .btn.secondary:hover{background:#f5f5f5;}
        @keyframes pulse{
            0%,100%{box-shadow:0 0 0 0 rgba(128,0,0,0.4);}
            50%{box-shadow:0 0 0 10px rgba(128,0,0,0);}
        }
        .profile-icon{
            width:40px;height:40px;border-radius:50%;
            margin-left:15px;object-fit:cover;
            border:2px solid #800000;cursor:pointer;
        }
        /* Contenu principal */
        main{
            width:100%;min-height:100vh;
            background:url("Resto.png") center/cover no-repeat;
            display:flex;align-items:center;justify-content:center;
            position:relative;
        }
        .overlay{
            width:100%;height:100%;
            background:rgba(255,255,255,0.05);
            display:flex;align-items:center;justify-content:center;
        }
        .content{
            position:absolute;top:50%;left:50%;
            transform:translate(-50%,-50%);
            background:rgba(255,255,255,0.9);
            padding:40px;border-radius:15px;
            max-width:500px;width:90%;
            box-shadow:0 0 15px rgba(0,0,0,0.1);
            text-align:center;
        }
        .content h1{font-size:28px;color:#2C3E50;margin-bottom:20px;}
        .content p{font-size:18px;color:#444;margin:10px 0;}
        footer{background:#2C3E50;color:#fff;padding:20px;text-align:center;font-size:14px;}
    </style>
</head>
<body>

<header>
    <!-- Logo cliquable vers l'accueil -->
    <a href="Accueil.php" class="logo-area">
        <img src="GUSTEAU'S.jpg" alt="Logo Gusteau">
        <div class="logo-text">GUSTEAU'S RESTAURANT</div>
    </a>

    <div class="buttons">
        <!-- Bouton Déconnexion -->
        <a href="logout.php" class="btn secondary">Se déconnecter</a>
        <!-- Icône Profil (reste cliquable sur profil) -->
        <a href="Profil.php">
            <img src="Profile.avif" alt="Profil" class="profile-icon">
        </a>
    </div>
</header>

<main>
    <div class="overlay">
        <div class="content">
            <h1>Mon Profil</h1>
            <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
            <p><strong>E-mail :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Inscrit le :</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés
</footer>

</body>
</html>
