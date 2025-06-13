<?php
// reset_password.php
require 'config.php';
$errors  = [];
$success = false;

// Récupère le token en GET
$token = $_GET['token'] ?? '';
if (!$token) {
    die('Token manquant');
}

// Vérifier token + expirationy
try {
    $stmt = $pdo->prepare("
    SELECT id
    FROM users
    WHERE reset_token = ?
        AND reset_expires > NOW()
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        die('Lien invalide ou expiré.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $p1 = $_POST['password'] ?? '';
        $p2 = $_POST['confirm_password'] ?? '';
        if (strlen($p1) < 6) {
            $errors[] = "Le mot de passe doit faire au moins 6 caractères.";
        }
        if ($p1 !== $p2) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (empty($errors)) {
            // Mettre à jour mot de passe + nettoyer token
            $hash = password_hash($p1, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
            UPDATE users
            SET password = ?, reset_token = NULL, reset_expires = NULL
            WHERE id = ?
            ");
            $stmt->execute([$hash, $user['id']]);
            $success = true;
        }
    }

} catch(PDOException $e){
    echo "Erreur de connexion à la base de donnée : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser mot de passe – Gusteau’s</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin:0; padding:0; font-family:'Georgia', serif;}
        body {
            background: url("Resto.png") no-repeat center center fixed;
            background-size: cover;
            display: flex; justify-content: center; align-items: center;
            height: 100vh; color: #800000;
        }
        .form-container {
            background-color: rgba(255,255,255,0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            width: 100%; max-width: 400px;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .errors { list-style: none; margin-bottom: 20px; color: #a00; }
        .errors li { margin-bottom: 5px; }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            background-color: #800000;
            color: #fff;
            padding: 12px;
            margin-top: 25px;
            width: 100%;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #a00d0d;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #800000;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            .form-container { padding: 25px; }
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Réinitialiser votre mot de passe</h2>

    <?php if ($success): ?>
        <p>Votre mot de passe a bien été réinitialisé.
            <a href="Connexion.php">Connectez-vous</a>.
        </p>
    <?php else: ?>
        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirmer mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Mettre à jour</button>
        </form>
    <?php endif; ?>

    <a href="Connexion.php" class="back-link">← Retour à la connexion</a>
</div>
</body>
</html>
