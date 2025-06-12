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
            display:flex; justify-content:center; align-items:center;
            height:100vh; color:#800000;
        }
        .form-container {
            background: rgba(255,255,255,0.95);
            padding:40px; border-radius:15px;
            box-shadow:0 0 15px rgba(0,0,0,0.2);
            width:100%; max-width:400px;
        }
        h2 { text-align:center; margin-bottom:20px; }
        .errors { list-style:none; margin-bottom:20px; color:#a00; }
        .errors li { margin-bottom:5px; }
        label { display:block; margin-top:15px; font-weight:bold; }
        input { width:100%; padding:10px; margin-top:8px; border:1px solid #ccc; border-radius:8px; }
        button {
            background:#800000; color:#fff; padding:12px; margin-top:25px;
            width:100%; border:none; border-radius:25px; cursor:pointer;
        }
        button:hover { background:#a00d0d; }
        .back-link {
            display:block; text-align:center; margin-top:15px;
            color:#800000; text-decoration:none;
        }
        .back-link:hover { text-decoration:underline; }
    </style>
</head>
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
</body>
</html>
