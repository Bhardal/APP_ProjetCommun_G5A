<?php
require 'config.php';

// si pas connecté, redirige vers connexion
if (empty($_SESSION['user_id'])) {
    header('Location: Connexion.php');
    exit;
}

// récupère quelques infos utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email, created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil – Gusteau’s</title>
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
<div class="profile">
    <h1>Bienvenue <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?> !</h1>
    <p><strong>E-mail :</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Inscrit le :</strong> <?= $user['created_at'] ?></p>
    <p><a href="logout.php">Déconnexion</a></p>
</div>
</body>
</html>
