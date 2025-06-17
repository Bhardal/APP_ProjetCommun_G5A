<?php
// Inscription.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

$errors = [];
// Valeurs par défaut pour pré-remplir en cas d'erreur
$nom    = '';
$prenom = '';
$email  = '';
$telephone = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupère et valide
    $nom    = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
    $email  = htmlspecialchars(filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL));
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $p1     = $_POST['password'] ?? '';
    $p2     = $_POST['confirm-password'] ?? '';

    if ($nom === '' || $prenom === '') {
        $errors[] = "Nom et prénom obligatoires.";
    }
    if (!$email) {
        $errors[] = "E-mail invalide.";
    }
    if (strlen($p1) < 6) {
        $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
    }
    if ($p1 !== $p2) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // 2. Vérifie si l’e-mail existe déjà
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Cette adresse e-mail est déjà prise.";
            }
        } catch(PDOException $e){
            echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
        }
    }

    // 3. Insère en BDD
    if (empty($errors)) {
        try {
            $hash = password_hash($p1, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (nom, prenom, email, telephone, password)
    VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nom, $prenom, $email, $telephone, $hash]);


            // redirige vers la page de connexion
            header("Location: Connexion.php?registered=1");
            exit;
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
    <title>Inscription – Gusteau’s</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; font-family:'Georgia', serif; }
        body {
            background: url("Resto.png") no-repeat center center fixed;
            background-size: cover;
            display:flex; align-items:center; justify-content:center;
            height:100vh;
        }
        .form-container {
            background:rgba(255,255,255,0.95);
            padding:30px;
            border-radius:12px;
            box-shadow:0 0 15px rgba(0,0,0,0.2);
            width:100%; max-width:340px;
        }
        .form-container h2 {
            text-align:center; color:#800000; margin-bottom:25px;
        }
        .errors { list-style:none; margin-bottom:20px; }
        .errors li { color:#a00; margin-bottom:5px; }
        label {
            display:block; margin-top:12px;
            color:#333; font-weight:bold; font-size:15px;
        }
        input {
            width:100%; padding:8px; margin-top:6px;
            border:1px solid #ccc; border-radius:8px;
            font-size:15px;
        }
        button {
            background-color:#800000; color:white;
            padding:10px; margin-top:22px; width:100%;
            border:none; border-radius:25px;
            font-size:15px; cursor:pointer;
            transition:background-color .3s ease;
        }
        button:hover { background-color:#a00d0d; }
        .login-link, .back-link {
            display:block; text-align:center; margin-top:12px;
            color:#800000; text-decoration:none; font-size:14px;
        }
        .login-link:hover, .back-link:hover { text-decoration:underline; }
        @media(max-width:500px){
            .form-container{ padding:20px; }
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
    <h2>Créer un compte</h2>

    <?php if ($errors): ?>
        <ul class="errors">
            <?php foreach($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="post" onsubmit="return validateForm()">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required
               value="<?= htmlspecialchars($nom) ?>">

        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" required
               value="<?= htmlspecialchars($prenom) ?>">

        <label for="email">Adresse e-mail</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($email) ?>">

        <label for="telephone">Numéro de téléphone</label>
        <input type="tel" id="telephone" name="telephone" pattern="[0-9]{10}" required
               value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm-password">Confirmer le mot de passe</label>
        <input type="password" id="confirm-password" name="confirm-password" required>

        <button type="submit">S'inscrire</button>
    </form>

    <a href="Connexion.php" class="login-link">← Déjà un compte ? Se connecter</a>
    <a href="Accueil.php"   class="back-link">← Retour à l'accueil</a>
</div>
<script>
    function validateForm() {
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('confirm-password').value;
        if (p1.length < 6) {
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return false;
        }
        if (p1 !== p2) {
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
        return true; // tout est OK, le formulaire peut être soumis
    }
</script>

</body>
</html>
