<?php
// mot-de-passe-oublie.php
require 'config.php';

// Charge PHPMailer manuellement
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors[] = "Adresse e-mail invalide.";
    } else {
        // Vérifier que l'utilisateur existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            $errors[] = "Aucun compte trouvé pour cette adresse.";
        } else {
            // Générer token + expiration (1h)
            $token   = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600);
            $stmt = $pdo->prepare("
              UPDATE users
              SET reset_token = ?, reset_expires = ?
              WHERE id = ?
            ");
            $stmt->execute([$token, $expires, $user['id']]);

            // Préparer le lien de réinitialisation
            $resetLink = "http://"
                . $_SERVER['HTTP_HOST']
                . dirname($_SERVER['REQUEST_URI'])
                . "/reset_password.php?token=$token";

            // Envoi avec PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Paramètres SMTP (ex. Gmail)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'huwilliam2601@gmail.com';
                $mail->Password   = 'ahptvtpcwiyvzbqw'; // mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Expéditeur et destinataire
                $mail->setFrom('huwilliam2601@gmail.com', 'Gusteau Restaurant');
                $mail->addAddress($email);

                // Corps HTML et CSS inline
                $mail->isHTML(true);

                $css = <<<CSS
body { font-family: Georgia, serif; background-color: #f9f9f9; color: #333; padding: 20px; }
.container { background: #fff; border-radius: 8px; padding: 20px; max-width: 600px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h1 { color: #800000; font-size: 22px; margin-bottom: 10px; }
p { font-size: 16px; line-height: 1.4; }
a.button { display: inline-block; background-color: #800000; color: #fff !important; text-decoration: none; padding: 10px 20px; border-radius: 25px; margin-top: 15px; }
CSS;

                $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>$css</style>
</head>
<body>
  <div class="container">
    <h1>Reinitialisation de votre mot de passe</h1>
    <p>Bonjour,</p>
    <p>Vous avez demandé à reinitialiser votre mot de passe pour <strong>Gusteau’s Restaurant</strong>.
    Cliquez sur le bouton ci-dessous pour en choisir un nouveau (valide 1 heure) :</p>
    <p style="text-align:center;">
      <a href="$resetLink" class="button">Reinitialiser mon mot de passe</a>
    </p>
    <p>Si vous n’avez pas demandé cette reinitialisation, ignorez simplement ce message.</p>
    <p>— L’équipe Gusteau’s Restaurant</p>
  </div>
</body>
</html>
HTML;

                $mail->Subject = 'Reinitialisation de votre mot de passe';
                $mail->Body    = $htmlBody;

                // Texte brut de secours
                $mail->AltBody = "Bonjour,\n\n"
                    . "Pour réinitialiser votre mot de passe, ouvrez ce lien :\n"
                    . "$resetLink\n\n"
                    . "Si vous n'avez pas demandé cette reinitialisation, ignorez ce message.";

                $mail->send();
                $success = true;
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
                $errors[] = "Impossible d'envoyer l'e-mail de reinitialisation.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié – Gusteau’s</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* CSS de votre formulaire */
        * { box-sizing: border-box; margin:0; padding:0; font-family:'Georgia',serif; }
        body {
            background: url("Resto.png") center/cover no-repeat fixed;
            height:100vh; color:#800000;
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
            margin-bottom: 15%;
            background: rgba(255,255,255,0.95);
            padding:40px; border-radius:15px;
            box-shadow:0 0 15px rgba(0,0,0,0.2);
            width:100%; max-width:400px;
        }
        .form-container h2 { text-align:center; margin-bottom:20px; }
        .errors { list-style:none; margin-bottom:20px; color:#a00; }
        .errors li { margin-bottom:5px; }
        .form-container label { display:block; margin-top:15px; font-weight:bold; }
        .form-container input { width:100%; padding:10px; margin-top:8px; border:1px solid #ccc; border-radius:8px; }
        .form-container button {
            background:#800000; color:#fff; padding:12px; margin-top:25px;
            width:100%; border:none; border-radius:25px; cursor:pointer;
        }
        .form-container button:hover { background:#a00d0d; }
        .back-link {
            display:block; text-align:center; margin-top:15px;
            color:#800000; text-decoration:none;
        }
        .back-link:hover { text-decoration:underline; }
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
    <h2>Mot de passe oublié</h2>
    <?php if ($success): ?>
        <p>Un lien de reinitialisation vous a été envoyé par e-mail.</p>
    <?php else: ?>
        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <form method="post">
            <label for="email">Votre adresse e-mail</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Envoyer le lien</button>
        </form>
    <?php endif; ?>
    <a href="Connexion.php" class="back-link">← Retour à la connexion</a>
</div>
<footer>
    &copy; 2025 Gusteau’s Restaurant — Tous droits réservés | Version 1.0<br>
    🔐 Site sécurisé — ♿ Accessible à tous les profils
</footer>
</body>
</html>
