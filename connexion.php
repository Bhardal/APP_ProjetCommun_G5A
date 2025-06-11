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
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pass, $user['password'])) {
            $errors[] = "Identifiants incorrects.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: Profil.php");
            exit;
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
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Georgia', serif; }
        body {
            background: url("Resto.png") no-repeat center center fixed;
            background-size: cover;
            display: flex; align-items: center; justify-content: center;
            height: 100vh;
        }
        .form-container {
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
        label {
            display: block; margin-top: 15px;
            color: #333; font-weight: bold;
        }
        input {
            width: 100%; padding: 10px; margin-top: 8px;
            border: 1px solid #ccc; border-radius: 8px;
            font-size: 16px;
        }
        button {
            background-color: #800000; color: #fff;
            padding: 12px; margin-top: 25px; width: 100%;
            border: none; border-radius: 25px;
            font-size: 16px; cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover { background-color: #a00d0d; }
        .forgot-link, .register-link, .back-link {
            display: block; text-align: center; margin-top: 15px;
            color: #800000; text-decoration: none; font-size: 15px;
        }
        .forgot-link:hover, .register-link:hover, .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 500px) {
            .form-container { padding: 25px; }
        }
    </style>
</head>
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
        <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= htmlspecialchars($email) ?>"
        >

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <a href="mot-de-passe-oublie.html" class="forgot-link">Mot de passe oublié ?</a>
    <a href="Inscription.php" class="register-link">Pas encore de compte ? Créer un compte</a>
    <a href="Accueil.php" class="back-link">← Retour à l'accueil</a>
</div>
</body>
</html>
